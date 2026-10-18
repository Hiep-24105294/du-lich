<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function edit(Request $request): Response
    {
        $user = $request->user()->load('role');

        return Inertia::render('Account/Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'organization' => $user->organization,
                'locale' => $user->locale,
                'theme' => $user->theme,
                'role' => $user->role?->code,
                'role_label' => $user->role ? __('enums.roles.'.$user->role->code) : null,
                'last_login_at' => $user->last_login_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated())->save();
        $this->audit->log('account.profile_updated', $user, ['fields' => array_keys($request->validated())], $user);

        // Đổi ngôn ngữ thì chuyển hướng sang URL theo ngôn ngữ mới
        return redirect(localized_route('account.profile', ['locale' => $user->locale]))
            ->with('success', __('messages.profile.updated', [], $user->locale));
    }

    public function password(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill(['password' => $request->validated('password')])->save();
        $request->session()->regenerate();
        $this->audit->log('account.password_changed', $user, [], $user);

        return back()->with('success', __('messages.profile.password_updated'));
    }
}
