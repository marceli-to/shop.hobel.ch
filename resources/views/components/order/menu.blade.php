@php
$routes = [
  'overview' => [
    'label' => 'Warenkorb',
    'order_step' => 1
  ],
  'invoice-address' => [
    'label' => 'Rechnungsadresse',
    'order_step' => 2
  ],
  'shipping-address' => [
    'label' => 'Lieferadresse',
    'order_step' => 3
  ],
  'payment' => [
    'label' => 'Zahlung',
    'order_step' => 4
  ],
  'summary' => [
    'label' => 'Zusammenfassung',
    'order_step' => 5
  ],
  'confirmation' => [
    'label' => 'Bestätigung',
    'order_step' => 6
  ],
];  
@endphp
@props(['order_step' => 1])
<nav>
  <ul class="divide-y divide-black border-y border-y-black">
    @foreach ($routes as $key => $route)
      <li>
        @if ($order_step == 6)
          <span class="font-europa-bold font-bold w-full min-h-32 flex items-center leading-none space-x-6">
            {{ $route['label']  }}
          </span>
        @else
          @if ($order_step >= $route['order_step'] || request()->routeIs('order.' . $key))
            <a 
              href="{{ route('order.' . $key) }}"
              class="w-full min-h-32 font-europa-bold font-bold flex items-center leading-none space-x-6 hover:text-flame group-hover:text-flame transition-all {{ request()->routeIs('order.' . $key) ? '!text-flame' : '' }}"
              title="{{ $route['label'] }}">
              {{ $route['label']  }}
            </a>
          @else
            <span class="w-full min-h-32 flex items-center leading-none space-x-6">
              {{ $route['label']  }}
            </span>
          @endif
        @endif
      </li>
    @endforeach
  </ul>
</nav>