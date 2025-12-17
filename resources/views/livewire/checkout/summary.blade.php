<div>

  <div class="lg:grid lg:grid-cols-6 gap-x-20">

    <!-- Summary Details -->
    <div class="lg:col-span-4 flex flex-col gap-y-40">

      <!-- Items -->
      @foreach($cart['items'] as $item)
        @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

        <div wire:key="summary-item-{{ $cartKey }}">

          <!-- Product header row: Name and Price -->
          <x-layout.row class="justify-between">
            <x-headings.h3 class="font-sans">
              {{ $item['name'] }}
            </x-headings.h3>
            <x-cart.money :amount="$item['price'] * $item['quantity']" />
          </x-layout.row>

          <!-- Description -->
          @if($item['description'])
            <x-layout.row>
              <span>{{ $item['description'] }}</span>
            </x-layout.row>
          @endif

          <!-- Shipping -->
          @if(!empty($item['shipping_price']))
            <x-layout.row class="justify-between border-b">
              <span>{{ $item['shipping_name'] ?? 'Versand' }}</span>
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
          <span>MwSt.</span>
          <x-cart.money :amount="$cart['tax'] ?? 0" />
        </x-layout.row>

        <x-layout.row class="justify-between font-sans border-b">
          <span>Total</span>
          <x-cart.money :amount="$cart['total']" />
        </x-layout.row>

      </div>

      <!-- Addresses -->
      <div class="flex flex-col gap-y-40">
        <x-layout.row class="justify-between border-b">
          <span class="font-sans">Rechnungsadresse</span>
          <x-icons.chevron-right size="sm" class="rotate-90" />
        </x-layout.row>

        <x-layout.row class="justify-between border-b">
          <span class="font-sans">Lieferadresse</span>
          <x-icons.chevron-right size="sm" class="rotate-90" />
        </x-layout.row>
      </div>

      <!-- Payment Method -->
      <div>
        <x-layout.row class="border-b">
          <label class="flex items-center cursor-pointer w-full h-80">
            <span class="block w-35">
              <x-icons.radio-checked />
            </span>
            <span class="flex items-center gap-x-20">
              @if($payment_method === 'invoice')
                <x-icons.payment-invoice />
                <span>Rechnung</span>
              @else
                <x-icons.payment-creditcard class="text-black" />
                <span>Kreditkarte</span>
              @endif
            </span>
          </label>
        </x-layout.row>
      </div>

      <!-- Terms and Submit -->
      <div>
        <x-layout.row class="border-b">
          <label class="flex items-center gap-x-20 cursor-pointer w-full h-80">
            <span class="block w-35">
              <input 
                type="checkbox" 
                wire:model="terms_accepted"
                class="peer sr-only">
              <x-icons.radio-unchecked class="peer-checked:hidden" />
              <x-icons.radio-checked class="hidden peer-checked:block" />
            </span>
            <span>Hiermit akzeptiere ich die <a href="/agb" class="font-sans" target="_blank">Allgemeinen Geschäftsbedingungen</a> und die <a href="/datenschutz" class="font-sans" target="_blank">Datenschutzerklärung</a>.</span>
          </label>
        </x-layout.row>

        @error('terms_accepted')
          <x-layout.row class="text-red-600">
            {{ $message }}
          </x-layout.row>
        @enderror

        <form action="{{ route('payment.initiate') }}" method="POST" class="mt-40">
          @csrf
          <x-form.button type="submit" :title="'Bestellen'" />
        </form>
      </div>

    </div>

    <!-- Cart Images (desktop only) -->
    <div class="hidden lg:flex lg:col-span-2 flex-col gap-y-20 pl-half-col">
      @foreach($cart['items'] as $item)
        @if($item['image'])
          <div wire:key="summary-image-{{ $item['cart_key'] ?? $item['uuid'] }}">
            <x-media.image
              :src="$item['image']"
              :alt="$item['name']"
              :width="300"
              :height="300"
              fit="contain"
              class="w-full"
            />
          </div>
        @endif
      @endforeach
    </div>

  </div>

</div>
