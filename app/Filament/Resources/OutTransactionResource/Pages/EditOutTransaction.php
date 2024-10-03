<?php

namespace App\Filament\Resources\OutTransactionResource\Pages;

use App\Filament\Resources\OutTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutTransaction extends EditRecord
{
    protected static string $resource = OutTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
