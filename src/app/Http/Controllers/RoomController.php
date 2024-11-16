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

        // フィルタリングロジック（前述の通り）
        if ($request->filled('host_rank')) {
            $hostRankRange = $request->input('host_rank');
            if (is_array($hostRankRange) && count($hostRankRange) === 2) {
                $query->whereBetween('host_rank', [$hostRankRange[0], $hostRankRange[1]]);
            }
        }

        if ($request->filled('host_characters')) {
            $hostCharacters = $request->input('host_characters');
            if (is_array($hostCharacters) && !empty($hostCharacters)) {
                $query->where(function($q) use ($hostCharacters) {
                    foreach ($hostCharacters as $character) {
                        $q->orWhereJsonContains('host_characters', $character);
                    }
                });
            }
        }

        if ($request->filled('categories')) {
            $categories = $request->input('categories');
            if (is_array($categories) && !empty($categories)) {
                $query->whereHas('attributes', function($q) use ($categories) {
                    $q->whereIn('room_attributes.id', $categories);
                });
            }
        }

        // 結果を取得
        $rooms = $query->with('attributes')->get();

        // attributesを取得
        $attributes = \App\Models\RoomAttribute::all();

        // ビューに渡す
        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
            'attributes' => $attributes,  // ここでattributesを渡す
        ]);
    }




    public function create()
    {
        $attributes = RoomAttribute::all();
        $characters = [
            'リュウ', 'ルーク', 'ジェイミー', '春麗', 'ガイル', 'キンバリー', 'ジュリ', 'ケン',
            'ブランカ', 'ダルシム', 'エドモンド本田', 'DJ', 'マノン', 'マリーザ', 'JP', 'ザンギエフ',
            'リリィ', 'キャミィ', 'ラシード', 'アキ', 'エド', '豪鬼'
        ];
        return Inertia::render('Rooms/Create', [
            'attributes' => $attributes,
            'characters' => $characters
        ]);
    }

    public function confirm(Request $request)
    {
        $data = $request->all();

        // attributes がある場合、カテゴリーの名前を取得
        if (!empty($data['attributes'])) {
            $attributeNames = RoomAttribute::whereIn('id', $data['attributes'])->pluck('name');
            $data['attributeNames'] = $attributeNames; // 新しいキーとして追加
        }

        return Inertia::render('Rooms/Confirm', [
            'formData' => $data,
        ]);
    }



    public function store(Request $request)
    {
        // バリデーションの修正
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'host_username' => 'required|string|max:255',
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
            $room->host_username = $validated['host_username'];
            $room->host_characters = json_encode($validated['host_characters']);
            $room->requested_characters = json_encode($validated['requested_characters']); // 募集キャラクターを必ず保存
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
