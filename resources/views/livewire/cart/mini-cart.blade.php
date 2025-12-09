<div class="bg-olive fixed left-0 lg:offset-col-8-inset w-full lg:w-col-3-padded {{ $isLanding ? 'top-(--header-height-expanded)' : 'top-(--header-height-sm)' }} lg:top-(--header-height-lg) h-dvh lg:h-[calc(100dvh_-_var(--header-height-lg))] {{ $show ? 'opacity-100 z-20' : '-z-1 opacity-0' }}">
  <div class="flex flex-col h-full p-15 lg:p-20 text-white">

    <!-- Header -->
    <div class="flex items-center justify-between mb-40">

      <x-headings.h2 class="font-muoto-regular font-regular text-md leading-none">
        Warenkorb
      </x-headings.h2>

      <button
        wire:click="close"
        class="cursor-pointer"
        aria-label="Schließen">
        <x-icons.cross :size="'sm'" />
      </button>
    </div>

    <!-- Cart Items -->
    <div class="overflow-y-auto">
      @if(empty($cart['items']))
        <div class="text-center py-12">
          <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          <p>Ihr Warenkorb ist leer</p>
        </div>
      @else
        <div class="space-y-4">
          @foreach($cart['items'] as $item)
            @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

            <div 
              class="flex flex-col gap-y-40"
              wire:key="mini-cart-item-{{ $cartKey }}">

              <div class="flex items-center justify-between h-40 border-y border-white">
                
                <x-headings.h3 class="font-muoto-regular font-regular">
                  {{ $item['name'] }}
                </x-headings.h3>

                <button
                  wire:click="removeItem('{{ $cartKey }}')"
                  class=""
                  aria-label="Entfernen">
                </button>

                <span>
                  Fr. {{ number_format($item['price'] * $item['quantity'], 2, '.', '\'') }}
                </span>
              </div>

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
      @endif
    </div>

    <!-- Footer -->
    @if(!empty($cart['items']))
      <div class="flex flex-col gap-y-40 mt-40">

        <div class="flex items-center justify-between font-muoto-regular font-regular min-h-40 border-y border-y-white">
          <span>Total</span>
          <span>
            Fr. {{ number_format($cart['total'] ?? 0, 2, '.', '\'') }}
          </span>
        </div>

        <a
          href="{{ route('cart.index') }}"
          wire:click="close"
          class="flex items-center justify-center font-muoto-regular font-regular w-full border border-whiite h-40 cursor-pointer">
          <span>Zum Warenkorb</span>
        </a>
      </div>
    @endif

  </div>
</div>