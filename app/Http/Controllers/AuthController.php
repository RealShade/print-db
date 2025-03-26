<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* **************************************** Public **************************************** */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->credentials())) {
            if (Auth::user()->status !== UserStatus::ACTIVE) {
                Auth::logout();

                return back()->withErrors(['email' => __('auth.account_inactive')]);
            }

            return redirect()->route('home');
        }

        return back()->withErrors(['email' => trans('auth.invalid_credentials')]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function register(RegisterRequest $request)
    {
        $userData             = $request->userData();
        $userData['password'] = Hash::make($userData['password']);
        $userData['status']   = config('app.free_registration') ? UserStatus::ACTIVE : UserStatus::NEW;

        $user = User::create($userData);
        $user->assignRole(UserRole::USER);

        return view('auth.register-success');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }
}
