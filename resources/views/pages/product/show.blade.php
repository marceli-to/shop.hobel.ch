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
      
      <x-misc.row class="font-sans">
        {{ $product->name }}
      </x-misc.row>
      
      @if ($product->short_description)
        <x-misc.row>
          {{ $product->short_description }}
        </x-misc.row>
      @endif
      
      <x-misc.row>
        Lieferfrist ca. 4 Wochen
      </x-misc.row>

      <x-misc.row class="border-b border-b-black">
        <x-cart.money :amount="$product->price" />
      </x-misc.row>

      <div class="mt-40">
        <livewire:cart.button :productUuid="$product->uuid" />
      </div>

      @if ($product->description)
        <div class="mt-40">
          {!! nl2br($product->description) !!}
        </div>
      @endif

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
