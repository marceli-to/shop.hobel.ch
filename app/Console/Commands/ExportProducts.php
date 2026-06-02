<?php

namespace App\Console\Commands;

use App\Models\Product;
use BackedEnum;
use Illuminate\Console\Command;

class ExportProducts extends Command
{
    protected $signature = 'app:export-products';

    protected $description = 'Export all products (including variants) to a CSV in storage/exports';

    public function handle(): int
    {
        $dir = storage_path('exports');
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            $this->error("Could not create export directory: {$dir}");

            return self::FAILURE;
        }

        $path = $dir.DIRECTORY_SEPARATOR.'products-'.now()->format('Y-m-d_His').'.csv';

        $handle = fopen($path, 'w');
        if ($handle === false) {
            $this->error("Could not open file for writing: {$path}");

            return self::FAILURE;
        }

        // UTF-8 BOM so Excel renders umlauts correctly.
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'image', 'type', 'name', 'description', 'sku', 'delivery_time', 'price', 'stock',
        ], escape: '');

        $count = 0;
        Product::query()
            ->withCount('images')
            ->orderBy('id')
            ->chunk(500, function ($products) use ($handle, &$count) {
                foreach ($products as $product) {
                    fputcsv($handle, [
                        $product->images_count > 0 ? 'yes' : 'no',
                        $product->type instanceof BackedEnum ? $product->type->value : $product->type,
                        $product->name,
                        $product->description,
                        $product->sku,
                        $product->delivery_time,
                        $product->price,
                        $product->stock,
                    ], escape: '');
                    $count++;
                }
            });

        fclose($handle);

        $this->components->info("Exported {$count} products to {$path}");

        return self::SUCCESS;
    }
}
