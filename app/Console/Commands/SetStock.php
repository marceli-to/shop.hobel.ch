<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SetStock extends Command
{
    use ConfirmableTrait;

    protected $signature = 'app:set-stock
                            {amount=50 : Stock amount to set for all products}
                            {--force : Force the operation to run when in production}';

    protected $description = 'Set stock for all products';

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $amount = (int) $this->argument('amount');

        $count = Product::query()->update(['stock' => $amount]);

        $this->info("Set stock to {$amount} for {$count} products.");

        return Command::SUCCESS;
    }
}
