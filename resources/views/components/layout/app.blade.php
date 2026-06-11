@props([
  'title' => 'Shop',
  'backlink' => null,
  'description' => null,
  'ogImage' => null,
  'ogType' => 'website',
])

<x-layout.head :title="$title" :description="$description" :ogImage="$ogImage" :ogType="$ogType" />
@if (request()->routeIs('page.landing'))
  <x-layout.header :dynamic="true" :showMobileTitle="false" :showDesktopTitle="false" />
@else
  <x-layout.header :title="$title" :showDesktopTitle="true" :backlink="$backlink" />
@endif
<x-layout.body>
  {{-- <x-layout.debug /> --}}
  <x-layout.main class="{{ request()->routeIs('page.landing') ? 'pt-40 lg:pt-0' : '' }}">
    {{ $slot ?? '' }}
  </x-layout.main>
</x-layout.body>
<x-layout.footer />