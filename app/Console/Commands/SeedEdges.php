<?php

namespace App\Console\Commands;

use App\Models\Edge;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SeedEdges extends Command
{
    use ConfirmableTrait;

    protected $signature = 'app:seed-edges {--force : Force the operation to run when in production}';

    protected $description = 'Seed the edges table with default Kanten';

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $edges = [
            ['name' => 'Standardkante',        'price' => 40,  'order' => 0],
            ['name' => 'Rundkante',            'price' => 65,  'order' => 1],
            ['name' => 'Schweizer Kante',      'price' => 90,  'order' => 2],
            ['name' => 'Karnies / Profilkante', 'price' => 120, 'order' => 3],
        ];

        foreach ($edges as $data) {
            $edge = Edge::updateOrCreate(
                ['name' => $data['name']],
                $data,
            );

            $this->info("Seeded edge: {$edge->name} (id={$edge->id})");
        }

        return Command::SUCCESS;
    }
}
