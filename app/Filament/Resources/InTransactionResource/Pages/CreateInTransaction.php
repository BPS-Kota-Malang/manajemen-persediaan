<?php

namespace App\Filament\Resources\InTransactionResource\Pages;

use App\Filament\Resources\InTransactionResource;
use App\Models\Employee;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

use App\Models\InTransaction;
use App\Models\InTransactionDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;


class CreateInTransaction extends CreateRecord
{
    protected static string $resource = InTransactionResource::class;

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
        return 'Pembelian disimpan';
    }

}
