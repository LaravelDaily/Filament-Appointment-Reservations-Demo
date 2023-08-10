<div class="mt-16">

    <h2 class="text-2xl font-bold tracking-tight mb-4">Create Reservation</h2>

    <div class="bg-white p-6 rounded-md shadow-sm">
        <form wire:submit="create">
            {{ $this->form }}

            <button type="submit" class="bg-primary-600 text-white font-bold rounded-md px-3 py-2 tracking-tight mt-8">
                Create
            </button>
        </form>
    </div>

    <x-filament-actions::modals />
</div>
