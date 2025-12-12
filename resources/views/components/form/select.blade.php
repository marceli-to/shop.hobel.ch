@props([
  'id' => '',
])
<x-layout.row class="col-span-4">
  <select 
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'w-full h-40 bg-transparent outline-none cursor-pointer']) }}>
    {{ $slot }}
  </select>
</x-layout.row>
