<?php

namespace App\Jobs;

use App\Actions\Order\GenerateInvoicePdf;
use App\Models\Order;
use App\Notifications\Order\ConfirmationNotification;
use App\Notifications\Order\InformationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProcessOrderJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public int $tries = 3;

	public int $backoff = 60;

	public function __construct(
		public Order $order
	) {}

	public function handle(): void
	{
		$invoicePath = "invoices/invoice-{$this->order->order_number}.pdf";
		if (!Storage::disk('local')->exists($invoicePath)) {
			$invoicePath = (new GenerateInvoicePdf())->execute($this->order);
		}

		if (!$this->order->confirmation_email_sent) {
			Notification::route('mail', $this->order->invoice_email)
				->notify(new ConfirmationNotification($this->order, $invoicePath));
			$this->order->update(['confirmation_email_sent' => true]);
		}

		if (!$this->order->admin_email_sent) {
			Notification::route('mail', config('mail.to'))
				->notify(new InformationNotification($this->order, $invoicePath));
			$this->order->update(['admin_email_sent' => true]);
		}
	}
}
