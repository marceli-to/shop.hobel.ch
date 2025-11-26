@props([
  'class' => ''
])
<h3 class="{{ $class }}">
  {{ $slot }}
</h3>