@props([
  'title' => 'Shop',
  'backlink' => null
])

<x-layout.head :title="$title" />
@if (request()->routeIs('page.landing'))
  <x-layout.header :dynamic="true" :showMobileTitle="false" :showDesktopTitle="false" />
@else
  <x-layout.header :title="$title" :showDesktopTitle="true" :backlink="$backlink" />
@endif
<x-layout.body>
  <x-layout.main class="pt-40 lg:pt-0">
    {{ $slot ?? '' }}
  </x-layout.main>
</x-layout.body>
<x-layout.footer />