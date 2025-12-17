<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\Order\ConfirmationNotification;
use App\Notifications\Order\InformationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

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
		// TODO: Generate invoice PDF (implement later)
		$invoice = null;

		// Send confirmation email to customer
		Notification::route('mail', $this->order->invoice_email)
			->notify(new ConfirmationNotification($this->order, $invoice));

		// Send information email to admin
		Notification::route('mail', config('mail.to'))
			->notify(new InformationNotification($this->order, $invoice));
	}
}
