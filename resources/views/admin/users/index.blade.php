<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Admin"
            title="Users"
            description="Manage access, roles, and account status. Users are deactivated instead of permanently deleted."
        >
            <x-slot name="actions">
                <a href="{{ route('admin.users.create') }}" class="app-button-primary">Create User</a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-flash-messages />

        <form method="GET" action="{{ route('admin.users.index') }}" class="app-card p-4 sm:p-5">
            <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Filter users</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $users->total() }} {{ Str::plural('account', $users->total()) }} found.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-primary-button>Apply</x-primary-button>
                    <a href="{{ route('admin.users.index') }}" class="app-button-secondary">Reset</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" type="search" class="mt-1" value="{{ $filters['q'] }}" placeholder="Name or email" />
                </div>
                <div>
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" class="app-input mt-1">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected($filters['role'] === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="active" value="Active" />
                    <select id="active" name="active" class="app-input mt-1">
                        <option value="">All active states</option>
                        <option value="1" @selected($filters['active'] === '1')>Active</option>
                        <option value="0" @selected($filters['active'] === '0')>Inactive</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="app-card overflow-hidden">
            <div class="hidden overflow-x-auto md:block">
                <table class="app-table">
                    <thead class="app-table-head">
                        <tr>
                            <th class="app-table-th" scope="col">User</th>
                            <th class="app-table-th" scope="col">Email</th>
                            <th class="app-table-th" scope="col">Role</th>
                            <th class="app-table-th" scope="col">Phone</th>
                            <th class="app-table-th" scope="col">Status</th>
                            <th class="app-table-th" scope="col">Created</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($users as $managedUser)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :user="$managedUser" size="sm" />
                                        <span class="text-sm font-semibold text-slate-950">{{ $managedUser->name }}</span>
                                    </div>
                                </td>
                                <td class="break-all px-4 py-4 text-sm text-slate-700">{{ $managedUser->email }}</td>
                                <td class="px-4 py-4 text-sm text-slate-700">{{ ucfirst($managedUser->role) }}</td>
                                <td class="app-table-td">{{ $managedUser->phone ?: 'Not specified' }}</td>
                                <td class="px-4 py-4">
                                    @if ($managedUser->is_active)
                                        <span class="app-badge bg-emerald-50 text-emerald-700 ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Active account</span>
                                    @else
                                        <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300"><span class="h-1.5 w-1.5 rounded-full bg-slate-400" aria-hidden="true"></span>Inactive account</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">{{ $managedUser->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-4 text-right text-sm">
                                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="app-link">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6">
                                    <x-empty-state title="No users found." description="Try changing or resetting the filters." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse ($users as $managedUser)
                    <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <x-avatar :user="$managedUser" />
                            <div class="min-w-0 flex-1">
                                <p class="break-words text-sm font-semibold text-slate-950">{{ $managedUser->name }}</p>
                                <p class="mt-1 break-all text-xs text-slate-500">{{ $managedUser->email }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="app-badge bg-indigo-50 text-indigo-700 ring-indigo-200"><span class="h-1.5 w-1.5 rounded-full bg-indigo-500" aria-hidden="true"></span>{{ ucfirst($managedUser->role) }}</span>
                                    @if ($managedUser->is_active)
                                        <span class="app-badge bg-emerald-50 text-emerald-700 ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Active</span>
                                    @else
                                        <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300"><span class="h-1.5 w-1.5 rounded-full bg-slate-400" aria-hidden="true"></span>Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.edit', $managedUser) }}" class="app-button-secondary">Edit User</a>
                        </div>
                    </article>
                @empty
                    <x-empty-state title="No users found." description="Try changing or resetting the filters." />
                @endforelse
            </div>
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
