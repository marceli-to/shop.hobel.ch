@props([
  'class' => ''
])
<h2 class="{{ $class }}">
  {{ $slot }}
</h2>