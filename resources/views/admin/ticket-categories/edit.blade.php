<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Admin"
            title="Edit Ticket Category"
            description="Update the category name or description used by existing ticket records."
        />
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <x-section-card title="Category details">
            <form method="POST" action="{{ route('admin.ticket-categories.update', $category) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" class="mt-1" value="{{ old('name', $category->name) }}" required maxlength="100" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="4" class="app-input mt-1">{{ old('description', $category->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route('admin.ticket-categories.index') }}" class="app-button-secondary">Cancel</a>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>
        </x-section-card>
    </div>
</x-app-layout>
