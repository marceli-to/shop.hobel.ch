@extends('app')
@section('content')

<x-grid.wrapper class="pt-40 lg:pt-0">

  <x-grid.span class="flex flex-col gap-y-20 lg:col-span-6">

    @foreach($products as $product)
      @php
        $firstMedia = $product->media->first();
      @endphp

      <x-product.teaser url="" :title="$product->name">

        <x-headings.h2 class="text-md lg:text-lg absolute top-16 lg:top-31 left-20 z-20">
          {{ $product->name }}
        </x-headings.h2>

        @if($firstMedia)
          <x-media.image
            :src="$firstMedia->file_path"
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

      </x-product.teaser>
    @endforeach

  </x-grid.span>

  <x-grid.span class="lg:col-span-5 lg:col-start-8 px-20 lg:pl-0">
    <div class="hidden lg:block lg:fixed lg:left-start-8 lg:bottom-40">
      <x-misc.claim />
    </div>
  </x-grid.span>

</x-grid.wrapper>

@endsection
