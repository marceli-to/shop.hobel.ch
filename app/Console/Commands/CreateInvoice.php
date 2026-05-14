<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use App\Models\Order;
use App\Services\Pdf\Pdf;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderConfirmationNotification;

class CreateInvoice extends Command
{
  use ConfirmableTrait;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'create:invoice {--force : Force the operation to run when in production}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create an invoice as PDF';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    if (! $this->confirmToProceed()) {
      return Command::FAILURE;
    }

    $order = Order::with('products')->latest()->first();
    $invoice = (new Pdf())->create([
      'data' => $order,
      'view' => 'invoice',
      'name' => config('invoice.invoice_prefix') . $order->uuid
    ]);
    
    try {
      Notification::route('mail', env('MAIL_TO'))
        ->notify(
          new OrderConfirmationNotification($order, $invoice)
        );
    } 
    catch (\Exception $e) {
      \Log::error($e->getMessage());
    }
    $this->info('The command was successful!');
  }
}
