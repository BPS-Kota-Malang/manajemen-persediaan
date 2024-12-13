<?php

namespace App\Filament\Resources\InTransactionResource\Pages;

use App\Filament\Resources\InTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInTransactions extends ListRecords
{
    protected static string $resource = InTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ButtonAction::make()->url(fn()=> route('download.tes'))->openUrlInNewTab( ),
            Actions\CreateAction::make(),
            
        ];
    }
}
