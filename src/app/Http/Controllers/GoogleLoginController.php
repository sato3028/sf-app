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

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'google_id' => $socialiteUser->id,
                    'avatar' => $socialiteUser->avatar,
                ]
            );

            if (empty($user->name)) {
                $user->name = '';
                $user->save();
            }

            Auth::login($user);

            // 名前が空の場合は、フラグをセッションに設定してリダイレクト
            if (empty($user->name)) {
                return redirect()->intended('/rooms')->with('needsNameSetup', true);
            }

            return redirect()->intended('/rooms');
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }
    }

}
