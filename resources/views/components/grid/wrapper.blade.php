@props([
  'class' => ''
])
<div 
  class="
  flex 
  flex-col 
  gap-y-20 
  lg:gap-y-0 
  lg:grid 
  lg:grid-cols-12 
  lg:gap-x-20
  {{ $class }}">
  {{  $slot }}
</div>