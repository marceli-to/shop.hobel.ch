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

              <x-misc.row class="justify-between border-y !border-white relative">
                
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

              <!-- Configuration Details -->
              {{-- @if(!empty($item['configuration']))
                <div class="mt-1 space-y-0.5">
                  @foreach($item['configuration'] as $config)
                    <div class="text-xs text-gray-500">
                      {{ $config['label'] }}
                    </div>
                  @endforeach
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

        <x-misc.row class="font-sans justify-between border-y !border-white">
          <span>Total</span>
          <x-cart.money :amount="$cart['total']" />
        </x-misc.row>

        <a
          href="{{ route('page.order.basket') }}"
          wire:click="close"
          class="flex items-center justify-center font-sans w-full border border-white h-40 cursor-pointer">
          <span>Zum Warenkorb</span>
        </a>

      </div>
    @endif

  </div>
  
</div>