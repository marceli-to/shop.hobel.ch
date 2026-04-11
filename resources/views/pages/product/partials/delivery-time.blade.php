@if ($product->delivery_time)
  <x-layout.row>
    Lieferfrist ca. {{ $product->delivery_time }}
  </x-layout.row>
@endif
