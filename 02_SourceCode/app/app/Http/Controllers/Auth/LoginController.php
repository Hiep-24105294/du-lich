<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', ['status' => $request->session()->get('status')]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'email' => mb_strtolower(trim($request->validated('email'))),
            'password' => $request->validated('password'),
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $this->audit->log('auth.login_failed', null, ['email' => $credentials['email']]);

            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        // Chống session fixation: cấp session id mới sau khi đăng nhập
        $request->session()->regenerate();
        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        $this->audit->log('auth.login', $user, [], $user);

        return redirect()->intended(localized_route('account.dashboard', ['locale' => $user->locale ?: app()->getLocale()]))
            ->with('success', __('messages.auth.logged_in', ['name' => $user->name]));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->audit->log('auth.logout', $user, [], $user);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(localized_route('home'))->with('success', __('messages.auth.logged_out'));
    }
}
