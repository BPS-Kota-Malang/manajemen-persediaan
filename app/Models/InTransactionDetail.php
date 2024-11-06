<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InTransactionDetail extends Model
{
    use HasFactory;

    protected $with = ['product'];

    protected $fillable = [
        'in_transaction_id',
        'product_id',
        'qty',
        'unit',
        'qty_in_pcs',
        'price',
        'amount',
        'date'
    ];

    public function inTransaction(): BelongsTo
    {
        return $this->belongsTo(InTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getUnits(): array //penting jgn diapus
    {
        return [
            $this->product->unit_1,
            $this->product->unit_2,
        ];
    }

    public function getConversionRate(): int
    {
        return $this->product->conversion_rate ?? 10; // Membuat default dari convertion rate product
    }

    public function addStock($qtyInPcs)
    {
        // Pastikan qtyInPcs tidak null
        if ($qtyInPcs === null) {
            \Log::error('qtyInPcs is null', ['detail' => $this]); // Log jika qtyInPcs null
            return; // Keluar dari fungsi jika qtyInPcs null
        }

        $product = $this->product;
        $priceInPcs = $this->price;

        if ($this->unit === $product->unit_1) {
            // Jika unit_1 dipilih, bagi harga dengan conversion rate
            $priceInPcs = $this->price / $product->conversion_rate;
        }
        // Jika unit_2 dipilih, harga tidak perlu diubah

        // Buat entri stok baru
        Stock::create([
            'product_id' => $this->product_id,
            'qty' => $qtyInPcs, // Simpan qty_in_pcs sebagai qty di tabel stock
            'price' => $priceInPcs, // Harga yang sudah dihitung sesuai unit
            'date' => $this->date,
        ]);
    }



    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->amount = $model->qty * $model->price; // Menghitung amount
        });

        static::created(function ($detail) {
            // Ambil qtyInPcs dari detail dan serahkan ke addStock
            $detail->addStock($detail->qty_in_pcs); // Pastikan qty_in_pcs dihitung dan dikirim
        });
    }

    public function create()
    {
        // Ambil data dari request
        $data = $this->mutateFormDataBeforeCreate(request()->all());

        // Buat InTransaction terlebih dahulu
        $inTransaction = InTransaction::create($data);

        // Jika detail transaksi ada, simpan ke in_transaction_details
        if (isset($data['in_transaction_details']) && !empty($data['in_transaction_details'])) {
            foreach ($data['in_transaction_details'] as $detail) {
                $detail['in_transaction_id'] = $inTransaction->id; // Tambahkan ID transaksi
                InTransactionDetail::create($detail); // Simpan detail transaksi
            }
        }
    }


    protected static function booted()
    {
        static::creating(function ($inTransactionDetail) {
            // Ambil tanggal dari inTransaction dan set ke inTransactionDetail
            if ($inTransactionDetail->inTransaction) {
                $inTransactionDetail->date = $inTransactionDetail->inTransaction->date;
            }
        });

        static::created(function ($inDetail) {
            // Menggunakan relasi product untuk menghindari query tambahan
            $product = $inDetail->product;

            if ($product) {
                // Ambil unit yang dipilih dari inDetail yang sudah tersimpan
                $unitType = $inDetail->unit;
                $qtyInPcs = $inDetail->qty; // Default qtyInPcs

                // Jika unit_1 dipilih, konversi qty ke pcs
                if ($unitType === $product->unit_1) {
                    $qtyInPcs *= $inDetail->getConversionRate(); // Konversi ke pcs menggunakan conversion rate
                }

                // Simpan qty_in_pcs ke entri stok
                $inDetail->addStock($qtyInPcs); // Panggil fungsi updateStock dengan qtyInPcs

                $inDetail->qty_in_pcs = $qtyInPcs; // Set qty_in_pcs
            }
        });
    }


    /**
     * Mengubah data sebelum update record untuk menghitung amount.
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Sama seperti pada create, pastikan amount dihitung
        if (!isset($data['amount']) && isset($data['qty']) && isset($data['price'])) {
            $data['amount'] = $data['qty'] * $data['price'];
        }

        return $data;
    }
}
