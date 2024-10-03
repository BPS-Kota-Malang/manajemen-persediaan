<?php

namespace App\Filament\Resources\OutTransactionResource\Pages;

use App\Filament\Resources\OutTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CreateOutTransaction extends CreateRecord
{
    protected static string $resource = OutTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;

    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Penjualan disimpan';
    }
}
