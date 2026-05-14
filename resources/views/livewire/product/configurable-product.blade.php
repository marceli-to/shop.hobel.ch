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

  @if ($this->summary())
    <x-layout.row>
      {{ $this->summary() }}
    </x-layout.row>
  @endif

  @if ($product->delivery_time)
    <x-layout.row>
      Lieferfrist {{ $product->delivery_time }}
    </x-layout.row>
  @endif

  <x-layout.row class="border-b border-b-black">
    <x-cart.money :amount="$price" class="w-auto" />
  </x-layout.row>

  @if ($this->canAddConfiguration())
    <div class="mt-40 space-y-40">
      <x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" />
      <x-cart.button :inCart="$inCart" />
    </div>
  @endif

  <div class="mt-40">

    <x-layout.row class="grid grid-cols-3 gap-x-20">
      <div class="col-span-1">
        Länge (cm)
      </div>
      <div class="col-span-2">
        <x-form.number
          :min="$product->min_length"
          :max="$product->max_length"
          wire:model.live.debounce.400ms="length" />
      </div>
    </x-layout.row>

    @unless ($this->isRound())
      <x-layout.row class="grid grid-cols-3 gap-x-20">
        <div class="col-span-1">
          Breite (cm)
        </div>
        <div class="col-span-2">
          <x-form.number
            :min="$product->min_width"
            :max="$product->max_width"
            wire:model.live.debounce.400ms="width" />
        </div>
      </x-layout.row>
    @endunless

    <x-layout.row class="grid grid-cols-3 gap-x-20">
      <div class="col-span-1">
        Oberfläche
      </div>
      <div class="col-span-2">
        <x-form.select wire:model.live="surfaceId">
          @foreach($product->surfaces as $surface)
            <option value="{{ $surface->id }}">{{ $surface->name }}</option>
          @endforeach
        </x-form.select>
      </div>
    </x-layout.row>

    <x-layout.row class="grid grid-cols-3 gap-x-20">
      <div class="col-span-1">
        Kante
      </div>
      <div class="col-span-2">
        <x-form.select wire:model.live="edgeId">
          @foreach($product->edges as $edge)
            <option value="{{ $edge->id }}">{{ $edge->name }}</option>
          @endforeach
        </x-form.select>
      </div>
    </x-layout.row>

    <x-form.selector
      class="border-b"
      label="Holzart"
      :items="$product->woodTypes"
      :selected="$product->woodTypes->firstWhere('id', $woodTypeId)?->name"
      wire:model.live="woodTypeId" />
  </div>



  @if ($product->description)
    <div class="my-40">
      {!! nl2br($product->description) !!}
    </div>
  @endif
</div>
