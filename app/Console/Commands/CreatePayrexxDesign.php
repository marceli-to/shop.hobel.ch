<?php

namespace App\Console\Commands;

use App\Services\PayrexxService;
use Illuminate\Console\Command;
use Payrexx\PayrexxException;

class CreatePayrexxDesign extends Command
{
    protected $signature = 'payrexx:create-design 
                            {--name=Shop Design : Name for the design}
                            {--default : Set as default design}';

    protected $description = 'Create a custom Payrexx payment page design';

    public function handle(PayrexxService $payrexxService): int
    {
        $this->info('Creating Payrexx design...');

        try {
            $design = $payrexxService->createDesign([
                'name' => $this->option('name'),
                'default' => $this->option('default'),
            ]);

            $this->newLine();
            $this->info('✓ Design created successfully!');
            $this->table(['Property', 'Value'], [
                ['ID', $design['id'] ?? 'N/A'],
                ['Name', $design['name']],
            ]);

            if ($design['id']) {
                $this->newLine();
                $this->line('Add this to your <comment>.env</comment> file:');
                $this->newLine();
                $this->line("  <info>PAYREXX_DESIGN_ID={$design['id']}</info>");
                $this->newLine();
            } else {
                $this->newLine();
                $this->warn('No design ID returned. Check the Payrexx dashboard for the design ID.');
                $this->line('Check logs for full response: storage/logs/laravel.log');
                $this->newLine();
            }

            return Command::SUCCESS;
        } catch (PayrexxException $e) {
            $this->error('Failed to create design: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
