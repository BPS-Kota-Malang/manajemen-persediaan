<?php

namespace App\Filament\Resources\InTransactionResource\Pages;

use App\Filament\Resources\InTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

use App\Models\InTransaction;
use App\Models\InTransactionDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateInTransaction extends CreateRecord
{
    protected static string $resource = InTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        DB::transaction(function () use (&$data) {
            $user = auth()->user(); 


            // Simpan data employee
            // $employee = Employee::create([
            //     'nip' => $employeeData['nip'],
            //     'name' => $employeeData['name'],
            //     'email' => $employeeData['email'],
            //     'no_handphone' => $employeeData['no_handphone'],
            //     'alamat' => $employeeData['alamat'],
            // ]);

            // Simpan data transaksi
            $inTransaction = InTransaction::create([
                'user_id' => $user->id,
                'datetime' => now(),
            ]);

            // Simpan data transaksi detail
            foreach ($data['in_transaction_details'] as $detail) {
                InTransactionDetail::create([
                    'in_transaction_id' => $inTransaction->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                ]);

                // Update stok produk
                $product = Product::find($detail['product_id']);
                if ($product) {
                    // Mengurangi stok sesuai jumlah transaksi
                    if ($product->stok >= $detail['qty']) {
                        $product->stok += $detail['qty'];
                        $product->save();
                    } else {
                        //throw new \Exception("Stok produk tidak mencukupi.");
                    }
                }
            }

            // Mengupdate data transaksi dengan ID employee
            $data['user_id'] = $user->id;
        });

        return $data;
    }
}
