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
      Lieferfrist ca. {{ $product->delivery_time }}
    </x-layout.row>
  @endif

  <div class="mt-40">

    <x-layout.row class="grid grid-cols-3 gap-x-20">
      <div class="col-span-1">
        Länge (cm)
      </div>
      <div class="col-span-2">
        <input
          type="number"
          step="1"
          min="{{ $product->min_length }}"
          max="{{ $product->max_length }}"
          wire:model.live.debounce.400ms="lengthCm"
          class="w-full bg-transparent outline-none placeholder:text-ash text-right appearance-none">
      </div>
    </x-layout.row>

    @unless ($this->isRound())
      <x-layout.row class="grid grid-cols-3 gap-x-20">
        <div class="col-span-1">
          Breite (cm)
        </div>
        <div class="col-span-2">
          <input
            type="number"
            step="1"
            min="{{ $product->min_width }}"
            max="{{ $product->max_width }}"
            wire:model.live.debounce.400ms="widthCm"
            class="w-full bg-transparent outline-none placeholder:text-ash text-right">
          </div>
      </x-layout.row>
    @endunless

    <div>
      <label class="block mb-6">Holzart</label>
      <div class="relative">
        <select
          wire:model.live="woodTypeId"
          class="w-full border border-black px-12 py-6 h-40 pr-32 bg-white appearance-none cursor-pointer focus:outline-none truncate">
          @foreach($product->woodTypes as $woodType)
            <option value="{{ $woodType->id }}">{{ $woodType->name }}</option>
          @endforeach
        </select>
        <x-icons.chevron-down class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none" />
      </div>
    </div>

    <div>
      <label class="block mb-6">Oberfläche</label>
      <div class="relative">
        <select
          wire:model.live="surfaceId"
          class="w-full border border-black px-12 py-6 h-40 pr-32 bg-white appearance-none cursor-pointer focus:outline-none truncate">
          @foreach($product->surfaces as $surface)
            <option value="{{ $surface->id }}">{{ $surface->name }}</option>
          @endforeach
        </select>
        <x-icons.chevron-down class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none" />
      </div>
    </div>

    <div>
      <label class="block mb-6">Kante</label>
      <div class="relative">
        <select
          wire:model.live="edgeId"
          class="w-full border border-black px-12 py-6 h-40 pr-32 bg-white appearance-none cursor-pointer focus:outline-none truncate">
          @foreach($product->edges as $edge)
            <option value="{{ $edge->id }}">{{ $edge->name }}</option>
          @endforeach
        </select>
        <x-icons.chevron-down class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none" />
      </div>
    </div>
  </div>

  <x-layout.row class="mt-40 border-b border-b-black">
    <x-cart.money :amount="$price" class="w-auto" />
  </x-layout.row>

  @if ($this->canAddConfiguration())
    <div class="mt-40 space-y-40">
      <x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" />
      <x-cart.button :inCart="$inCart" />
    </div>
  @endif

  @if ($product->description)
    <div class="my-40">
      {!! nl2br($product->description) !!}
    </div>
  @endif
</div>
