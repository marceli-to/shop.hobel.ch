@props([
  'class' => ''
])
<h1 class="{{ $class }}">
  {{ $slot }}
</h1>