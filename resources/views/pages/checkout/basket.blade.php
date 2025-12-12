<x-layout.app :title="'Warenkorb'">
  
  <x-grid.wrapper>

    <x-grid.span class="hidden lg:block lg:col-span-3 pl-20">

      <x-menu.checkout.menu />

    </x-grid.span>

    <x-grid.span class="lg:col-span-4 px-20 lg:px-0">

      <livewire:cart.cart />

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>