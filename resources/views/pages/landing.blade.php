@extends('app')
@section('content')

<x-grid.wrapper class="pt-40 lg:pt-0">

  <x-grid.span class="flex flex-col gap-y-20 lg:col-span-6">

    <x-product.teaser url="/" title="Tisch">

      <x-headings.h2 class="text-md lg:text-lg absolute top-16 lg:top-31 left-20 z-20">
        Tisch
      </x-headings.h2>

      <x-media.image
        src="media/dummy/tisch.jpg"
        alt="Tisch"
        fit="max"
        :quality="90"
        :formats="['avif', 'webp', 'jpg']"
        :breakpoints="[
          ['media' => '(min-width: 1280px)', 'width' => 1580],
          ['media' => '(min-width: 1024px)', 'width' => 1280],
          ['media' => '(min-width: 768px)', 'width' => 1024],
          ['width' => 768],
        ]"
        class="block w-full h-auto aspect-[4/3] object-cover relative z-10 group-hover:scale-110 origin-center transition-transform duration-700" />

    </x-product.teaser>

    <x-product.teaser url="/" title="Stuhl">

      <x-headings.h2 class="text-md lg:text-lg absolute top-16 lg:top-31 left-20 z-20">
        Stuhl
      </x-headings.h2>

      <x-media.image
        src="media/dummy/stuhl.jpg"
        alt="Stuhl"
        fit="max"
        :quality="90"
        :formats="['avif', 'webp', 'jpg']"
        :breakpoints="[
          ['media' => '(min-width: 1280px)', 'width' => 1580],
          ['media' => '(min-width: 1024px)', 'width' => 1280],
          ['media' => '(min-width: 768px)', 'width' => 1024],
          ['width' => 768],
        ]"
        class="block w-full h-auto aspect-[4/3] object-cover relative z-10 group-hover:scale-110 origin-center transition-transform duration-700" />

    </x-product.teaser>

    <x-product.teaser url="/" title="Tablett">

      <x-headings.h2 class="text-md lg:text-lg absolute top-16 lg:top-31 left-20 z-20">
        Tablett
      </x-headings.h2>

      <x-media.image
        src="media/dummy/tablett.jpg"
        alt="Tablett"
        fit="max"
        :quality="90"
        :formats="['avif', 'webp', 'jpg']"
        :breakpoints="[
          ['media' => '(min-width: 1280px)', 'width' => 1580],
          ['media' => '(min-width: 1024px)', 'width' => 1280],
          ['media' => '(min-width: 768px)', 'width' => 1024],
          ['width' => 768],
        ]"
        class="block w-full h-auto aspect-[4/3] object-cover relative z-10 group-hover:scale-110 origin-center transition-transform duration-700" />

    </x-product.teaser>

  </x-grid.span>

  <x-grid.span class="lg:col-span-5 lg:col-start-8 px-20 lg:pl-0">
    <div class="hidden lg:block lg:fixed lg:left-start-8 lg:bottom-40">
      <x-misc.claim />
    </div>
  </x-grid.span>

</x-grid.wrapper>

@endsection
