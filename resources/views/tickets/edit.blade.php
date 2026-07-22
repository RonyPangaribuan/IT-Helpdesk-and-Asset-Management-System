<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :eyebrow="$ticket->ticket_code"
            title="Edit Ticket"
            description="Update editable ticket details while preserving the existing workflow and history."
        />
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <x-section-card title="Ticket details" description="Available fields depend on your role and the ticket status.">
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-8">
                @csrf
                @method('PATCH')

                @if (Auth::user()->isAdmin())
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Admin edits are limited to category, priority, and related asset.
                    </div>
                @else
                    <section class="space-y-5">
                        <div>
                            <h2 class="text-base font-semibold text-slate-950">Issue Information</h2>
                            <p class="mt-1 text-sm text-slate-600">Keep the report accurate before it is assigned.</p>
                        </div>

                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" class="mt-1" value="{{ old('title', $ticket->title) }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="7" class="app-input mt-1" required>{{ old('description', $ticket->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="location" value="Location" />
                            <x-text-input id="location" name="location" class="mt-1" value="{{ old('location', $ticket->location) }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>
                    </section>
                @endif

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Classification</h2>
                        <p class="mt-1 text-sm text-slate-600">Category and priority guide assignment and urgency.</p>
                    </div>

                    @if ($ticket->category->trashed())
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            Current category "{{ $ticket->category->name }}" is archived. Select an active category to save changes.
                        </div>
                    @endif

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="ticket_category_id" value="Category" />
                            <select id="ticket_category_id" name="ticket_category_id" class="app-input mt-1" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('ticket_category_id', $ticket->ticket_category_id) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority" class="app-input mt-1" required>
                                <option value="">Select priority</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->value }}" @selected(old('priority', $ticket->priority->value) === $priority->value)>{{ $priority->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Related Asset</h2>
                        <p class="mt-1 text-sm text-slate-600">Keep the current archived/inactive asset when preserving historical context.</p>
                    </div>

                    @if ($ticket->asset?->trashed() || ($ticket->asset && ! $ticket->asset->isSelectableForTickets()))
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            Current asset "{{ $ticket->asset->asset_code }}" is not selectable for new tickets. You may keep it or select an active asset.
                        </div>
                    @endif

                    <div>
                        <x-input-label for="asset_id" value="Asset" />
                        <select id="asset_id" name="asset_id" class="app-input mt-1">
                            <option value="">No related asset</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}" @selected((int) old('asset_id', $ticket->asset_id) === $asset->id)>
                                    {{ $asset->asset_code }} - {{ $asset->name }} - {{ $asset->location }}{{ $asset->trashed() ? ' (archived)' : '' }}{{ ! $asset->isSelectableForTickets() && ! $asset->trashed() ? ' (inactive)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('asset_id')" class="mt-2" />
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('tickets.show', $ticket) }}" class="app-button-secondary">Cancel</a>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>
        </x-section-card>
    </div>
</x-app-layout>
