<x-layout.app
  :title="$product->name"
  :description="$product->seoDescription"
  :ogImage="$product->ogImageUrl"
  ogType="product"
  :backlink="route('page.category', ['category' => $category])">

  <x-grid.wrapper>

    <x-grid.span class="lg:col-span-6">
      @if ($product->images)
        <x-swiper.container class="lg:!hidden">
          @foreach($product->images as $image)
            <x-swiper.slide>
              <x-media.image
                :src="$image->file_path"
                fit="max"
                :quality="90"
                :formats="['avif', 'webp', 'jpg']"
                :breakpoints="[
                  ['media' => '(min-width: 768px)', 'width' => 1024],
                  ['width' => 768],
                ]"
                class="block w-full h-auto" />
            </x-swiper.slide>
          @endforeach
        </x-swiper.container>

        <div class="hidden lg:flex lg:flex-col lg:gap-y-20">
          @foreach($product->images as $image)
            <x-media.image
              :src="$image->file_path"
              fit="max"
              :quality="90"
              :formats="['avif', 'webp', 'jpg']"
              :breakpoints="[
                ['media' => '(min-width: 1024px)', 'width' => 1280],
              ]"
              class="block w-full h-auto" />
          @endforeach
        </div>
      @endif
    </x-grid.span>

    <x-grid.span class="lg:col-span-3 lg:col-start-8 px-20 lg:px-0 lg:mt-80">

      <x-layout.row class="font-sans">
        {{ $product->name }}
      </x-layout.row>

      @switch($product->type)
        @case(\App\Enums\ProductType::Variations)
          @livewire('product.variations-product', ['product' => $product], key('product-' . $product->uuid))
          @break
        @case(\App\Enums\ProductType::Configurable)
          @livewire('product.configurable-product', ['product' => $product], key('product-' . $product->uuid))
          @break
        @default
          @livewire('product.simple-product', ['product' => $product], key('product-' . $product->uuid))
      @endswitch

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
