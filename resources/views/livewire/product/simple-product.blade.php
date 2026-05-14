<div>
  @if ($product->short_description)
    <x-layout.row>
      {{ $product->short_description }}
    </x-layout.row>
  @endif

  @if ($product->attributes->isNotEmpty())
    @foreach($product->attributes as $attribute)
      <x-layout.row>
        {{ $attribute->name }}
      </x-layout.row>
    @endforeach
  @endif

  @if ($product->delivery_time)
    <x-layout.row>
      Lieferfrist {{ $product->delivery_time }}
    </x-layout.row>
  @endif

  <x-layout.row class="border-b border-b-black">
    <x-cart.money :amount="$product->price" class="w-auto" />
  </x-layout.row>

  @if($canAddToCart)
    <div class="mt-40 space-y-40">
      <x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" />
      <x-cart.button :inCart="$inCart" />
    </div>
  @else
    <div class="mt-40">
      <p>Bitte kontaktieren Sie uns (E-Mail: <a href="mailto:shop@hobel.ch" class="font-sans">shop@hobel.ch</a>)</p>
    </div>
  @endif

  @if ($product->description)
    <div class="my-40">
      {!! nl2br($product->description) !!}
    </div>
  @endif
</div>
