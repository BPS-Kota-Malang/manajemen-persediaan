<x-filament::page>
    <div class="flex flex-col items-center space-y-6">
        <div>
            <h1 class="text-2xl font-bold">Scan QR Code</h1>
        </div>

        <!-- Kamera dan QR Scanner -->
        <div id="qr-reader" class="border border-gray-300 rounded-md"></div>
        <p class="mt-2 text-gray-600">Arahkan kamera Anda ke QR Code.</p>

        <!-- Hasil Scan -->
        <div class="w-full mt-6">
            <h2 class="text-xl font-semibold">Produk yang Ditemukan:</h2>
            <ul id="product-list" class="mt-4 space-y-2">
                <!-- Produk hasil scan akan muncul di sini -->
            </ul>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script>
        const reader = new Html5Qrcode("qr-reader");
        const productList = document.getElementById("product-list");

        // Fungsi untuk menangani hasil scan QR
        const handleScanResult = (decodedText) => {
            // Fetch data produk berdasarkan hasil scan
            fetch(`/api/products/${decodedText}`)
                .then(response => response.json())
                .then(product => {
                    // Tambahkan produk ke daftar
                    const item = document.createElement("li");
                    item.textContent = `${product.name} - ${product.category.name}`;
                    productList.appendChild(item);
                })
                .catch(error => console.error("Error fetching product:", error));
        };

        // Mulai scan QR Code
        reader.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, handleScanResult);
    </script>
</x-filament::page>
