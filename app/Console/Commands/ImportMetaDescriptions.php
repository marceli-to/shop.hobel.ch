<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class ImportMetaDescriptions extends Command
{
    protected $signature = 'meta:import
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Import product and category meta descriptions from CSV files in the public directory';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $products = $this->importFile(
            public_path('meta_descriptions_products.csv'),
            Product::class,
            $dryRun
        );

        $categories = $this->importFile(
            public_path('meta_descriptions_categories.csv'),
            Category::class,
            $dryRun
        );

        if ($products === null || $categories === null) {
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '')."Done. Products updated: {$products}, Categories updated: {$categories}.");

        return Command::SUCCESS;
    }

    private function importFile(string $path, string $model, bool $dryRun): ?int
    {
        if (! is_readable($path)) {
            $this->error("File not found or not readable: {$path}");

            return null;
        }

        $this->info('Importing '.basename($path).' …');

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        if ($header === false) {
            $this->error("File is empty: {$path}");
            fclose($handle);

            return null;
        }

        $header = array_map('trim', $header);
        $idIndex = array_search('id', $header, true);
        $descIndex = array_search('meta_description', $header, true);

        if ($idIndex === false || $descIndex === false) {
            $this->error("Missing 'id' or 'meta_description' column in: {$path}");
            fclose($handle);

            return null;
        }

        $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || $row[$idIndex] === null || $row[$idIndex] === '') {
                continue;
            }

            $id = (int) $row[$idIndex];
            $description = trim($row[$descIndex] ?? '');

            $record = $model::find($id);

            if (! $record) {
                $this->warn("  {$model} #{$id} not found — skipped.");

                continue;
            }

            if ($record->meta_description === $description) {
                continue;
            }

            if (! $dryRun) {
                $record->meta_description = $description;
                $record->save();
            }

            $updated++;
        }

        fclose($handle);

        return $updated;
    }
}
