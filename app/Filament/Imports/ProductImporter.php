<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Filament\Notifications\Notification;

class ProductImporter extends Importer implements WithHeadingRow
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('category')
                ->requiredMapping()
                ->relationship()
                ->rules(['required', 'exists:categories,id']),
            ImportColumn::make('brand')
                ->requiredMapping()
                ->relationship()
                ->rules(['required', 'exists:brands,id']),
            ImportColumn::make('unit_1')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('unit_2')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('conversion_rate')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // return Product::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Product();
    }

    public function handleImportCompletion(Import $import): void
    {
        // Generate body for the notification
        $body = $this->getCompletedNotificationBody($import);

        // Kirim notifikasi secara langsung tanpa queue
        Notification::make()
            ->title('Import Completed')
            ->body($body)
            ->success()  // Set success atau error
            ->send();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeValidate(): void
    {
        // Cari ID brand berdasarkan nama yang ada dalam data impor
        $brand_id = Brand::query()->where('name', $this->data['brand'])->first()?->id;
        $this->data['brand'] = $brand_id;

        // Cari ID category berdasarkan nama yang ada dalam data impor
        $category_id = Category::query()->where('name', $this->data['category'])->first()?->id;
        $this->data['category'] = $category_id;

        // Validasi apakah brand_id atau category_id ditemukan
        if (is_null($brand_id) || is_null($category_id)) {
            throw new \Exception("Data brand atau category tidak valid. Pastikan data brand dan category sesuai dengan database.");
        }
    }
}
