<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase text-teal-700">Tickets</p>
            <h1 class="text-2xl font-semibold text-stone-950">Create Ticket</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required maxlength="150" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="ticket_category_id" value="Category" />
                            <select id="ticket_category_id" name="ticket_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('ticket_category_id') === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select priority</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->value }}" @selected(old('priority') === $priority->value)>{{ $priority->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="location" value="Location" />
                        <x-text-input id="location" name="location" class="mt-1 block w-full" value="{{ old('location') }}" required maxlength="150" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-stone-600 hover:text-stone-950">Cancel</a>
                        <x-primary-button>Create Ticket</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
