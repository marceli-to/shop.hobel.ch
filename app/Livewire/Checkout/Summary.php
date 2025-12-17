<?php

namespace App\Livewire\Checkout;

use Livewire\Component;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Order\Create as CreateOrderAction;
use App\Actions\Order\Finalize as FinalizeOrderAction;
use App\Actions\Order\GenerateOrderNumber;
use App\Services\PayrexxService;

class Summary extends Component
{
  public array $cart;
  public array $invoice_address;
  public array $delivery_address;
  public string $payment_method;
  public bool $terms_accepted = false;

  protected $rules = [
    'terms_accepted' => 'accepted',
  ];

  protected $messages = [
    'terms_accepted.accepted' => 'Bitte akzeptieren Sie die AGB und Datenschutzerklärung.',
  ];

  public function mount(): void
  {
    $this->cart = (new GetCartAction())->execute();
    $this->calculateTotals();
    $this->invoice_address = session()->get('invoice_address', []);
    $this->delivery_address = session()->get('delivery_address', []);
    $this->payment_method = session()->get('payment_method', 'creditcard');
  }

  private function calculateTotals(): void
  {
    $taxRate = config('invoice.tax_rate') / 100;
    $items = collect($this->cart['items'] ?? []);
    
    $subtotal = $items->sum(fn($item) => $item['price'] * $item['quantity']);
    $shipping = $items->sum(fn($item) => $item['shipping_price'] ?? 0);
    
    $this->cart['subtotal'] = $subtotal + $shipping;
    $this->cart['shipping'] = $shipping;
    $this->cart['tax'] = $this->cart['subtotal'] * $taxRate;
    $this->cart['total'] = $this->cart['subtotal'] + $this->cart['tax'];
  }

  public function placeOrder(): void
  {
    $this->validate();

    if ($this->payment_method === 'invoice') {
      $this->processInvoiceOrder();
    } else {
      $this->processCreditCardOrder();
    }
  }

  private function processInvoiceOrder(): void
  {
    // Create order in database
    (new CreateOrderAction())->execute(
      $this->cart,
      $this->invoice_address,
      $this->delivery_address,
      'invoice'
    );

    // Finalize order (send emails, clear cart, etc.)
    (new FinalizeOrderAction())->execute();

    $this->redirect(route('page.checkout.confirmation'));
  }

  private function processCreditCardOrder(): void
  {
    // Generate unique order number as reference ID
    $referenceId = (new GenerateOrderNumber())->execute();

    // Store reference in session for later verification
    session()->put('payment_reference', $referenceId);

    try {
      $payrexxService = app(PayrexxService::class);
      $gateway = $payrexxService->createGateway($this->cart, $referenceId);

      // Store gateway ID for verification
      session()->put('payment_gateway_id', $gateway['id']);

      // Redirect to Payrexx payment page
      $this->redirect($gateway['link']);

    } catch (\Exception $e) {
      session()->flash('error', 'Zahlung konnte nicht initialisiert werden. Bitte versuchen Sie es erneut.');
    }
  }

  public function render()
  {
    return view('livewire.checkout.summary');
  }
}
