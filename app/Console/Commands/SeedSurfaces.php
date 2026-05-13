<?php

namespace App\Console\Commands;

use App\Models\Surface;
use Illuminate\Console\Command;

class SeedSurfaces extends Command
{
    protected $signature = 'app:seed-surfaces';

    protected $description = 'Seed the surfaces table with default Oberflächen';

    public function handle(): int
    {
        $surfaces = [
            ['name' => 'Öl farblos',        'price' => 120, 'minimum_amount' => 160, 'order' => 0],
            ['name' => 'Pigmentiertes Öl',  'price' => 180, 'minimum_amount' => 220, 'order' => 1],
            ['name' => 'Klarlack Antikmatt', 'price' => 190, 'minimum_amount' => 240, 'order' => 2],
        ];

        foreach ($surfaces as $data) {
            $surface = Surface::updateOrCreate(
                ['name' => $data['name']],
                $data,
            );

            $this->info("Seeded surface: {$surface->name} (id={$surface->id})");
        }

        return Command::SUCCESS;
    }
}
