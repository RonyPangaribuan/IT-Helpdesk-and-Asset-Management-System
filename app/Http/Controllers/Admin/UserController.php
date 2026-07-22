<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->latest();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $role = $request->query('role');

        if (is_string($role) && in_array($role, User::ROLES, true)) {
            $query->where('role', $role);
        }

        $active = $request->query('active');

        if (in_array($active, ['0', '1'], true)) {
            $query->where('is_active', $active === '1');
        }

        return view('admin.users.index', [
            'users' => $query->paginate(10)->withQueryString(),
            'roles' => User::ROLES,
            'filters' => [
                'q' => $search,
                'role' => in_array($role, User::ROLES, true) ? $role : null,
                'active' => in_array($active, ['0', '1'], true) ? $active : null,
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', [
            'roles' => User::ROLES,
        ]);
    }

    public function store(StoreUserRequest $request, UserManagementService $users): RedirectResponse
    {
        $user = $users->createUser($request->userData());

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => User::ROLES,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UserManagementService $users): RedirectResponse
    {
        $users->updateUser($request->user(), $user, $request->userData($user));

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User updated.');
    }
}
