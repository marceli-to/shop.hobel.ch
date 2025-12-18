<x-layout.app 
  :title="$category->name" 
  :backlink="route('page.landing')">
  
  <x-grid.wrapper x-data="{ activeTag: null }">

    <x-grid.span class="flex flex-col gap-y-20 lg:col-span-6">
      
      @foreach($products as $product)
        <x-product.teaser 
          :url="route('page.product', ['category' => $category, 'product' => $product])" 
          :title="$product->name"
          x-show="activeTag === null || {{ json_encode($product->tags->pluck('slug')->toArray()) }}.includes(activeTag)"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100"
          x-transition:leave-end="opacity-0">

          <x-headings.h2 class="text-md lg:text-lg absolute top-16 lg:top-31 left-20 z-20">
            {{ $product->name }}
          </x-headings.h2>

          @if($product->previewImage)
            <x-media.image
              :src="$product->previewImage->file_path"
              :alt="$product->name"
              fit="max"
              :quality="90"
              :formats="['avif', 'webp', 'jpg']"
              :breakpoints="[
                ['media' => '(min-width: 1024px)', 'width' => 1280],
                ['media' => '(min-width: 768px)', 'width' => 1024],
                ['width' => 768],
              ]"
              class="block w-full h-auto aspect-[4/3] object-cover relative z-10 group-hover:scale-110 origin-center transition-transform duration-700" />
          @endif

          <div class="text-md lg:text-lg absolute bottom-16 lg:bottom-32 left-20 z-20">
            Fr. {{ number_format($product->price, 2, '.', '\'') }}
          </div>

        </x-product.teaser>
      @endforeach

    </x-grid.span>

    <x-grid.span class="lg:col-span-3 lg:col-start-8 px-20 lg:px-0">

      @if($tags->count() > 0)
      <div class="lg:-mt-5">
        <x-headings.h3 class="text-sm text-ash mb-8 w-full">Filter</x-headings.h3>
        <ul class="flex flex-col border-b">
          <li class="border-t border-black py-6">
            <button 
              type="button"
              class="flex items-center justify-between w-full text-left cursor-pointer"
              :class="{ 'font-sa': activeTag === null }"
              @click.prevent="activeTag = null">
              <span>Alle</span>
              <x-icons.cross size="sm" x-show="activeTag === null" class="ml-auto" />
            </button>
          </li>
          @foreach($tags as $tag)
            <li class="border-t border-black py-6">
              <button 
                type="button"
                class="flex items-center justify-between w-full text-left cursor-pointer"
                :class="{ 'font-sa': activeTag === '{{ $tag->slug }}' }"
                @click.prevent="activeTag = activeTag === '{{ $tag->slug }}' ? null : '{{ $tag->slug }}'">
                <span>{{ $tag->name }}</span>
                <x-icons.cross size="sm" x-show="activeTag === '{{ $tag->slug }}'" class="ml-auto" />
              </button>
            </li>
          @endforeach
        </ul>
      </div>
      @endif

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>