<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-x-3 mt-6">
            <x-filament::button type="submit">
                Lưu thay đổi
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

