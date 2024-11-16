<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialiteUser = Socialite::driver('google')->user();
            $email = $socialiteUser->email;

            // 更新または作成するフィールドを指定
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $socialiteUser->name,
                    'google_id' => $socialiteUser->id,
                    'avatar' => $socialiteUser->avatar, // Google のアバター画像 URL など
                    // 他に必要なフィールドがあれば追加
                ]
            );

            Auth::login($user);

            return redirect()->intended('/rooms');
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }
    }
}
