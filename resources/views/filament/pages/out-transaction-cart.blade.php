
<x-filament::page>
    <div class="space-y-4">

         <!-- QR Scanner Section -->
         <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Scan QR Code</h2>
            <div class="aspect-video max-w-md mx-auto">
                <video id="preview" class="w-full h-full rounded-lg"></video>
            </div>
        </div>

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
                <x-filament::button
                    color="success"
                    wire:click="checkout">
                    Checkout
                </x-filament::button>

                <x-filament::button
                    color="danger"
                    wire:click="resetCart">
                    Reset Cart
                </x-filament::button>
            </div>
        @else
            <p class="text-gray-500">Your cart is empty.</p>
        @endif
    </div>

    <!-- Scripts untuk QR Scanner -->
    @push('scripts')
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let scanner = new Instascan.Scanner({ 
                video: document.getElementById('preview'),
                mirror: false
            });

            scanner.addListener('scan', function (content) {
                // Redirect ke URL yang ada di QR code
                window.location.href = content;
            });

            // Start camera
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    // Coba gunakan kamera belakang jika ada
                    let selectedCamera = cameras[0];
                    cameras.forEach(function(camera) {
                        if (camera.name.toLowerCase().includes('back')) {
                            selectedCamera = camera;
                        }
                    });
                    scanner.start(selectedCamera);
                } else {
                    console.error('No cameras found.');
                    alert('No cameras found.');
                }
            }).catch(function (e) {
                console.error(e);
                alert('Error accessing camera: ' + e.message);
            });
        });
    </script>
    @endpush

</x-filament::page>
