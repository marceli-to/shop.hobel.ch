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

      @if ($product->children->isNotEmpty())
        {{-- Product with children (variations) --}}
        @php
          $childrenData = $product->children->map(fn($c) => [
            'uuid' => $c->uuid,
            'label' => $c->label,
            'price' => (float) $c->price,
          ])->values();
          $firstChild = $product->children->first();
        @endphp
        <div
          x-data="{
            children: {{ Js::from($childrenData) }},
            selectedUuid: '{{ $firstChild->uuid }}',
            get selected() {
              return this.children.find(c => c.uuid === this.selectedUuid) || this.children[0];
            },
            formatPrice(price) {
              return price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, String.fromCharCode(39));
            }
          }"
        >
          <x-layout.row>
            <span x-text="selected.label"></span>
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
              Lieferfrist ca. {{ $product->delivery_time }}
            </x-layout.row>
          @endif

          <x-layout.row class="border-b border-b-black">
            <span>CHF <span x-text="formatPrice(selected.price)"></span></span>
          </x-layout.row>

          {{-- Render a cart button for each child, show only the selected one --}}
          @foreach($product->children as $child)
            <div class="mt-40" x-show="selectedUuid === '{{ $child->uuid }}'">
              <livewire:cart.button
                :productUuid="$child->uuid"
                :key="'cart-btn-' . $child->uuid" />
            </div>
          @endforeach

          <div class="mt-40 relative">
            <select
              x-model="selectedUuid"
              class="w-full border border-black px-12 py-6 h-40 pr-32 bg-white appearance-none cursor-pointer focus:outline-none">
              @foreach($product->children as $child)
                <option value="{{ $child->uuid }}">
                  {{ $child->label }}
                </option>
              @endforeach
            </select>
            <x-icons.chevron-down class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>

          @if ($product->description)
            <div class="mt-40">
              {!! nl2br($product->description) !!}
            </div>
          @endif
        </div>
      @else
        {{-- Simple product (no children) --}}
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

        <x-layout.row class="border-b border-b-black">
          <x-cart.money :amount="$product->price" class="w-auto" />
        </x-layout.row>

        <div class="mt-40">
          <livewire:cart.button :productUuid="$product->uuid" />
        </div>

        @if ($product->description)
          <div class="mt-40">
            {!! nl2br($product->description) !!}
          </div>
        @endif
      @endif

    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
