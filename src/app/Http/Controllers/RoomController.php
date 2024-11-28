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

        if ($request->filled('host_characters')) {
            $hostCharacters = $request->input('host_characters');
            if (is_array($hostCharacters) && !empty($hostCharacters)) {
                $query->where(function ($q) use ($hostCharacters) {
                    foreach ($hostCharacters as $character) {
                        $q->orWhereJsonContains('host_characters', $character);
                    }
                });
            }
        }

        if ($request->filled('categories')) {
            $categories = $request->input('categories');
            if (is_array($categories) && !empty($categories)) {
                $query->whereHas('attributes', function ($q) use ($categories) {
                    $q->whereIn('room_attributes.id', $categories);
                });
            }
        }

        // 結果を取得
        $rooms = $query->with('attributes')->get();

        // attributesを取得
        $attributes = \App\Models\RoomAttribute::all();

        // 現在のユーザーを取得
        $user = auth()->user();

        // ユーザー名が未設定の場合のフラグ
        $needsNameSetup = $user ? empty($user->name) : false;

        // ビューに渡す
        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
            'attributes' => $attributes,
            'needsNameSetup' => $needsNameSetup, // ユーザー名変更モーダル用のフラグ
            'user' => $user, // ログイン中のユーザー情報
        ]);
    }

    public function create()
    {
        $attributes = RoomAttribute::all();
        $characters = [
            ['name' => 'リュウ', 'image' => '/images/ryu_icon.jpg'],
            ['name' => 'ルーク', 'image' => '/images/luke_icon.jpg'],
            ['name' => 'ジェイミー', 'image' => '/images/jamie_icon.jpg'],
            ['name' => '春麗', 'image' => '/images/chunli_icon.jpg'],
            ['name' => 'ガイル', 'image' => '/images/guile_icon.jpg'],
            ['name' => 'キンバリー', 'image' => '/images/kimberly_icon.jpg'],
            ['name' => 'ジュリ', 'image' => '/images/juri_icon.jpg'],
            ['name' => 'ケン', 'image' => '/images/ken_icon.jpg'],
            ['name' => 'ブランカ', 'image' => '/images/blanka_icon.jpg'],
            ['name' => 'ダルシム', 'image' => '/images/dhalsim_icon.jpg'],
            ['name' => 'エドモンド本田', 'image' => '/images/honda_icon.jpg'],
            ['name' => 'DJ', 'image' => '/images/deejay_icon.jpg'],
            ['name' => 'マノン', 'image' => '/images/manon_icon.jpg'],
            ['name' => 'マリーザ', 'image' => '/images/marisa_icon.jpg'],
            ['name' => 'JP', 'image' => '/images/jp_icon.jpg'],
            ['name' => 'ザンギエフ', 'image' => '/images/zangief_icon.jpg'],
            ['name' => 'リリィ', 'image' => '/images/lily_icon.jpg'],
            ['name' => 'キャミィ', 'image' => '/images/cammy_icon.jpg'],
            ['name' => 'ラシード', 'image' => '/images/rashid_icon.jpg'],
            ['name' => 'アキ', 'image' => '/images/aki_icon.jpg'],
            ['name' => 'エド', 'image' => '/images/ed_icon.jpg'],
            ['name' => '豪鬼', 'image' => '/images/gouki_icon.jpg'],
            ['name' => 'ベガ', 'image' => '/images/vega_icon.jpg'],
            ['name' => 'テリー', 'image' => '/images/terry_icon.jpg'],
            ['name' => 'なんでも', 'image' => '/images/all_icon.jpg'],
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
            'requested_characters' => 'required|array|min:1',
            'requested_characters.*.name' => 'required|string',
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
            $room->host_characters = json_encode($validated['host_characters']);
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

            // Inertiaでのリダイレクト
            return Inertia::location(route('rooms.show', ['room' => $room->id]));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Room creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($roomId)
    {
        $room = Room::with(['participants.user', 'chats.user', 'attributes'])->findOrFail($roomId);
        return Inertia::render('Rooms/Show', ['room' => $room]);
    }

    public function join($roomId)
    {
        $room = Room::findOrFail($roomId);
        if ($room->participants()->count() < 2) {
            $room->participants()->create(['user_id' => auth()->id()]);
        }

        return redirect()->route('rooms.show', $roomId);
    }

    public function destroy($roomId)
    {
        $room = Room::findOrFail($roomId);

        // ホストのみ削除可能
        if (auth()->id() === $room->host_id) {
            $room->delete();
            return redirect()->route('rooms.index')->with('message', 'ルームを解散しました');
        }

        return back()->with('error', 'ルームの解散はホストのみ可能です');
    }

    public function leave($roomId)
    {
        $participant = Participant::where('room_id', $roomId)->where('user_id', auth()->id())->first();

        if ($participant) {
            $participant->delete();
            return redirect()->route('rooms.index')->with('message', 'ルームを退出しました');
        }

        return back()->with('error', 'ルーム退出に失敗しました');
    }
}
