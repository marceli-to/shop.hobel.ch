@extends('app')
@section('content')
<x-layout.page-title>
  Warenkorb
</x-layout.page-title>
<div class="md:grid md:grid-cols-12 gap-x-16 lg:mt-30 pb-20 lg:pb-40">
  <div class="hidden md:block md:col-span-4 lg:col-span-2 lg:col-start-2">
    <x-order.menu order_step="{{ $order_step }}" />
  </div>
  <div class="md:col-span-8 lg:col-span-5 xl:col-span-4">
    @empty ($cart['items'])
      <p class="text-lg font-europa-light font-light -mt-4">Ihr Warenkorb ist leer.</p>
    @else
      @foreach($cart['items'] as $item)
        <livewire:cart-item :uuid="$item['uuid']" :key="$item['uuid']" />
      @endforeach
      <livewire:cart-total />
      <x-table.row class="border-none">
        <x-buttons.primary route="{{ route('order.invoice-address') }}" label="Rechnungsadresse" class="!min-h-34" />
      </x-table.row>
    @endempty
  </div>
  <div class="hidden lg:block lg:col-span-2 xl:col-span-2">
    @foreach($cart['items'] as $item)
      <x-media.picture :image="$item['image']" :alt="$item['title']" class="hidden md:block md:mb-16 xl:max-w-[240px]" />
    @endforeach
  </div>
</div>
@endsection