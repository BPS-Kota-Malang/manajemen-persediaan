<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ViewQrCode extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament-panels.resources.product-resource.pages.view-qr-code';
    
    protected function getActions(): array
    {
        return [];
    }

    
}
