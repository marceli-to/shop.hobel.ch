<?php

namespace App\Console\Commands;

use App\Actions\Order\GenerateInvoicePdf;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class PreviewOrderPdf extends Command
{
    use ConfirmableTrait;

    protected $signature = 'pdf:preview
                            {order : The order ID or UUID}
                            {--lambda : Generate using AWS Lambda instead of local}
                            {--force : Force the operation to run when in production}';

    protected $description = 'Generate an invoice PDF for an order';

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $identifier = $this->argument('order');

        $order = Order::with('items')
            ->where('id', $identifier)
            ->orWhere('uuid', $identifier)
            ->first();

        if (!$order) {
            $this->error("Order not found: {$identifier}");
            return Command::FAILURE;
        }

        $this->info("Generating PDF for order: {$order->order_number}");

        if ($this->option('lambda')) {
            $this->info('Using AWS Lambda...');
            app()->instance('force_lambda_pdf', true);
        } else {
            $this->info('Using local Puppeteer...');
        }

        $path = (new GenerateInvoicePdf())->execute($order);

        $this->info("PDF saved to: storage/app/{$path}");

        return Command::SUCCESS;
    }
}
