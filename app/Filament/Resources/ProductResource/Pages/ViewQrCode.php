<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Endroid\QrCode\Builder\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewQrCode extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament-panels.resources.product-resource.pages.view-qr-code';
    
    protected function getActions(): array
    {
        return [];
    }

    public function generateQrCode()
    {
        // Generate QR Code as a base64 encoded string
        $qrCode = Builder::create()
            ->data($this->record->id) // Data for the QR code
            ->size(200)               // Size of the QR Code
            ->build();

        // Encode QR code to base64
        $imageData = base64_encode($qrCode->getString());
        return 'data:' . $qrCode->getMimeType() . ';base64,' . $imageData;
    }

    // Tambahkan metode untuk unduhan QR Code
    public function downloadQrCode(): StreamedResponse
    {
        // Generate QR Code as PNG
        $qrCode = Builder::create()
            ->data($this->record->id) // Data untuk QR Code
            ->size(200)               // Ukuran QR Code
            ->build();

        // Nama file QR Code
        $fileName = 'qrcode-' . $this->record->name . '.png';

        // Response file untuk diunduh
        return response()->streamDownload(function () use ($qrCode) {
            echo $qrCode->getString(); // QR Code dalam bentuk string
        }, $fileName, [
            'Content-Type' => 'image/png',
        ]);
    }
}
