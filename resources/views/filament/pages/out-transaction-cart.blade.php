<x-filament::page>
    <div class="space-y-4">
        <h2 class="text-xl font-semibold">Cart Items</h2>

        @if (count($this->cartItems) > 0)
            <!-- Tabel Cart -->
            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->cartItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item['quantity'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button 
                                        wire:click="removeItem({{ $item['id'] }})" 
                                        class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tombol Checkout dan Reset -->
            <div class="flex justify-end space-x-2">
                <x-filament::button color="success" wire:click="checkout">
                    Checkout
                </x-filament::button>
                <x-filament::button color="danger" wire:click="resetCart">
                    Reset Cart
                </x-filament::button>
            </div>
        @else
            <p class="text-gray-500">Your cart is empty.</p>
        @endif
    </div>
</x-filament::page>