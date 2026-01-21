<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
 
            $user = User::where('email', $googleUser->getEmail())->first();
 
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'user' => Str::slug($googleUser->getName()) . rand(100, 999),
                    'password' => bcrypt(Str::random(12)),  
                    'status' => 'active'
                ]);
 
                $user->assignRole(2);  
            }
 
            Auth::login($user);

            return redirect()->route('home')->with('success', 'Berhasil login dengan Google!');
        } catch (\Exception $e) {
            return redirect('/daftar')->with('error', 'Gagal login: ' . $e->getMessage());
        }
    }
}
