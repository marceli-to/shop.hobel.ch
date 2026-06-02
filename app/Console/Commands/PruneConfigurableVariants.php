<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class PruneConfigurableVariants extends Command
{
    protected $signature = 'app:prune-configurable-variants
        {--force : Skip the confirmation prompt}
        {--dry-run : Show what would be deleted without backing up or deleting}
        {--skip-backup : Skip the database backup (not recommended)}';

    protected $description = 'Back up the database to storage/backups, then hard-delete the leftover variant rows of configurable products';

    public function handle(): int
    {
        $configIds = Product::query()
            ->where('type', ProductType::Configurable->value)
            ->pluck('id');

        if ($configIds->isEmpty()) {
            $this->info('No configurable products found. Nothing to prune.');

            return self::SUCCESS;
        }

        // Variant rows are the direct children (parent_id) of a configurable product.
        // Include trashed so a hard delete leaves nothing behind.
        $childCount = Product::withTrashed()
            ->whereIn('parent_id', $configIds)
            ->count();

        if ($childCount === 0) {
            $this->info('Configurable products have no variant rows. Nothing to prune.');

            return self::SUCCESS;
        }

        $this->components->info("Found {$childCount} variant rows under {$configIds->count()} configurable products:");
        $this->table(
            ['ID', 'Product', 'Variant rows'],
            Product::whereIn('id', $configIds)->orderBy('id')->get()->map(fn (Product $p) => [
                $p->id,
                $p->name,
                Product::withTrashed()->where('parent_id', $p->id)->count(),
            ])->all()
        );

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no backup taken and no rows deleted.');

            return self::SUCCESS;
        }

        if (! $this->option('force')
            && ! $this->confirm("Hard-delete these {$childCount} variant rows? A database backup is taken first.", false)) {
            $this->info('Aborted. Nothing was deleted.');

            return self::FAILURE;
        }

        // 1. Back up the database before any destructive change.
        if (! $this->option('skip-backup')) {
            $backupPath = $this->backupDatabase();

            if ($backupPath === null) {
                $this->error('Database backup failed — aborting. No rows were deleted.');

                return self::FAILURE;
            }

            $size = number_format(filesize($backupPath) / 1024 / 1024, 2);
            $this->components->info("Database backed up to {$backupPath} ({$size} MB)");
        } else {
            $this->warn('Skipping database backup (--skip-backup).');
        }

        // 2. Hard delete the variant rows. The ON DELETE CASCADE on
        //    product_shipping_method.product_id clears the pivot rows for us.
        $deleted = 0;
        DB::transaction(function () use ($configIds, &$deleted) {
            $deleted = Product::withTrashed()
                ->whereIn('parent_id', $configIds)
                ->forceDelete();
        });

        $this->components->info("Hard-deleted {$deleted} variant rows.");
        $this->components->info('Remaining products: '.Product::withTrashed()->count());

        return self::SUCCESS;
    }

    /**
     * Dump the full database to storage/backups via mysqldump.
     * Returns the absolute path to the dump, or null on failure.
     */
    protected function backupDatabase(): ?string
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if (($config['driver'] ?? null) !== 'mysql') {
            $this->error("Backup only supports the mysql driver (got '".($config['driver'] ?? 'null')."').");

            return null;
        }

        $dir = storage_path('backups');
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            $this->error("Could not create backup directory: {$dir}");

            return null;
        }

        $fullPath = $dir.DIRECTORY_SEPARATOR.sprintf(
            '%s-%s.sql',
            $config['database'],
            now()->format('Y-m-d_His')
        );

        $process = new Process(
            [
                'mysqldump',
                '--host='.$config['host'],
                '--port='.$config['port'],
                '--user='.$config['username'],
                '--single-transaction',
                '--quick',
                '--no-tablespaces',
                '--result-file='.$fullPath,
                $config['database'],
            ],
            // Pass the password via the environment so it never appears in the
            // process list. Symfony inherits the parent env and overrides this key.
            env: ['MYSQL_PWD' => (string) ($config['password'] ?? '')],
            timeout: 600,
        );

        $this->line('Backing up database (mysqldump)…');
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('mysqldump failed: '.trim($process->getErrorOutput()));

            return null;
        }

        if (! is_file($fullPath) || filesize($fullPath) === 0) {
            $this->error('Backup file was not created or is empty.');

            return null;
        }

        return $fullPath;
    }
}
