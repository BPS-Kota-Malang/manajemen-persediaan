<x-filament::page>
    {!! QrCode::size(200)->generate($record->id); !!}
</x-filament::page>
