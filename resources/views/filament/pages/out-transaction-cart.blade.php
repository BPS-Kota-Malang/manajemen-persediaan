<x-filament::page>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush 
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
                    onclick="confirmDelete({{ $item['id'] }})"
                    style="display: inline-flex; 
                           align-items: center; 
                           padding: 8px 12px; 
                           background-color: #DC2626; 
                           color: white; 
                           border-radius: 6px;
                           border: none;
                           cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         style="width: 20px; height: 20px;"
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke="currentColor">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2" 
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span style="margin-left: 8px; font-weight: 500;">Delete</span>
                </button>
            </td>
    @push('scripts')
<script>
    function confirmDelete(productId) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah anda yakin akan menghapus cart ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('removeItem', productId);
            }
        });
    }

    // Untuk menampilkan notifikasi sukses setelah item dihapus
    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            timer: event.detail.timer,
            showConfirmButton: false
        }).then(() => {
            // Refresh halaman jika diperlukan
            window.location.reload();
        });
    });

    window.addEventListener('swal:error', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            timer: event.detail.timer,
            showConfirmButton: false
        });
    });
</script>
@endpush
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tombol Checkout dan Reset -->
            <div class="flex justify-end gap-4">
                <x-filament::button
                    color="primary"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold"
                    wire:click="checkout">
                    Checkout
                </x-filament::button>
                
                <x-filament::button
                onclick="confirmResetCart()"
                color="danger" 
                class="bg-red-600 hover:bg-red-700 text-white font-semibold">
                Reset Cart
                </x-filament::button>
                </div>
                @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
        function confirmResetCart() {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah anda yakin ingin menghapus semua cart ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('resetCart');
            }
        });
    }
    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            timer: event.detail.timer,
            showConfirmButton: false
        });
    });
</script>
@endpush
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