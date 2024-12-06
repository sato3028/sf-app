<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomAttribute;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chat;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        // フィルタリングロジック
        if ($request->filled('host_rank')) {
            $hostRankRange = $request->input('host_rank');
            if (is_array($hostRankRange) && count($hostRankRange) === 2) {
                $query->whereBetween('host_rank', [$hostRankRange[0], $hostRankRange[1]]);
            }
        }

        if ($request->filled('rank_range')) {
            $rankRange = $request->input('rank_range');
            if (is_array($rankRange) && count($rankRange) === 2) {
                $query->whereBetween('min_rank', [$rankRange[0], $rankRange[1]])
                      ->whereBetween('max_rank', [$rankRange[0], $rankRange[1]]);
            }
        }

        // ホストキャラクターのフィルタリング
        if ($request->filled('host_characters')) {
            $hostCharacters = $request->input('host_characters');
            Log::info('受信したhost_characters:', $hostCharacters);

            if (!in_array('ALL', $hostCharacters, true)) {
                $query->where(function ($q) use ($hostCharacters) {
                    foreach ($hostCharacters as $name) {
                        $q->orWhereRaw("JSON_CONTAINS(host_characters, JSON_OBJECT('name', ?))", [$name]);
                    }
                });
            }
        }

        // 募集キャラクターのフィルタリング
        if ($request->filled('requested_characters')) {
            $requestedCharacters = $request->input('requested_characters');
            Log::info('受信したrequested_characters:', $requestedCharacters);

            if (!in_array('ALL', $requestedCharacters, true)) {
                $query->where(function ($q) use ($requestedCharacters) {
                    foreach ($requestedCharacters as $name) {
                        $q->orWhereRaw("JSON_CONTAINS(requested_characters, JSON_OBJECT('name', ?))", [$name]);
                    }
                });
            }
        }

        // カテゴリー（attributes）のフィルタリング
        if ($request->filled('attributes')) {
            $categories = array_map('intval', $request->input('attributes'));
            Log::info('受信したカテゴリー:', $categories);

            if (!empty($categories)) {
                $query->whereHas('attributes', function ($q) use ($categories) {
                    $q->whereIn('room_attributes.id', $categories);
                });
            }
        }

        // 募集MR範囲のフィルタリング
        if ($request->filled('requested_mr_range')) {
            $requestedMrRange = $request->input('requested_mr_range');
            if (is_array($requestedMrRange) && count($requestedMrRange) === 2) {
                $query->whereBetween('min_mr', [$requestedMrRange[0], $requestedMrRange[1]])
                      ->whereBetween('max_mr', [$requestedMrRange[0], $requestedMrRange[1]]);
            }
        }

        // 結果を取得
        $rooms = $query->with('attributes')->get();

        // デバッグ用ログ
        Log::info('受信したフィルター', $request->all());
        Log::info('生成されたSQL', [
            'query' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        // attributesを取得
        $attributes = RoomAttribute::all();

        // 現在のユーザー情報
        $user = auth()->user();
        $needsNameSetup = $user ? empty($user->name) : false;

        // レンダリング
        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
            'attributes' => $attributes,
            'needsNameSetup' => $needsNameSetup,
            'user' => $user,
        ]);
    }


    public function create()
    {
        $attributes = RoomAttribute::all();
        $characters = [
            ['name' => 'RYU', 'image' => '/images/ryu_icon.jpg'],
            ['name' => 'LUKE', 'image' => '/images/luke_icon.jpg'],
            ['name' => 'JAMIE', 'image' => '/images/jamie_icon.jpg'],
            ['name' => 'CHUNLI', 'image' => '/images/chunli_icon.jpg'],
            ['name' => 'GUILE', 'image' => '/images/guile_icon.jpg'],
            ['name' => 'KIMBERLY', 'image' => '/images/kimberly_icon.jpg'],
            ['name' => 'JURI', 'image' => '/images/juri_icon.jpg'],
            ['name' => 'KEN', 'image' => '/images/ken_icon.jpg'],
            ['name' => 'BLANKA', 'image' => '/images/blanka_icon.jpg'],
            ['name' => 'DHALSIM', 'image' => '/images/dhalsim_icon.jpg'],
            ['name' => 'HONDA', 'image' => '/images/honda_icon.jpg'],
            ['name' => 'DEEJAY', 'image' => '/images/deejay_icon.jpg'],
            ['name' => 'MANON', 'image' => '/images/manon_icon.jpg'],
            ['name' => 'MARISA', 'image' => '/images/marisa_icon.jpg'],
            ['name' => 'JP', 'image' => '/images/jp_icon.jpg'],
            ['name' => 'ZANGIEF', 'image' => '/images/zangief_icon.jpg'],
            ['name' => 'LILY', 'image' => '/images/lily_icon.jpg'],
            ['name' => 'CAMMY', 'image' => '/images/cammy_icon.jpg'],
            ['name' => 'RASHID', 'image' => '/images/rashid_icon.jpg'],
            ['name' => 'AKI', 'image' => '/images/aki_icon.jpg'],
            ['name' => 'ED', 'image' => '/images/ed_icon.jpg'],
            ['name' => 'GOUKI', 'image' => '/images/gouki_icon.jpg'],
            ['name' => 'VEGA', 'image' => '/images/vega_icon.jpg'],
            ['name' => 'TERRY', 'image' => '/images/terry_icon.jpg'],
            ['name' => 'ALL', 'image' => '/images/all_icon.jpg'],
        ];

        return Inertia::render('Rooms/Create', [
            'attributes' => $attributes,
            'characters' => $characters,
            'user' => auth()->user()
        ]);
    }


    public function confirm(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'host_characters' => 'required|array|min:1',
            'host_characters.*.name' => 'required|string',
            'host_characters.*.type' => 'required|string',
            'requested_characters' => 'required|array|min:1',
            'requested_characters.*.name' => 'required|string',
            'requested_characters.*.type' => 'required|string',
            'host_rank' => 'required|integer|min:1|max:25000',
            'rank_range' => 'required|array',
            'rank_range.0' => 'required|integer|min:1|max:25000',
            'rank_range.1' => 'required|integer|min:1|max:25000',
            'host_mr' => 'nullable|integer|min:1000|max:2500',
            'mr_range' => 'nullable|array',
            'mr_range.0' => 'nullable|integer|min:1000|max:2500',
            'mr_range.1' => 'nullable|integer|min:1000|max:2500',
            'attributes' => 'nullable|array',
        ]);

        $data['host_username'] = auth()->user()->name;

        return Inertia::render('Rooms/Confirm', [
            'formData' => $data,
            'attributes' => RoomAttribute::all(),
        ]);
    }






    public function store(Request $request)
    {
        // バリデーションの修正
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'host_characters' => 'required|array',
            'requested_characters' => 'required|array', // 募集キャラクターを必須に
            'rank_range' => 'required|array',
            'rank_range.0' => 'required|integer|min:1|max:25000',
            'rank_range.1' => 'required|integer|min:1|max:25000',
            'host_rank' => 'required|integer|min:1|max:25000',
            'host_mr' => 'nullable|integer|min:1000|max:2500',
            'mr_range' => 'nullable|array',
            'mr_range.0' => 'nullable|integer|min:1000|max:2500',
            'mr_range.1' => 'nullable|integer|min:1000|max:2500',
            'attributes' => 'nullable|array',
        ]);

        try {
            // ルームを作成
            $room = new Room();
            $room->host_id = auth()->id();
            $room->title = $validated['title'];
            $room->host_username = auth()->user()->name;
            $room->host_characters = json_encode(
                array_map('array_map', ['strval'], $validated['host_characters'])
            );
            $room->requested_characters = json_encode($validated['requested_characters']); // 募集キャラクターを保存
            $room->host_rank = $validated['host_rank'];
            $room->host_mr = $validated['host_mr'] ?? null;
            $room->min_rank = $validated['rank_range'][0];
            $room->max_rank = $validated['rank_range'][1];
            $room->min_mr = $validated['mr_range'][0] ?? null;
            $room->max_mr = $validated['mr_range'][1] ?? null;
            $room->status = 'open';
            $room->save();

            // カテゴリーの保存
            if (!empty($validated['attributes'])) {
                $room->attributes()->attach($validated['attributes']);
            }

            session(['current_room_id' => $room->id]);

            Log::info('現在のルームIDをセッションに保存:', ['current_room_id' => session('current_room_id')]);

            // Inertiaでのリダイレクト
            return Inertia::location(route('rooms.show', ['room' => $room->id]));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Room creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($roomId)
    {
        $room = Room::with(['participants.user', 'chats.user', 'attributes'])->findOrFail($roomId);
        return Inertia::render('Rooms/Show', [
            'room' => $room,
            'alert' => session('alert'), // セッションからアラートを渡す
        ]);
    }

    public function join($roomId)
    {
        $room = Room::findOrFail($roomId);
        if ($room->participants()->count() < 2) {
            $room->participants()->create(['user_id' => auth()->id()]);
        }

        session(['current_room_id' => $roomId]);

        return redirect()->route('rooms.show', $roomId);
    }

    public function destroy($roomId)
    {
        $room = Room::findOrFail($roomId);

        // ホストのみ削除可能
        if (auth()->id() === $room->host_id) {
            $room->delete();

            session()->forget('current_room_id');

            return redirect()->route('rooms.index')->with('message', 'ルームを解散しました');
        }

        return back()->with('error', 'ルームの解散はホストのみ可能です');
    }

    public function leave($roomId)
    {
        $participant = Participant::where('room_id', $roomId)->where('user_id', auth()->id())->first();

        if ($participant) {
            $participant->delete();

            session()->forget('current_room_id');

            return redirect()->route('rooms.index')->with('message', 'ルームを退出しました');
        }

        return back()->with('error', 'ルーム退出に失敗しました');
    }
}
