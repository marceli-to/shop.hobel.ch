@props([
  'selector' => 'product-swiper',
])
<div data-{{ $selector }} {{ $attributes->merge(['class' => 'swiper']) }}>
  <div class="swiper-wrapper">
    {{ $slot }}
  </div>
  <div class="swiper-pagination"></div>
</div>
