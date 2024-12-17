<?php

namespace App\Http\Controllers;

use App\Models\OutTransaction;
use Illuminate\Http\Request;
use App\Filament\Pages\OutTransactionCart;
use Barryvdh\DomPDF\Facade\Pdf;

class OutTransactionController extends Controller
{
    public function showCart($productId = null)
    {
        return app(OutTransactionCart::class)->mount($productId);
    }

    public function addToCart(Request $request)
    {
        // Logika untuk menambahkan item ke keranjang
        return response()->json(['message' => 'Item berhasil ditambahkan ke keranjang']);
    }

    public function exportPDF(Request $request)
    {
        $from = $request->input('from');
        $until = $request->input('until');

        $query = OutTransaction::query()
            ->with(['employee', 'outTransactionDetails.product'])
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($until, fn($q) => $q->whereDate('date', '<=', $until))
            ->orderBy('date', 'desc');

        $transactions = $query->get();

        $pdf = PDF::loadView('outtransaction-list-pdf', [
            'transactions' => $transactions,
            'from' => $from,
            'until' => $until,
        ]);

        return $pdf->stream('outtransaction-list.pdf');
    }

    public function showDetail(OutTransaction $outTransaction)
    {
        return view('filament.components.outtransaction-detail-modal', [
            'details' => $outTransaction->outTransactionDetails
        ]);
    }
    
}
