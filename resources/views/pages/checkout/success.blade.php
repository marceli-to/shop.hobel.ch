<x-layout.app :title="'Bestellung erfolgreich'">
  
  <x-grid.wrapper>

    <x-grid.span class="lg:col-span-4 lg:col-start-4">

      <div class="flex flex-col gap-y-40">

        <!-- Success Message -->
        <div class="text-center">
          <div class="mb-20">
            <svg class="w-60 h-60 mx-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <x-headings.h2>Vielen Dank für Ihre Bestellung!</x-headings.h2>
          <p class="mt-10 text-gray-600">Ihre Zahlung wurde erfolgreich verarbeitet.</p>
        </div>

        <!-- Order Reference -->
        <x-layout.row class="justify-center">
          <span>Bestellreferenz: <strong>{{ $reference }}</strong></span>
        </x-layout.row>

        <!-- Order Summary -->
        @if(!empty($cart['items']))
          <div>
            <x-layout.row class="border-t-0">
              <span class="font-sans">Bestellübersicht</span>
            </x-layout.row>

            @foreach($cart['items'] as $item)
              <x-layout.row class="justify-between">
                <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                <x-cart.money :amount="$item['price'] * $item['quantity']" />
              </x-layout.row>
            @endforeach

            <x-layout.row class="justify-between font-sans border-b">
              <span>Total</span>
              <x-cart.money :amount="$cart['total']" />
            </x-layout.row>
          </div>
        @endif

        <!-- Continue Shopping -->
        <div class="text-center">
          <a 
            href="{{ route('page.landing') }}"
            class="inline-block py-15 px-30 bg-black text-white font-sans hover:bg-gray-800 transition-colors">
            Weiter einkaufen
          </a>
        </div>

      </div>

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
