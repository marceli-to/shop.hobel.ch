<?php

namespace App\Livewire\Checkout;

use Livewire\Component;
use App\Actions\Cart\Get as GetCartAction;

class Summary extends Component
{
  public array $cart;
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

  public function render()
  {
    return view('livewire.checkout.summary');
  }
}
