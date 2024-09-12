<?php

namespace App\Filament\Resources\InTransactionResource\Pages;

use App\Filament\Resources\InTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInTransaction extends EditRecord
{
    protected static string $resource = InTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
