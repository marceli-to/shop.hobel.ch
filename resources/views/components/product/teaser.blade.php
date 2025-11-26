@props([
  'url' => '',
  'title' => '',
])
<a 
  href="{{ $url }}"
  aria-label="{{ $title }}"
  class="relative group overflow-hidden">
  {{ $slot }}
</a>