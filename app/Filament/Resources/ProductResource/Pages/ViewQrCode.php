<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Endroid\QrCode\Builder\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewQrCode extends ViewRecord
{
    protected static string $resource = ProductResource::class;
    protected static string $view = 'filament-panels.resources.product-resource.pages.view-qr-code';

    protected function getActions(): array
    {
        return [
            Action::make('downloadQrCode')
                ->label('Download QR Code')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return $this->downloadQrCode($this->record->id);
                }),
        ];
    }

    public function generateQrCode()
    {
        // Generate URL for cart page with product ID
        $url = route('outtransactions.cart', ['productId' => $this->record->id]);

        // Generate QR Code as a base64 encoded string
        $qrCode = Builder::create()
            //->writer(new PngWriter())
            ->data($url) // Use the URL as the QR code data
            ->size(200)  // Size of the QR Code
            ->build();

        // Encode QR code to base64
        $imageData = base64_encode($qrCode->getString());

        return 'data:' . $qrCode->getMimeType() . ';base64,' . $imageData;
    }
    // public function generateQrCode()
    // {
    //     // Generate URL for cart page with product ID
    //     $url = route('outtransactions.cart.add', ['product_id' => $this->record->id]);

    //     // Generate QR Code as a base64 encoded string
    //     $qrCode = Builder::create()
    //         ->data($url) // Use the URL as the QR code data
    //         ->size(200)  // Size of the QR Code
    //         ->build();

    //     // Encode QR code to base64
    //     $imageData = base64_encode($qrCode->getString());
    //     return 'data:' . $qrCode->getMimeType() . ';base64,' . $imageData;
    // }

    public function downloadQrCode(): StreamedResponse
    {
        // Generate URL for cart page with product ID
        $url = route('outtransactions.cart', ['product_id' => $this->record->id]);

        // Generate QR Code as PNG
        $qrCode = Builder::create()
            ->data($url) // Use the URL as the QR code data
            ->size(200)  // Size of the QR Code
            ->build();

        // Name of the QR Code file
        $fileName = 'qrcode-' . $this->record->name . '.png';

        // Response file for download
        return response()->streamDownload(function () use ($qrCode) {
            echo $qrCode->getString(); // QR Code as a string
        }, $fileName, [
            'Content-Type' => 'image/png',
        ]);
    }

    public function generateQrCodeUrl()
       {
           $url = route('outtransactions.cart', ['productId' => $this->record->id]);

           return $url;
       }

       public function removeItem($productId)
       {
           try {
               $cart = Session::get('cart', []);
               
               if (isset($cart[$productId])) {
                   unset($cart[$productId]);
                   Session::put('cart', $cart);
                   $this->cartItems = $cart;
   
                   // Dispatch event sukses
                   $this->dispatchBrowserEvent('swal:success', [
                       'title' => 'Berhasil!',
                       'text' => 'Item berhasil dihapus dari cart',
                       'icon' => 'success',
                       'timer' => 3000
                   ]);
               }
           } catch (\Exception $e) {
               // Handle error
               $this->dispatchBrowserEvent('swal:error', [
                   'title' => 'Error!',
                   'text' => 'Gagal menghapus item dari cart',
                   'icon' => 'error',
                   'timer' => 3000
               ]);
           }
       }

       public function resetCart()
{
    try {
        Session::forget('cart');
        $this->cartItems = [];
        
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Semua item berhasil dihapus dari cart',
            'icon' => 'success',
            'timer' => 3000
        ]);
    } catch (\Exception $e) {
        $this->dispatchBrowserEvent('swal:error', [
            'title' => 'Error!',
            'text' => 'Gagal menghapus cart',
            'icon' => 'error',
            'timer' => 3000
        ]);
    }
}}