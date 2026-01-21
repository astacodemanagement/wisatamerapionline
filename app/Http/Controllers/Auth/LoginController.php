<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\LogHelper;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Override the authenticated method to check if user is active or semi-active
     */
    protected function authenticated(Request $request, $user)
    {
        if (!in_array($user->status, ['active', 'semi-active'])) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => ['Akun Anda belum diaktifkan. Silahkan hubungi administrator untuk informasi lebih lanjut.'],
            ]);
        }

        // Simpan log histori login berhasil
        LogHelper::logAction(
            'users',
            $user->id,
            'Login Success',
            null,
            [
                'email' => $request->input('email'),
                'password' => '[disembunyikan]',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        // Simpan log histori login gagal
        LogHelper::logAction(
            'users',
            null,
            'Login Failed',
            [
                'email' => $request->input('email'),
                'password' => '[disembunyikan]',
            ],
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Override credentials to use username instead of email
     */
    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'username';

        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
}