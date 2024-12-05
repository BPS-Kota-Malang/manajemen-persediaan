<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;

class ViewQrCode extends ViewRecord
{
    protected static string $resource = ProductResource::class;
    protected static string $view = 'filament-panels.resources.product-resource.pages.view-qr-code';

    protected function getActions(): array
    {
        return [
            Action::make('downloadQrCode')
                ->label('Download QR Code')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return $this->downloadQrCode($this->record->id);
                }),
        ];
    }

    public function downloadQrCode($id)
    {
        // Generate QR Code as PNG
        $qrCode = QrCode::format('png')->size(200)->generate($id);

        // Return QR Code as download response
        return Response::make($qrCode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qrcode-' . $id . '.png"',
        ]);
    }
}

