<!-- resources/views/filament/components/intransaction-detail-modal.blade.php -->
<div class="p-4 space-y-4 bg-white rounded-lg shadow-xl">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($details as $detail)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->product_id }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->product->name }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->unit }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->qty }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($detail->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
