<x-layout.app
  :title="$product->name"
  :backlink="route('page.category', ['category' => $category])">

  <x-grid.wrapper>

    <x-grid.span class="flex flex-col gap-y-20 lg:col-span-6">
      @if ($product->images)
        @foreach($product->images as $image)
          <x-media.image
            :src="$image->file_path"
            fit="max"
            :quality="90"
            :formats="['avif', 'webp', 'jpg']"
            :breakpoints="[
              ['media' => '(min-width: 1024px)', 'width' => 1280],
              ['media' => '(min-width: 768px)', 'width' => 1024],
              ['width' => 768],
            ]"
            class="block w-full h-auto" />
        @endforeach
      @endif
    </x-grid.span>

    <x-grid.span class="lg:col-span-3 lg:col-start-8 px-20 lg:px-0 lg:mt-80">

      <x-layout.row class="font-sans">
        {{ $product->name }}
      </x-layout.row>

      @switch($product->type)
        @case(\App\Enums\ProductType::Variations)
          @include('pages.product.partials.type-variations')
          @break
        @case(\App\Enums\ProductType::Configurable)
          @include('pages.product.partials.type-configurable')
          @break
        @default
          @include('pages.product.partials.type-simple')
      @endswitch

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
