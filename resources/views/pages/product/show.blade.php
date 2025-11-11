@extends('app')
@section('content')
<x-layout.page-title>
  Produkte: {{ $product->group_title }}
</x-layout.page-title>
<div 
  class="relative lg:mt-30"
  x-data="{ shippingInfo: false }">
  <div 
    class="hidden lg:block absolute inset-0 z-20 m-32 left-[calc((100%_/_4))] top-0 w-[calc((100%/2)_-_64px)] h-[calc(100vh_-_310px)]"
    x-cloak
    x-show="shippingInfo"
    x-on:click.outside="shippingInfo = false"
    x-on:keyup.escape.window="shippingInfo = false">
    <div class="bg-flame font-europa-regular font-regular text-white text-lg w-full h-full p-22 pr-64 relative">
      <a
        href="javascript:;"
        x-on:click="shippingInfo = !shippingInfo"
        class="absolute right-32 top-32"
        title="Versandinstruktionen verbergen">
        <x-icons.cross-large />
      </a>
      <x-product.instructions />
    </div>
  </div>
  <x-swiper.wrapper 
    type="product"
    containerClass="js-swiper-product" 
    wrapperClass="swiper-product">
    @if ($product->image)
      <x-swiper.slide productUuid="{{ $product->uuid }}">
        <x-media.picture :image="$product->image" :alt="$product->title" />
      </x-swiper.slide>
    @endif
    @if ($product->cards)
      @foreach($product->cards as $card)
        <x-swiper.slide productUuid="{{ $product->uuid }}">
          @if ($card['type'] == 'Bild')
            <x-media.picture :image="$card['image']" :alt="$product->title" :lazy="false" />
          @endif
          @if ($card['type'] == 'Text')
            <x-product.cards.text :text="$card['text']" class="text-sm md:text-md p-32 lg:px-84 bg-ivory overflow-y-auto" />
          @endif
        </x-swiper.slide>
      @endforeach
    @endif
    @if ($product->variations->count() > 0)
      @foreach($product->variations as $variation)
        @if ($variation->image)
          <x-swiper.slide productUuid="{{ $variation->uuid }}">
            <x-media.picture :image="$variation->image" :alt="$variation->title" />
          </x-swiper.slide>
        @endif
        @if ($variation->cards)
          @foreach($variation->cards as $card)
            <x-swiper.slide productUuid="{{ $variation->uuid }}">
              @if ($card['type'] == 'Bild')
                <x-media.picture :image="$card['image']" :alt="$product->title" :lazy="false" />
              @endif
              @if ($card['type'] == 'Text')
                <x-product.cards.text :text="$card['text']" class="text-sm lg:text-md p-32 bg-ivory" />
              @endif
            </x-swiper.slide>
          @endforeach
        @endif
      @endforeach
    @endif
  </x-swiper.wrapper>


  <div class="md:grid md:grid-cols-12 md:gap-x-16 lg:block">
    <div class="md:col-span-10 md:col-start-2">
      <x-product.info :product="$product" />

      @if ($product->variations->count() > 0)
        @foreach($product->variations as $variation)
          <x-product.info :product="$variation" :parent="$product" class="hidden" />
        @endforeach
      @endif

      @if ($product->state->value() == 'deliverable' || $product->state->value() == 'ready_for_pickup')
        <x-table.row class="mt-64 lg:hidden">
          <div class="pt-4">
            <span class="font-europa-bold font-bold">Versandinformationen</span>
            <x-product.instructions />
          </div>
        </x-table.row>
      @endif
    </div>
  </div>

</div>
@endsection