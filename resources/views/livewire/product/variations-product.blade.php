<div>
  <x-layout.row>
    {{ $selectedLabel }}
  </x-layout.row>

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
    <x-cart.money :amount="$selectedPrice" class="w-auto" />
  </x-layout.row>

  <div class="mt-40 relative">
    <select
      wire:change="selectVariation($event.target.value)"
      class="w-full border border-black px-12 py-6 h-40 pr-32 bg-white appearance-none cursor-pointer focus:outline-none truncate">
      @foreach($product->children as $child)
        <option value="{{ $child->uuid }}">
          {{ $child->label }}
        </option>
      @endforeach
    </select>
    <x-icons.chevron-down class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none" />
  </div>

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
