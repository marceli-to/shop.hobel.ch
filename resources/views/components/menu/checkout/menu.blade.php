@php
  $cart = (new \App\Actions\Cart\Get())->execute();
  $currentStep = $cart['order_step'] ?? 0;
  $isPaid = $cart['is_paid'] ?? false;
  $hasItems = !empty($cart['items']);
  $currentRoute = request()->route()->getName();
  
  $steps = [
    ['route' => 'page.checkout.basket', 'label' => 'Warenkorb', 'requiredStep' => 0],
    ['route' => 'page.checkout.invoice-address', 'label' => 'Rechnungsadresse', 'requiredStep' => 1],
    ['route' => 'page.checkout.delivery-address', 'label' => 'Lieferadresse', 'requiredStep' => 2],
    ['route' => 'page.checkout.payment', 'label' => 'Zahlung', 'requiredStep' => 3],
    ['route' => 'page.checkout.summary', 'label' => 'Zusammenfassung', 'requiredStep' => 4],
    ['route' => 'page.checkout.confirmation', 'label' => 'Bestätigung', 'requiredStep' => 5, 'requiresPaid' => true],
  ];
@endphp

<nav class="text-md -mt-6">
  <ul class="flex flex-col gap-4">
    @foreach($steps as $step)
      @php
        $isActive = $currentRoute === $step['route'];
        $requiresCart = $step['requiredStep'] > 0;
        $canAccess = $currentStep >= $step['requiredStep'] && (!$requiresCart || $hasItems) && (!($step['requiresPaid'] ?? false) || $isPaid);
      @endphp
      <li>
        @if($canAccess)
          <a href="{{ route($step['route']) }}" class="{{ $isActive ? '' : 'text-ash' }}">
            {{ $step['label'] }}
          </a>
        @else
          <span class="{{ $isActive ? 'text-black' : 'text-ash' }}">
            {{ $step['label'] }}
          </span>
        @endif
      </li>
    @endforeach
  </ul>
</nav>
