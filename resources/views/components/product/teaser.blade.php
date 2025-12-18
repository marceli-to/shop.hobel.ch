@props([
  'url' => '',
  'title' => '',
])
<a 
  href="{{ $url }}"
  aria-label="{{ $title }}"
  {{ $attributes->merge(['class' => 'relative group overflow-hidden']) }}>
  {{ $slot }}
</a>