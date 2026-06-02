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

        // Load everything keyed by id so we can resolve a variant's parent.
        $products = Product::query()->withCount('images')->get()->keyBy('id');

        // A variant shows the parent's image (see HandlesCart), so its "image"
        // status follows the parent. Top-level products use their own count.
        $hasImage = function (Product $p) use ($products): bool {
            $source = $p->parent_id && $products->has($p->parent_id)
                ? $products->get($p->parent_id)
                : $p;

            return $source->images_count > 0;
        };

        // Group each parent with its variants: order by group (parent id),
        // parent row first, then variants by their `order`, then id.
        $sorted = $products->values()->sort(function (Product $a, Product $b) {
            $groupA = $a->parent_id ?? $a->id;
            $groupB = $b->parent_id ?? $b->id;

            return [$groupA, $a->parent_id ? 1 : 0, $a->order, $a->id]
                <=> [$groupB, $b->parent_id ? 1 : 0, $b->order, $b->id];
        });

        $count = 0;
        foreach ($sorted as $product) {
            fputcsv($handle, [
                $hasImage($product) ? 'yes' : 'no',
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

        fclose($handle);

        $this->components->info("Exported {$count} products to {$path}");

        return self::SUCCESS;
    }
}
