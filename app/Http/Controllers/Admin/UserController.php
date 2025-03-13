<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    use AuthorizesRequests;

    /* **************************************** Constructor **************************************** */
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /* **************************************** Public **************************************** */
    public function activate(User $user) : RedirectResponse
    {
        $this->authorize('update', $user);

        $user->status = UserStatus::ACTIVE;
        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', __('admin.buttons.confirm'));
    }

    public function block(User $user) : RedirectResponse
    {
        $this->authorize('update', $user);

        $user->status = UserStatus::BLOCKED;
        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', __('admin.buttons.block'));
    }

    public function destroy(User $user) : RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->email === config('app.admin_email')) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('admin.user.cannot_delete_admin'));
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('admin.buttons.delete'));
    }

    public function index() : View
    {
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(config('app.users_per_page'));

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user) : View
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }
}
