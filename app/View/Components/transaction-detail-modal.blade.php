<!-- resources/views/filament/components/transaction-detail-modal.blade.php -->
<div class="transaction-details-modal">
    <h2>Transaction Details</h2>

    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>Product</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
                <tr>
                    <td>{{ $detail->product->name }}</td> <!-- Asumsi relasi ke Product -->
                    <td>{{ $detail->unit }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->price }}</td>
                    <td>{{ $detail->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
