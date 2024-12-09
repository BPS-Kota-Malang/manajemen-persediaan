<x-filament::page>
    <div class="flex flex-col items-center space-y-4">
        <!-- Display QR Code -->
        <img src="{{ $this->generateQrCode() }}" alt="QR Code" class="w-48 h-48">

        <!-- Product Name as Title -->
        <h2 class="text-lg font-semibold">{{ $record->name }}</h2>

        <!-- Download Button -->
        <x-filament::button wire:click="downloadQrCode">
            Download QR Code
        </x-filament::button>
    </div>
</x-filament::page>
