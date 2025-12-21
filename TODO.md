# TODO

## ProcessOrderJob - Make Idempotent

**File:** `app/Jobs/ProcessOrderJob.php`

**Problem:** If the job fails partway through (e.g., after sending customer email but before admin email), retries will:
- Regenerate the PDF unnecessarily
- Send duplicate emails to the customer

**Solution:**
1. Add migration for `confirmation_email_sent` and `admin_email_sent` boolean columns on `orders` table
2. Check if PDF already exists before regenerating
3. Track email status on the order to prevent duplicates

**Implementation:**

```php
public function handle(): void
{
    // Generate invoice PDF only if not already generated
    $invoicePath = "invoices/invoice-{$this->order->order_number}.pdf";
    if (!Storage::disk('local')->exists($invoicePath)) {
        $invoicePath = (new GenerateInvoicePdf())->execute($this->order);
    }

    // Track email status on the order to prevent duplicates
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
```
