<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GoLiveReset extends Command
{
    protected $signature = 'shop:reset
                            {--stock=50 : Stock level to set for every product}
                            {--force : Skip the confirmation prompt}';

    protected $description = 'Reset the shop for go-live: clear orders and runtime data, reset product stock';

    /**
     * Non-product tables that accumulate runtime/test data and should be emptied.
     */
    protected array $tablesToReset = [
        'order_items',
        'orders',
        'jobs',
        'failed_jobs',
    ];

    public function handle(): int
    {
        $stock = (int) $this->option('stock');

        $this->warn('This will permanently delete ALL orders and runtime data, then reset stock.');
        $this->line('Tables to be truncated: '.implode(', ', $this->tablesToReset));
        $this->line("Stock for every product will be set to: {$stock}");

        if (! $this->option('force') && ! $this->confirm('Are you sure you want to continue?')) {
            $this->info('Aborted.');

            return Command::SUCCESS;
        }

        // Truncate non-product tables (FK checks off so child/parent order doesn't matter).
        Schema::disableForeignKeyConstraints();
        foreach ($this->tablesToReset as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("Truncated: {$table}");
            }
        }
        Schema::enableForeignKeyConstraints();

        // Reset stock for every product (includes soft-deleted to be safe).
        $updated = Product::withTrashed()->update(['stock' => $stock]);
        $this->line("Set stock to {$stock} on {$updated} product(s).");

        $this->info('Shop reset complete. Ready for go-live.');

        return Command::SUCCESS;
    }
}
