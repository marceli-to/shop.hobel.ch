@props(['image', 'alt' => '', 'lazy' => true])
<picture class="w-full">
  <source media="(min-width: 1280px)" srcset="/img/large/{{ $image }}">
  <source media="(min-width: 768px)" srcset="/img/medium/{{ $image }}">
  <img 
    src="/img/small/{{ $image }}" 
    alt="{{ $alt }}" 
    title="{{ $alt }}" 
    height="800" 
    width="800"
    @if ($lazy)
    loading="lazy"
    @endif
    {{ $attributes->merge(['class' => 'block']) }}>
</picture>