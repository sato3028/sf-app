<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class CheckRoomSession
{
    public function handle(Request $request, Closure $next)
    {
        // ユーザーがログインしていない場合は処理を続行
        if (!auth()->check()) {
            return $next($request);
        }

        // セッションにルームIDが保存されている場合
        if (session()->has('current_room_id')) {
            $currentRoomId = session('current_room_id');

            // 現在のリクエストがルームURLでない場合リダイレクト
            if (!$request->is("rooms/{$currentRoomId}")) {
                session()->flash('alert', '別のページに移動する際はルームを解散または退出してください。');
                Log::info('Redirecting to room with alert:', ['alert' => session('alert')]);
                return redirect()->route('rooms.show', $currentRoomId);
            }
        }

        return $next($request);
    }
}
