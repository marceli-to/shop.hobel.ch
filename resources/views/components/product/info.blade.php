@props(['product', 'parent' => null])
<div 
  {{ $attributes->merge(['class' => 'bg-white w-full mt-32 lg:mt-0 lg:pl-16 lg:absolute lg:h-full lg:z-30 lg:top-0 lg:left-[calc((100%_/_12)_-_15px)] lg:w-[calc((100%/6)_+_19px)]']) }}
  data-variation-wrapper="{{ $product->uuid }}">
  <div class="lg:pr-16 lg:max-h-[calc(100vh_-_220px)] lg:overflow-y-auto">
    <x-table.row class="font-europa-bold font-bold py-4 !min-h-33">
      {{ $product->title }}
    </x-table.row>
    <x-table.row class="py-4 !min-h-33">
      {{ $product->description }}
    </x-table.row>

    @foreach($product->attributes as $attribute)
      <x-table.row class="py-4 !min-h-33">
        {{ $attribute }}
      </x-table.row>
    @endforeach

    <x-table.row class="py-4 !min-h-33">
      CHF {{ $product->price }}
    </x-table.row>

    @if ($product->state->value() == 'deliverable' || $product->state->value() == 'ready_for_pickup')
      <x-table.row class="italic border-b border-b-black py-4 !min-h-33">
        {{ $product->stock }} Stück {{ $product->state->label() }}
      </x-table.row>
    @else
    <x-table.row class="italic border-b border-b-black py-4 !min-h-33">
      {{ $product->stateText }}
    </x-table.row>
    @endif


    @if ($parent && $parent->variations && $parent->variations->count() > 0)

      <div class="mt-32">
        <x-table.row>
          <x-product.buttons.switcher :value="$parent->uuid" :label="$parent->title" />
        </x-table.row>
        @foreach($parent->variations as $variation)
          <x-table.row class="{{ $loop->last ? 'border-b border-b-black' : ''}} {{ $variation->uuid == $product->uuid ? 'font-europa-bold font-bold' : ''}}">
            <x-product.buttons.switcher :value="$variation->uuid" :label="$variation->title" :active="$product->uuid == $variation->uuid ? true : false" />
          </x-table.row>
        @endforeach
      </div>

    @elseif ($product->variations && $product->variations->count() > 0)
      
      <div class="mt-32">
        <x-table.row class="font-europa-bold font-bold">
          <x-product.buttons.switcher :value="$product->uuid" :label="$product->title" :active="true" />
        </x-table.row>
        @foreach($product->variations as $variation)
          <x-table.row class="{{ $loop->last ? 'border-b border-b-black' : ''}}">
            <x-product.buttons.switcher :value="$variation->uuid" :label="$variation->title" />
          </x-table.row>
        @endforeach
      </div>

    @endif
    
    @if ($product->stock > 0 && ($product->state->value() == 'deliverable' || $product->state->value() == 'ready_for_pickup'))
      <livewire:cart-button :productUuid="$product->uuid" :key="$product->uuid" />
    @endif

    @if ($product->state->value() != 'deliverable' && $product->state->value() != 'ready_for_pickup')

    <div class="mt-16 min-h-33">
      <livewire:product-notification :uuid="$product->uuid" :key="$product->uuid" />
    </div>
    @endif

    @if ($product->state->value() == 'deliverable' || $product->state->value() == 'ready_for_pickup')
      <x-table.row class="hidden lg:flex mt-32 border-none">
        <a 
          href="javascript:;" 
          :class="{ '!border-y-flame !text-flame': shippingInfo }"
          x-on:click="shippingInfo = !shippingInfo"
          class="min-h-32 w-full flex items-center leading-none space-x-6 hover:text-flame group-hover:text-flame border-y border-y-black hover:border-y-flame transition-all"
          title="Versandinstruktionen anzeigen">
          <x-icons.chevron-right-tiny class="w-6 h-auto" />
          <span>Versandinformationen</span>
        </a>
      </x-table.row>
    @endif
  </div>
</div>