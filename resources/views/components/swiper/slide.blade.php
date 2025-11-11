@props(['productUuid' => ''])
<div class="swiper-slide" @if ($productUuid) data-product-uuid="{{ $productUuid }}" @endif>
  {{ $slot }}
</div>