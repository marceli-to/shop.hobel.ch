<div data-product-configurator x-data="{ length: @js($length), width: @js($width) }" class="scroll-mt-[12.5rem]">
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
      <div class="col-span-1 flex items-center">
        <x-form.slider
          :min="$product->min_length"
          :max="$product->max_length"
          x-model.number="length"
          x-on:change="$wire.set('length', length)" />
      </div>
      <div class="col-span-1 flex gap-x-4">
        <x-form.number
          :min="$product->min_length"
          :max="$product->max_length"
          x-model.number="length"
          x-on:change="$wire.set('length', length)" />
        <button type="button" class="cursor-pointer pl-8" x-on:click="$el.previousElementSibling.focus()">
          <x-icons.pencil class="w-12 h-auto" />
        </button>
      </div>
    </x-layout.row>

    @unless ($this->isRound())
      <x-layout.row class="grid grid-cols-3 gap-x-20">
        <div class="col-span-1">
          Breite (cm)
        </div>
        <div class="col-span-1 flex items-center">
          <x-form.slider
            :min="$product->min_width"
            :max="$product->max_width"
            x-model.number="width"
            x-on:change="$wire.set('width', width)" />
        </div>
        <div class="col-span-1 flex gap-x-4">
          <x-form.number
            :min="$product->min_width"
            :max="$product->max_width"
            x-model.number="width"
            x-on:change="$wire.set('width', width)" />
          <button type="button" class="cursor-pointer pl-8" x-on:click="$el.previousElementSibling.focus()">
            <x-icons.pencil class="w-12 h-auto" />
          </button>
        </div>
      </x-layout.row>
    @endunless

    <x-form.list-selector
      label="Oberfläche"
      :items="$product->surfaces"
      :selected="$product->surfaces->firstWhere('id', $surfaceId)?->name"
      wire:model.live="surfaceId" />

    <x-form.list-selector
      label="Kante"
      :items="$product->edges"
      :selected="$product->edges->firstWhere('id', $edgeId)?->name"
      wire:model.live="edgeId" />

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
