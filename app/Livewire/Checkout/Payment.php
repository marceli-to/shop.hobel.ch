<?php
namespace App\Livewire\Checkout;
use Livewire\Component;

class Payment extends Component
{
  public string $payment_method = 'creditcard';

  protected $rules = [
    'payment_method' => 'required|in:creditcard,invoice',
  ];

  public function mount(): void
  {
    $method = session()->get('payment_method');
    
    if ($method) {
      $this->payment_method = $method;
    }
  }

  public function save(): void
  {
    $this->validate();

    session()->put('payment_method', $this->payment_method);

    $this->redirect(route('page.checkout.summary'));
  }

  public function render()
  {
    return view('livewire.checkout.payment');
  }
}
