<x-layout.app :title="'Zahlung wird verarbeitet'">
  
  <x-grid.wrapper>

    <x-grid.span class="lg:col-span-4 lg:col-start-4">

      <div class="flex flex-col gap-y-40">

        <!-- Pending Message -->
        <div class="text-center">
          <div class="mb-20">
            <svg class="w-60 h-60 mx-auto text-yellow-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <x-headings.h2>Zahlung wird verarbeitet</x-headings.h2>
          <p class="mt-10 text-gray-600">Ihre Zahlung wird noch verarbeitet. Sie erhalten eine Bestätigung per E-Mail.</p>
        </div>

        <!-- Order Reference -->
        <x-layout.row class="justify-center">
          <span>Bestellreferenz: <strong>{{ $reference }}</strong></span>
        </x-layout.row>

        <!-- Continue Shopping -->
        <div class="text-center">
          <a 
            href="{{ route('page.landing') }}"
            class="inline-block py-15 px-30 bg-black text-white font-sans hover:bg-gray-800 transition-colors">
            Zurück zur Startseite
          </a>
        </div>

      </div>

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
