<?php

namespace App\Filament\Components;

use App\Models\InTransactionDetail; // Model transaksi detail
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Modal;
use Filament\Tables\Columns\TextColumn;
use Illuminate\View\Component;

class TransactionDetailModal extends Component
{
    public $details;

    public function mount($transactionId)
    {
        // Ambil data detail transaksi berdasarkan transaction_id
        $this->details = InTransactionDetail::where('transaction_id', $transactionId)->with('product')->get();
    }

    public function render()
    {
        return view('filament.components.transaction-detail-modal', [
            'details' => $this->details,
        ]);
    }
}
