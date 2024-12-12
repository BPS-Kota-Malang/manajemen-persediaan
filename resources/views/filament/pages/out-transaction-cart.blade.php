
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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $counter = 1;
                        @endphp
                        @foreach ($this->cartItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $counter++ }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <!-- Tombol Minus -->
                                        <button 
                                            wire:click="decrementQuantity({{ $item['id'] }})"
                                            class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>

                                        <!-- Quantity -->
                                        <span class="text-gray-700 px-6 min-w-[3rem] text-center font-medium">
                                            {{ $item['quantity'] }}
                                        </span>

                                        <!-- Tombol Plus -->
                                        <button 
                                            wire:click="incrementQuantity({{ $item['id'] }})"
                                            class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $item['unit_2'] ?? 'pcs' }} {{-- Mengambil unit_2 dari product --}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button 
                                        wire:click="removeItem({{ $item['id'] }})"
                                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                        Hapus
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
                    color="primary"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold"
                    wire:click="checkout">
                    Checkout
                </x-filament::button>
                
                <x-filament::button
                    color="danger" 
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold"
                    wire:click="resetCart">
                    Reset Cart
                </x-filament::button>
            </div>


        @else
            <p class="text-gray-500">Your cart is empty.</p>
        @endif
    </div>

     <!-- Modal Edit Quantity -->
     <x-filament::modal id="edit-quantity-modal" wire:model="showEditModal">
        <x-slot name="header">
            Edit Quantity
        </x-slot>

        <div class="p-4">
            <x-filament::input
                type="number"
                wire:model="editQuantity"
                label="Quantity"
                min="1"
            />
        </div>

        <x-slot name="footer">
            <x-filament::button wire:click="updateQuantity" color="primary">
                Save
            </x-filament::button>
            <x-filament::button wire:click="$set('showEditModal', false)" color="secondary">
                Cancel
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

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
