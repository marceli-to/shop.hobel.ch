@if ($product->short_description)
  <x-layout.row>
    {{ $product->short_description }}
  </x-layout.row>
@endif

@include('pages.product.partials.attributes')
@include('pages.product.partials.delivery-time')

<x-layout.row class="border-b border-b-black">
  <x-cart.money :amount="$product->price" class="w-auto" />
</x-layout.row>

@include('pages.product.partials.cart-action', [
  'productUuid' => $product->uuid,
  'wireKey' => 'cart-btn-' . $product->uuid,
])

@include('pages.product.partials.description')

