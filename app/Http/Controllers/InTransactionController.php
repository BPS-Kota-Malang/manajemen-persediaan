<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class InTransactionController extends Controller
{
    protected $user;
    protected $employee;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function exportPDF(Request $request)
    {
        $dateFrom = $request->from;
        $dateUntil = $request->until;

        $query = InTransaction::with(['employee', 'inTransactionDetails.product']);
        
        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateUntil) {
            $query->whereDate('date', '<=', $dateUntil);
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('intransaction-list-pdf', [
            'transactions' => $transactions,
            'filters' => [
                'from' => $dateFrom,
                'until' => $dateUntil
            ]
        ]);

        return $pdf->stream('daftar_transaksi_masuk.pdf');
    }
}
