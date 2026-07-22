<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Tickets"
            title="Create Ticket"
            description="Submit a clear report so administrators can assign the right technician quickly."
        />
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <x-section-card title="Support request" description="Describe the issue, where it happens, and any asset involved.">
            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <section class="space-y-5">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Issue Information</h2>
                        <p class="mt-1 text-sm text-slate-600">Use a short title and enough detail for the support team to reproduce the issue.</p>
                    </div>

                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1" value="{{ old('title') }}" required maxlength="150" placeholder="Example: Laptop cannot connect to campus Wi-Fi" />
                        <p class="app-help">Maximum 150 characters.</p>
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="7" class="app-input mt-1" required placeholder="What happened, when it started, and what you already tried.">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="location" value="Location" />
                        <x-text-input id="location" name="location" class="mt-1" value="{{ old('location') }}" required maxlength="150" placeholder="Building, room, floor, or desk" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Classification</h2>
                        <p class="mt-1 text-sm text-slate-600">Choose the category and priority that best match the issue.</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="ticket_category_id" value="Category" />
                            <select id="ticket_category_id" name="ticket_category_id" class="app-input mt-1" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('ticket_category_id') === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority" class="app-input mt-1" required>
                                <option value="">Select priority</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->value }}" @selected(old('priority') === $priority->value)>{{ $priority->label() }}</option>
                                @endforeach
                            </select>
                            <p class="app-help">Use Critical only when work is blocked or service is unavailable.</p>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Related Asset</h2>
                        <p class="mt-1 text-sm text-slate-600">Link a managed device if the issue concerns known inventory.</p>
                    </div>

                    <div>
                        <x-input-label for="asset_id" value="Asset" />
                        <select id="asset_id" name="asset_id" class="app-input mt-1">
                            <option value="">No related asset</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}" @selected((int) old('asset_id') === $asset->id)>{{ $asset->asset_code }} - {{ $asset->name }} - {{ $asset->location }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('asset_id')" class="mt-2" />
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Supporting Files</h2>
                        <p class="mt-1 text-sm text-slate-600">Add screenshots, photos, or PDFs when they help explain the issue.</p>
                    </div>

                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
                        <x-input-label for="attachments" value="Upload supporting files" />
                        <input id="attachments" name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf" class="mt-3 block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                        <p class="app-help">JPG, PNG, or PDF. Maximum 5 MB each, up to 5 files.</p>
                        <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                        <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('tickets.index') }}" class="app-button-secondary">Cancel</a>
                    <x-primary-button>Create Ticket</x-primary-button>
                </div>
            </form>
        </x-section-card>
    </div>
</x-app-layout>
