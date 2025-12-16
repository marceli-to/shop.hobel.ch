<?php
namespace App\Livewire\Checkout;
use Livewire\Component;
use App\Actions\Cart\Update as UpdateCart;

class DeliveryAddress extends Component
{
  public string $salutation = 'Dr.';
  public string $firstname = 'Pierluigi';
  public string $lastname = 'Menefregista';
  public string $street = 'Marktgasse';
  public string $street_number = '3';
  public string $zip = '8765';
  public string $city = 'Esempione';
  public string $country = 'Schweiz';

  protected $rules = [
    'firstname' => 'required|string|max:255',
    'lastname' => 'required|string|max:255',
    'street' => 'required|string|max:255',
    'street_number' => 'nullable|string|max:50',
    'zip' => 'required|string|max:20',
    'city' => 'required|string|max:255',
    'country' => 'required|string|max:255',
    'salutation' => 'nullable|string|max:50',
  ];

  protected $messages = [
    'firstname.required' => 'Bitte geben Sie Ihren Vornamen ein.',
    'lastname.required' => 'Bitte geben Sie Ihren Nachnamen ein.',
    'street.required' => 'Bitte geben Sie Ihre Strasse ein.',
    'zip.required' => 'Bitte geben Sie Ihre PLZ ein.',
    'city.required' => 'Bitte geben Sie Ihren Ort ein.',
    'country.required' => 'Bitte geben Sie Ihr Land ein.',
  ];

  public function mount(): void
  {
    $address = session()->get('delivery_address', []);
    
    if (!empty($address)) {
      $this->salutation = $address['salutation'] ?? $this->salutation;
      $this->firstname = $address['firstname'] ?? $this->firstname;
      $this->lastname = $address['lastname'] ?? $this->lastname;
      $this->street = $address['street'] ?? $this->street;
      $this->street_number = $address['street_number'] ?? $this->street_number;
      $this->zip = $address['zip'] ?? $this->zip;
      $this->city = $address['city'] ?? $this->city;
      $this->country = $address['country'] ?? $this->country;
    }
  }

  public function save(): void
  {
    $this->validate();

    session()->put('delivery_address', [
      'salutation' => $this->salutation,
      'firstname' => $this->firstname,
      'lastname' => $this->lastname,
      'street' => $this->street,
      'street_number' => $this->street_number,
      'zip' => $this->zip,
      'city' => $this->city,
      'country' => $this->country,
    ]);

    (new UpdateCart())->execute(['order_step' => 3]);

    $this->redirect(route('page.checkout.payment'));
  }

  public function render()
  {
    return view('livewire.checkout.delivery-address');
  }
}
