<?php

namespace App\Filament\Resources\OutTransactionResource\Pages;

use App\Filament\Resources\OutTransactionResource;
use Filament\Resources\Pages\Page;

class OutTransaction extends Page
{
    protected static string $resource = OutTransactionResource::class;

    protected static ?string $navigationLabel = 'Scan QR Code';
    protected static ?string $navigationIcon = 'heroicon-o-qrcode';

    protected static string $view = 'filament.resources.out-transaction-resource.pages.out-transaction';

    
}
