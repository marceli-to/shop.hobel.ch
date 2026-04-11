@if ($product->attributes->isNotEmpty())
  @foreach($product->attributes as $attribute)
    <x-layout.row>
      {{ $attribute->name }}
    </x-layout.row>
  @endforeach
@endif
