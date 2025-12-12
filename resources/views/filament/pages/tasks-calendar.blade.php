<x-filament-panels::page>
    @if (count($widgets = $this->getWidgets()))
        <x-filament-widgets::widgets
            :widgets="$widgets"
        />
    @endif
</x-filament-panels::page>

