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
                ['ID', $design['id']],
                ['Name', $design['name']],
            ]);

            $this->newLine();
            $this->line('Add this to your <comment>.env</comment> file:');
            $this->newLine();
            $this->line("  <info>PAYREXX_DESIGN_ID={$design['id']}</info>");
            $this->newLine();

            return Command::SUCCESS;
        } catch (PayrexxException $e) {
            $this->error('Failed to create design: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
