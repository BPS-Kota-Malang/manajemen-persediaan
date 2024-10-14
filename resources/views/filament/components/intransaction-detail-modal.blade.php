<!-- resources/views/filament/components/transaction-detail-modal.blade.php -->
<div>
  
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Unit</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Qty</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $detail)
                <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $detail->product_id }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">
                {{ $detail->product ? $detail->product->name : 'Produk tidak ditemukan' }}
            </td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $detail->unit }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $detail->qty }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $detail->amount }}</td>
        </tr>
            @endforeach
        </tbody>
    </table>
</div>
