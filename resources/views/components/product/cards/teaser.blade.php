@props(['product'])
<a 
  href="{{ route('product.show', ['product' => $product->slug ?? '-']) }}"
  title="{{ $product->group_title }}"
  class="relative block">
  <h2 class="absolute w-[calc(100%_-_32px)] z-50 top-12 lg:top-70 left-16 lg:left-[calc((100vw/12)_-_2px)] text-lg">
    {{ $product->group_title }}
  </h2>
  <x-media.picture :image="$product->image" :alt="$product->title" />
</a>