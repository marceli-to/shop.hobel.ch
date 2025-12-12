<x-layout.app :title="'Bestellung abschliessen'">
  
  <x-grid.wrapper>

    <x-grid.span class="lg:col-span-4 lg:col-start-4">

      @if(session('error'))
        <div class="mb-20 p-20 bg-red-50 border border-red-200 text-red-700">
          {{ session('error') }}
        </div>
      @endif

      <div class="flex flex-col gap-y-40">

        <!-- Header -->
        <x-layout.row class="border-t-0">
          <x-headings.h2>Zusammenfassung</x-headings.h2>
        </x-layout.row>

        <!-- Items -->
        @foreach($cart['items'] as $item)
          <div>
            <x-layout.row class="justify-between">
              <span class="font-sans">{{ $item['name'] }}</span>
              <x-cart.money :amount="$item['price'] * $item['quantity']" />
            </x-layout.row>

            @if($item['description'])
              <x-layout.row>
                <span class="text-gray-600">{{ $item['description'] }}</span>
              </x-layout.row>
            @endif

            <x-layout.row class="justify-between">
              <span>Anzahl: {{ $item['quantity'] }}</span>
            </x-layout.row>

            @if(!empty($item['shipping_price']) && $item['shipping_price'] > 0)
              <x-layout.row class="justify-between border-b">
                <span>Versand</span>
                <x-cart.money :amount="$item['shipping_price']" />
              </x-layout.row>
            @endif
          </div>
        @endforeach

        <!-- Totals -->
        <div>
          <x-layout.row class="justify-between">
            <span>Netto</span>
            <x-cart.money :amount="$cart['subtotal'] ?? $cart['total']" />
          </x-layout.row>

          <x-layout.row class="justify-between">
            <span>MwSt. {{ config('invoice.tax_rate') }}%</span>
            <x-cart.money :amount="$cart['tax'] ?? 0" />
          </x-layout.row>

          <x-layout.row class="justify-between font-sans border-b">
            <span>Total</span>
            <x-cart.money :amount="$cart['total']" />
          </x-layout.row>
        </div>

        <!-- Payment Button -->
        <div class="mt-20">
          <form action="{{ route('payment.initiate') }}" method="POST">
            @csrf
            <button 
              type="submit"
              class="w-full py-15 px-20 bg-black text-white font-sans text-center hover:bg-gray-800 transition-colors cursor-pointer">
              Weiter zur Zahlung
            </button>
          </form>

          <a 
            href="{{ route('cart.index') }}"
            class="block mt-15 text-center text-gray-600 hover:text-black transition-colors">
            Zurück zum Warenkorb
          </a>
        </div>

      </div>

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
