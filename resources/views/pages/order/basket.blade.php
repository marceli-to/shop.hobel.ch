<x-layout.app :title="'Bestellung'">
  
  <x-grid.wrapper>

    <x-grid.span class="hidden lg:block lg:col-span-3 pl-20">

      <nav class="text-md">
        <ul>
          <li>
            <a href="">Warenkorb</a>
          </li>
          <li>
            <a href="">Rechnungsadresse</a>
          </li>
          <li>
            <a href="">Lieferadresse</a>
          </li>
          <li>
            <a href="">Zahlung</a>
          </li>
        </ul>
      </nav>

    </x-grid.span>

    <x-grid.span class="lg:col-span-4">

      <livewire:cart.cart />

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>