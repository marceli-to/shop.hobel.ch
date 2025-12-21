<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\Tag;
use App\Models\Image;
use App\Models\WoodType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DataImport extends Command
{
    protected $signature = 'app:data-import';

    protected $description = 'Import data from data.json';

    protected array $data = [];
    protected array $idMaps = [];

    public function handle(): int
    {
        $path = 'data-import/data.json';

        if (!Storage::disk('local')->exists($path)) {
            $this->error("File not found: storage/app/{$path}");
            return Command::FAILURE;
        }

        $json = json_decode(Storage::disk('local')->get($path), true);

        if (!$json) {
            $this->error('Failed to parse JSON file');
            return Command::FAILURE;
        }

        // Parse the JSON structure into tables
        foreach ($json as $item) {
            if (isset($item['type']) && $item['type'] === 'table') {
                $this->data[$item['name']] = $item['data'];
            }
        }

        // Clear tables first
        $this->clearTables();

        // Import in order (respecting dependencies)
        $this->importWoodTypes();
        $this->importCategories();
        $this->importTags();
        $this->importProducts();
        $this->importProductAttributes();
        $this->importProductVariations();
        $this->importProductImages();
        $this->importCategoryProduct();
        $this->importProductTag();

        $this->info('Data import completed!');

        return Command::SUCCESS;
    }

    protected function clearTables(): void
    {
        $this->info('Clearing tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('product_tag')->truncate();
        DB::table('category_product')->truncate();
        Image::truncate();
        ProductVariation::truncate();
        ProductAttribute::truncate();
        Product::truncate();
        Tag::truncate();
        Category::truncate();
        WoodType::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('  - Tables cleared');
    }

    protected function importWoodTypes(): void
    {
        if (!isset($this->data['wood_types'])) {
            $this->warn('No wood_types data found');
            return;
        }

        $count = 0;

        foreach ($this->data['wood_types'] as $index => $item) {
            WoodType::create([
                'name' => $item['name'],
                'price' => $item['price'],
                'order' => $index,
            ]);
            $count++;
        }

        $this->info("  - Imported {$count} wood types");
    }

    protected function importCategories(): void
    {
        if (!isset($this->data['categories'])) {
            $this->warn('No categories data found');
            return;
        }

        $this->idMaps['categories'] = [];
        $count = 0;

        foreach ($this->data['categories'] as $index => $item) {
            $category = Category::create([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'order' => $index,
            ]);
            $this->idMaps['categories'][$item['id']] = $category->id;
            $count++;
        }

        $this->info("  - Imported {$count} categories");
    }

    protected function importTags(): void
    {
        if (!isset($this->data['tags'])) {
            $this->warn('No tags data found');
            return;
        }

        $this->idMaps['tags'] = [];
        $count = 0;

        foreach ($this->data['tags'] as $index => $item) {
            $categoryId = $this->idMaps['categories'][$item['category_id']] ?? null;

            if (!$categoryId) {
                $this->warn("  - Skipping tag '{$item['name']}': category not found");
                continue;
            }

            $tag = Tag::create([
                'category_id' => $categoryId,
                'name' => $item['name'],
                'slug' => $item['slug'],
                'order' => $index,
            ]);
            $this->idMaps['tags'][$item['id']] = $tag->id;
            $count++;
        }

        $this->info("  - Imported {$count} tags");
    }

    protected function importProducts(): void
    {
        if (!isset($this->data['products'])) {
            $this->warn('No products data found');
            return;
        }

        $this->idMaps['products'] = [];
        $count = 0;

        foreach ($this->data['products'] as $item) {
            // Map product type
            $type = match ($item['type']) {
                'simple' => ProductType::Simple,
                'configurable' => ProductType::Configurable,
                'variations' => ProductType::Variations,
                default => ProductType::Simple,
            };

            $stock = (int) ($item['stock'] ?? 0);
            if ($stock === 0) {
                $stock = 10;
            }

            $product = Product::create([
                'type' => $type,
                'name' => $item['name'],
                'sku' => $item['sku'],
                'description' => $item['description'] ?: null,
                'price' => $item['price'] ?: 0,
                'delivery_time' => $item['delivery_time'] ?: null,
                'stock' => $stock,
                'published' => (bool) $item['published'],
            ]);
            $this->idMaps['products'][$item['id']] = $product->id;
            $count++;
        }

        $this->info("  - Imported {$count} products");
    }

    protected function importProductAttributes(): void
    {
        if (!isset($this->data['product_attributes'])) {
            $this->warn('No product_attributes data found');
            return;
        }

        $count = 0;

        foreach ($this->data['product_attributes'] as $item) {
            $productId = $this->idMaps['products'][$item['product_id']] ?? null;

            if (!$productId) {
                continue;
            }

            ProductAttribute::create([
                'product_id' => $productId,
                'name' => $item['description'],
                'order' => $item['position'] ?? 0,
            ]);
            $count++;
        }

        $this->info("  - Imported {$count} product attributes");
    }

    protected function importProductVariations(): void
    {
        if (!isset($this->data['product_variations'])) {
            $this->warn('No product_variations data found');
            return;
        }

        $count = 0;

        foreach ($this->data['product_variations'] as $index => $item) {
            $productId = $this->idMaps['products'][$item['product_id']] ?? null;

            if (!$productId) {
                continue;
            }

            // Convert \n to actual newlines in description
            $description = $item['description'] ? str_replace('\n', "\n", $item['description']) : null;

            $stock = (int) ($item['stock'] ?? 0);
            if ($stock === 0) {
                $stock = 10;
            }

            ProductVariation::create([
                'product_id' => $productId,
                'name' => $item['name'],
                'label' => $item['label'] ?: null,
                'sku' => $item['sku'],
                'short_description' => $description,
                'price' => $item['price'] ?: 0,
                'stock' => $stock,
                'order' => $index,
            ]);
            $count++;
        }

        $this->info("  - Imported {$count} product variations");
    }

    protected function importProductImages(): void
    {
        $path = 'data-import/product-images.json';

        if (!Storage::disk('local')->exists($path)) {
            $this->warn("No product-images.json found");
            return;
        }

        $images = json_decode(Storage::disk('local')->get($path), true);

        if (!$images) {
            $this->warn('Failed to parse product-images.json');
            return;
        }

        $count = 0;

        foreach ($images as $item) {
            $productId = $this->idMaps['products'][$item['imageable_id']] ?? null;

            if (!$productId) {
                continue;
            }

            $fileName = $item['name'];
            $filePath = 'products/' . $fileName;

            Image::create([
                'imageable_type' => Product::class,
                'imageable_id' => $productId,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'order' => $item['position'] ?? 0,
                'preview' => ($item['position'] ?? 0) === 0,
            ]);
            $count++;
        }

        $this->info("  - Imported {$count} product images");
    }

    protected function importCategoryProduct(): void
    {
        if (!isset($this->data['category_product'])) {
            $this->warn('No category_product data found');
            return;
        }

        $count = 0;

        foreach ($this->data['category_product'] as $item) {
            $categoryId = $this->idMaps['categories'][$item['category_id']] ?? null;
            $productId = $this->idMaps['products'][$item['product_id']] ?? null;

            if (!$categoryId || !$productId) {
                continue;
            }

            DB::table('category_product')->insert([
                'category_id' => $categoryId,
                'product_id' => $productId,
            ]);
            $count++;
        }

        $this->info("  - Imported {$count} category-product relations");
    }

    protected function importProductTag(): void
    {
        if (!isset($this->data['product_tag'])) {
            $this->warn('No product_tag data found');
            return;
        }

        $count = 0;

        foreach ($this->data['product_tag'] as $item) {
            $productId = $this->idMaps['products'][$item['product_id']] ?? null;
            $tagId = $this->idMaps['tags'][$item['tag_id']] ?? null;

            if (!$productId || !$tagId) {
                continue;
            }

            DB::table('product_tag')->insert([
                'product_id' => $productId,
                'tag_id' => $tagId,
            ]);
            $count++;
        }

        $this->info("  - Imported {$count} product-tag relations");
    }
}
