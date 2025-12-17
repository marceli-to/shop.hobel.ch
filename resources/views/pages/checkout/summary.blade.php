<x-layout.app :title="'Bestellung abschliessen'">
  
  <x-grid.wrapper>

    <x-grid.span class="hidden lg:block lg:col-span-3 pl-20">

      <x-menu.checkout.menu />

    </x-grid.span>

    <x-grid.span class="lg:col-span-6 px-20 lg:px-0">

      @if(session('error'))
        <div class="mb-20 p-20 bg-red-50 border border-red-200 text-red-700">
          {{ session('error') }}
        </div>
      @endif

      <livewire:checkout.summary />

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
