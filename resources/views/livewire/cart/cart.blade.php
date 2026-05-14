<div>

  <!-- Empty -->
  @if (empty($cart['items']))
    <x-layout.row class="border-t-0 items-start">
      <span>Ihr Warenkorb ist leer</span>
    </x-layout.row>
  @endif

  <!-- Items/Totals -->
  @if(!empty($cart['items']))
    <div class="lg:grid lg:grid-cols-6 gap-x-20">

      <!-- Cart Details -->
      <div class="lg:col-span-4 flex flex-col gap-y-40">

        <!-- Items -->
        @foreach($cart['items'] as $item)

          @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

          <div
            class="flex flex-col gap-y-40"
            wire:key="cart-item-{{ $cartKey }}">

            <div>

              <!-- Product header row: Name, X button, Price -->
              <x-layout.row class="justify-between gap-12 border-t {{ empty($item['label']) && empty($item['configuration']) ? 'border-b' : '' }} relative">

                <button
                  wire:click="removeItem('{{ $cartKey }}')"
                  class="group flex items-center justify-center cursor-pointer w-12 h-40"
                  aria-label="Artikel entfernen">
                  <x-icons.cross :size="'sm'" class="group-hover:rotate-180 transition-all" />
                </button>

                <x-headings.h3 class="font-sans w-full truncate">
                  {{ $item['name'] }}
                </x-headings.h3>

                <x-cart.money :amount="$item['price'] * $item['quantity']" />

              </x-layout.row>

              @if(!empty($item['label']))
                <x-layout.row class="border-b">
                  {{ $item['label'] }}
                </x-layout.row>
              @endif

              <!-- Configuration Details -->
              @if(!empty($item['configuration']))
                <x-layout.row>
                  <span>{{ $item['configuration'] }}</span>
                </x-layout.row>
              @endif
            </div>

            <!-- Shipping options -->
            @if(!empty($item['shipping_methods']))
              <div>
                @foreach($item['shipping_methods'] as $index => $method)
                  <x-layout.row class="{{ $loop->last ? 'border-b' : '' }}">
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
                  </x-layout.row>
                @endforeach
              </div>
            @else
              <x-layout.row class="border-b">
                <span>Bitte <a href="mailto:info@hobel.ch" aria-label="Kontaktieren Sie uns per E-Mail" class="underline underline-offset-2 decoration-1 hover:no-underline">kontaktieren Sie uns</a> für Abholung oder Versand</span>
              </x-layout.row>
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

          <x-layout.row class="justify-between">
            <span>Netto</span>
            <x-cart.money :amount="$cart['subtotal'] ?? $cart['total']" />
          </x-layout.row>

          @if(collect($cart['items'])->contains(fn($item) => $item['is_shipping'] ?? false))
            <x-layout.row class="justify-between">
              <span>Versand</span>
              @if(($cart['shipping'] ?? 0) > 0)
                <x-cart.money :amount="$cart['shipping']" />
              @else
                <span>Kostenlos</span>
              @endif
            </x-layout.row>
          @endif

          <x-layout.row class="justify-between">
            <span>MwSt. {{ config('invoice.tax_rate') }}%</span>
            <x-cart.money :amount="$cart['tax'] ?? 0" />
          </x-layout.row>

          <x-layout.row class="justify-between font-sans border-b">
            <span>Total</span>
            <x-cart.money :amount="$cart['total']" />
          </x-layout.row>

        </div>

        <x-form.button route="{{ route('page.checkout.invoice-address') }}" :title="'Rechnungsadresse'" />

      </div>

      <!-- Cart Images (desktop only) -->
      <div class="hidden lg:flex lg:col-span-2 flex-col gap-y-20 pl-half-col">
        @foreach($cart['items'] as $item)
          @if($item['image'])
            <div wire:key="cart-image-{{ $item['cart_key'] ?? $item['uuid'] }}">
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
  @endif

</div>
