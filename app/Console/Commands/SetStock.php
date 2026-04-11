<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SetStock extends Command
{
    protected $signature = 'app:set-stock {amount=50 : Stock amount to set for all products}';

    protected $description = 'Set stock for all products';

    public function handle(): int
    {
        $amount = (int) $this->argument('amount');

        $count = Product::query()->update(['stock' => $amount]);

        $this->info("Set stock to {$amount} for {$count} products.");

        return Command::SUCCESS;
    }
}
