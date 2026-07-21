<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase text-teal-700">{{ $ticket->ticket_code }}</p>
            <h1 class="text-2xl font-semibold text-stone-950">Edit Ticket</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    @if (Auth::user()->isAdmin())
                        <div class="rounded-md bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-inset ring-amber-200">
                            Admin edits in Milestone 2 are limited to category and priority.
                        </div>
                    @else
                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title', $ticket->title) }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('description', $ticket->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    @endif

                    @if ($ticket->category->trashed())
                        <div class="rounded-md bg-stone-50 px-4 py-3 text-sm text-stone-700 ring-1 ring-inset ring-stone-200">
                            Current category "{{ $ticket->category->name }}" is archived. Select an active category to save changes.
                        </div>
                    @endif

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="ticket_category_id" value="Category" />
                            <select id="ticket_category_id" name="ticket_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('ticket_category_id', $ticket->ticket_category_id) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select priority</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->value }}" @selected(old('priority', $ticket->priority->value) === $priority->value)>{{ $priority->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>

                    @unless (Auth::user()->isAdmin())
                        <div>
                            <x-input-label for="location" value="Location" />
                            <x-text-input id="location" name="location" class="mt-1 block w-full" value="{{ old('location', $ticket->location) }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>
                    @endunless

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-medium text-stone-600 hover:text-stone-950">Cancel</a>
                        <x-primary-button>Save Changes</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
