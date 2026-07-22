<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">Admin</p>
                <h1 class="text-2xl font-semibold text-stone-950">Users</h1>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex w-fit items-center rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                Create User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 rounded-lg border border-stone-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <x-input-label for="q" value="Search" />
                        <x-text-input id="q" name="q" type="search" class="mt-1 block w-full" value="{{ $filters['q'] }}" placeholder="Name or email" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected($filters['role'] === $role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="active" value="Active" />
                        <select id="active" name="active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All active states</option>
                            <option value="1" @selected($filters['active'] === '1')>Active</option>
                            <option value="0" @selected($filters['active'] === '0')>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-primary-button>Apply</x-primary-button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-stone-600 hover:text-stone-950">Reset</a>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Active</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Created</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-stone-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($users as $managedUser)
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-stone-950">{{ $managedUser->name }}</td>
                                    <td class="break-all px-4 py-4 text-sm text-stone-700">{{ $managedUser->email }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ ucfirst($managedUser->role) }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $managedUser->phone ?: 'Not specified' }}</td>
                                    <td class="px-4 py-4">
                                        @if ($managedUser->is_active)
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Active account</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-300">Inactive account</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-stone-700">{{ $managedUser->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <a href="{{ route('admin.users.edit', $managedUser) }}" class="font-medium text-teal-700 hover:text-teal-900">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-stone-600">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
