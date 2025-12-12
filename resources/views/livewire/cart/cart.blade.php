<div>

  <!-- Empty -->
  @if (empty($cart['items']))
    <x-misc.row class="border-t-0 items-start">
      <span>Ihr Warenkorb ist leer</span>
    </x-misc.row>
  @endif

  <!-- Items/Totals -->
  @if(!empty($cart['items']))
    <div class="flex flex-col gap-y-40">

      <!-- Items -->
      @foreach($cart['items'] as $item)

        @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

        <div 
          class="flex flex-col gap-y-40"
          wire:key="cart-item-{{ $cartKey }}">
          
          <div>

            <!-- Product header row: Name, X button, Price -->
            <x-misc.row class="justify-between border-t relative">
              
              <x-headings.h3 class="font-sans">
                {{ $item['name'] }}
              </x-headings.h3>

              <button
                wire:click="removeItem('{{ $cartKey }}')"
                class="group flex items-center justify-center cursor-pointer w-40 h-40 absolute left-1/2 -translate-x-1/2"
                aria-label="Artikel entfernen">
                <x-icons.cross :size="'sm'" class="group-hover:rotate-180 transition-all" />
              </button>

              <x-cart.money :amount="$item['price'] * $item['quantity']" />

            </x-misc.row>

            <!-- Description -->
            @if($item['description'])
              <x-misc.row class="border-b">
                <span>{{ $item['description'] }}</span>
              </x-misc.row>
            @endif

            <!-- Configuration Details -->
            @if(!empty($item['configuration']))
              @foreach($item['configuration'] as $config)
                <x-misc.row>
                  <span>{{ $config['label'] }}</span>
                </x-misc.row>
              @endforeach
            @endif
          </div>

          <!-- Shipping options -->
          @if(!empty($item['shipping_methods']))
            <div>
              @foreach($item['shipping_methods'] as $index => $method)
                <x-misc.row class="justify-between {{ $loop->last ? 'border-b' : '' }}">
                  <label class="flex items-center gap-x-20 cursor-pointer">
                    <input 
                      type="radio" 
                      name="shipping_{{ $cartKey }}" 
                      value="{{ $method['id'] }}"
                      wire:click="updateShipping('{{ $cartKey }}', {{ $method['id'] }})"
                      {{ ($item['selected_shipping'] ?? null) == $method['id'] ? 'checked' : '' }}
                      class="peer sr-only"
                    >
                    <x-icons.radio-unchecked class="peer-checked:hidden" />
                    <x-icons.radio-checked class="hidden peer-checked:block" />
                    <span>{{ $method['name'] }}</span>
                  </label>
                  <x-cart.money :amount="$method['price']" />
                </x-misc.row>
              @endforeach
            </div>
          @endif
          
          <!-- Quantity Selector -->
          <livewire:cart.button
            :productUuid="$item['uuid']"
            :cartKey="$cartKey"
            :showButton="false"
            :key="'cart-page-button-' . $cartKey" />

        </div>  
      @endforeach

      <!-- Totals -->
      <div>

        <x-misc.row class="justify-between">
          <span>Netto</span>
          <x-cart.money :amount="$cart['subtotal'] ?? $cart['total']" />
        </x-misc.row>

        <x-misc.row class="justify-between">
          <span>MwSt. {{ config('invoice.tax_rate') }}%</span>
          <x-cart.money :amount="$cart['tax'] ?? 0" />
        </x-misc.row>

        <x-misc.row class="justify-between font-sans border-b">
          <span>Total</span>
          <x-cart.money :amount="$cart['total']" />
        </x-misc.row>

      </div>


      <x-form.button route="{{ route('page.checkout.invoice-address') }}" :title="'Rechnungsadresse'" />

    </div>
  @endif

</div>
