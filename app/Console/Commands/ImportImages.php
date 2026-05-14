<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportImages extends Command
{
    use ConfirmableTrait;

    protected $signature = 'app:import-images {--force : Force the operation to run when in production}';

    protected $description = 'Import upscaled images from image-import directory, replacing existing product images';

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $importPath = storage_path('app/image-import');
        $productsPath = storage_path('app/public/products');

        if (!File::isDirectory($importPath)) {
            $this->error("Import directory does not exist: {$importPath}");
            return Command::FAILURE;
        }

        $files = File::files($importPath);
        $replaced = 0;
        $unmatched = 0;

        foreach ($files as $file) {
            $importName = $file->getFilename();

            // Strip the gigapixel suffix: "name-gigapixel-low_res-width-NNNNpx.ext" → "name.ext"
            $targetName = preg_replace('/-gigapixel-low_res-width-\d+px/', '', $importName);

            $targetPath = $productsPath . '/' . $targetName;

            if (File::exists($targetPath)) {
                // Replace existing file
                File::copy($file->getPathname(), $targetPath);
                $replaced++;

                // Update Image model dimensions and size
                $imageRecord = Image::where('file_name', $targetName)->first();
                if ($imageRecord) {
                    $imageInfo = getimagesize($targetPath);
                    $imageRecord->update([
                        'size' => File::size($targetPath),
                        'width' => $imageInfo[0] ?? $imageRecord->width,
                        'height' => $imageInfo[1] ?? $imageRecord->height,
                    ]);
                }
            } else {
                $this->warn("No match for: {$importName} → {$targetName}");
                $unmatched++;
            }
        }

        // Clear Glide cache so new images are served
        $glideCachePath = storage_path('app/.glide-cache');
        if (File::isDirectory($glideCachePath)) {
            File::deleteDirectory($glideCachePath);
            File::makeDirectory($glideCachePath);
            $this->info('Glide cache cleared.');
        }

        $this->info("Done: {$replaced} images replaced, {$unmatched} unmatched.");

        return Command::SUCCESS;
    }
}
