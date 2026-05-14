<div 
  class="
    bg-olive 
    fixed 
    left-0 
    lg:offset-col-8-inset 
    w-full 
    h-dvh 
    lg:h-[calc(100dvh_-_var(--header-height-lg))] 
    lg:w-col-3-padded 
    {{ $isLanding ? 'top-(--header-height-expanded) lg:top-(--header-height-lg)' : 'top-(--header-height-sm) lg:top-(--header-height-lg)' }} 
    {{ $show ? 'opacity-100 z-30' : '-z-1 opacity-0' }}">

  <div class="flex flex-col h-full p-15 lg:p-20 text-white">

    <!-- Header -->
    <div class="flex items-center justify-between mb-40">

      <x-headings.h2 class="font-sans text-md leading-none">
        Warenkorb
      </x-headings.h2>

      <button
        wire:click="close"
        class="cursor-pointer"
        aria-label="Schließen">
        <x-icons.cross :size="'md'" />
      </button>
    </div>

    <!-- Empty -->
    @if (empty($cart['items']))
      <p>Ihr Warenkorb ist leer</p>
    @endif

    <!-- Items -->
    @if(!empty($cart['items']))
      <div class="overflow-y-auto">
        <div class="flex flex-col gap-y-40">

          @foreach($cart['items'] as $item)

            @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

            <div 
              class="flex flex-col gap-y-40"
              wire:key="mini-cart-item-{{ $cartKey }}">

              <x-layout.row class="justify-between gap-12 border-y !border-white relative">

                <button
                  wire:click="removeItem('{{ $cartKey }}')"
                  class="group flex items-center justify-center cursor-pointer w-12 h-40"
                  aria-label="Artikel entfernen">
                  <x-icons.cross :size="'sm'" class="group-hover:rotate-180 transition-all" />
                </button>

                <x-headings.h3 class="font-sans w-full truncate">
                  {{ $item['name'] }}
                  @if(!empty($item['label']))
                    <span class="inline font-normal text-sm">{{ $item['label'] }}</span>
                  @endif
                </x-headings.h3>

                <x-cart.money :amount="$item['price'] * $item['quantity']" :show-currency="false" />

              </x-layout.row>

              <!-- Configuration Details -->
              {{-- @if(!empty($item['configuration']))
                <div class="mt-1 text-xs text-gray-500">
                  {{ $item['configuration'] }}
                </div>
              @endif --}}

              <!-- Quantity Selector -->
              <livewire:cart.button
                :productUuid="$item['uuid']"
                :cartKey="$cartKey"
                :showButton="false"
                :key="'cart-mini-button-' . $cartKey"
                class="border-white" />

            </div>  
          @endforeach
        </div>
      </div>
    @endif

    <!-- Footer -->
    @if(!empty($cart['items']))
      <div class="flex flex-col gap-y-40 mt-40">

        <x-layout.row class="font-sans justify-between border-y !border-white">
          <span>Total</span>
          <x-cart.money :amount="$cart['subtotal']" :show-currency="false" />
        </x-layout.row>

        <a
          href="{{ route('page.checkout.basket') }}"
          wire:click="close"
          class="flex items-center justify-center font-sans w-full border border-white h-40 cursor-pointer">
          <span>Zum Warenkorb</span>
        </a>

        <span class="text-xxs">Alle Preise in CHF</span>

      </div>
    @endif

  </div>
  
</div>