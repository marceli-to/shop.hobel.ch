<x-layout.app :title="'Bestellbestätigung'">
  
  <x-grid.wrapper>

    <x-grid.span class="hidden lg:block lg:col-span-3 pl-20">

      <x-menu.checkout.menu />

    </x-grid.span>

    <x-grid.span class="lg:col-span-6 px-20 lg:px-0">

      <livewire:checkout.confirmation />

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>