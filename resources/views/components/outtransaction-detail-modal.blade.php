<div>
    <h2 class="text-lg font-bold">Transaction Details</h2>

    <table class="table-auto w-full">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2">Product</th>
                <th class="px-4 py-2">Unit</th>
                <th class="px-4 py-2">Quantity</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
                <tr>
                    <td class="border px-4 py-2">{{ $detail->product->name }}</td>
                    <td class="border px-4 py-2">{{ $detail->unit }}</td>
                    <td class="border px-4 py-2">{{ $detail->qty }}</td>
                    <td class="border px-4 py-2">{{ $detail->price }}</td>
                    <td class="border px-4 py-2">{{ $detail->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
