<div 
  x-data="{ showCart: false, toggleCart() { this.showCart = !this.showCart; } }"
  @toggle-cart.window="toggleCart"
  @display-updated-cart.window="showCart = true"
  @hide-updated-cart.window="showCart = false"
  class="relative">
  @if (isset($cart['items']))
  <div 
    x-cloak 
    x-show="showCart" 
    x-on:click.outside="showCart = false"
    x-on:keyup.escape.window="showCart = false"
    class="fixed z-80 sm:z-60 h-full w-full max-w-[calc(100%_-_16px)] xs:max-w-[320px] bg-white top-0 right-0 px-16 border-l border-l-black">
    <div class="relative">
      <a 
        href="javascript:;"
        x-on:click="showCart = false">
        <x-icons.cross-small class="absolute top-20 left-0 w-16 h-auto" />
      </a>
      <div class="pt-140 xs:pt-85 lg:pt-100">
        <h2 
          class="font-europa-bold font-bold mt-4"
          wire:loading.class="hidden" 
          wire:target="removeCartItem">
          Warenkorb
        </h2>
        <div 
          wire:loading 
          wire:target="removeCartItem" 
          class="fixed z-60 h-full w-full xs:max-w-[320px] bg-white">
          <h2 
            class="font-europa-bold font-bold">
            Aktualisiere Warenkorb...
          </h2>
        </div>
        <div class="w-full mt-36 space-y-32">
          @foreach($cart['items'] as $item)
            <div class="cart-item-touch">
              <x-table.row class="font-europa-bold font-bold flex justify-between">
                <span>{{ $item['title'] }}</span>
                <span>{{ $item['quantity'] }}</span>
              </x-table.row>
              <x-table.row class="flex justify-between">
                <span>{{ $item['description'] }}</span>
                <span>{{ $item['price'] }}</span>
              </x-table.row>
              <x-table.row class="border-b border-b-black flex justify-center items-center">
                <a 
                  href="javascript:;"
                  wire:click="removeCartItem('{{ $item['uuid'] }}')"
                  class="block">
                  <x-icons.cross-small class="w-12 h-auto" />
                </a>
              </x-table.row>
            </div>
            <div class="cart-item-no-touch">
              <a 
                href="javascript:;" 
                wire:click="removeCartItem('{{ $item['uuid'] }}')" 
                class="block hover:text-flame group relative" 
                title="Produkt entfernen">
                <x-icons.cross-small 
                  class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-16 -mt-1 h-auto opacity-0 group-hover:opacity-100 bg-white" />
                <x-table.row class="font-europa-bold font-bold flex justify-between group-hover:border-flame">
                  <span>{{ $item['title'] }}</span>
                  <span>{{ $item['quantity'] }}</span>
                </x-table.row>
                <x-table.row class="border-b border-b-black flex justify-between group-hover:border-flame">
                  <span>{{ $item['description'] }}</span>
                  <span>{{ $item['price'] }}</span>
                </x-table.row>
              </a>
            </div>
          @endforeach
          <x-table.row class="font-europa-bold font-bold flex justify-between border-y border-y-black">
            <span>Total</span>
            <span>{{ number_format($cart['total'], 2, '.', '') }}</span>
          </x-table.row>
          <x-table.row class="border-none">
            <x-buttons.primary route="{{ route('order.overview') }}" label="Erwerben" class="font-europa-bold font-bold " />
          </x-table.row>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
