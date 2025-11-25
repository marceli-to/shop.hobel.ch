<x-layout.head />
@if (request()->routeIs('page.landing'))
  <x-layout.header :dynamic="true" :showMobileTitle="false" :showDesktopTitle="false" />
@else
  <x-layout.header :showDesktopTitle="true" />
@endif
<x-layout.body>
  <x-layout.main>
    @yield('content')
  </x-layout.main>
</x-layout.body>
<x-layout.footer />