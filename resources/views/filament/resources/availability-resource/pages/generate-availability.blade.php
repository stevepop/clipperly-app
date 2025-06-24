<x-filament::page>
    <form wire:submit.prevent="generate" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Generate Availability
            </x-filament::button>
        </div>
    </form>
</x-filament::page>