@extends('app')
@section('content')
<x-layout.page-title>
  Herzlichen Dank für Ihre Bestellung
</x-layout.page-title>
<div class="md:grid md:grid-cols-12 gap-x-16 lg:mt-30 pb-20 lg:pb-40">
  <div class="hidden md:block md:col-span-4 lg:col-span-2 lg:col-start-2">
    <x-order.menu order_step="{{ $order_step }}" />
  </div>
  <div class="md:col-span-8 lg:col-span-5 xl:col-span-4">
    @foreach ($order->orderProducts as $product)
      <div class="mb-32 last-of-type:mb-0 divide-y divide-black border-t border-t-black">
        <div class="grid grid-cols-4">
          <x-table.row class="border-none col-span-3 flex justify-between font-europa-bold font-bold">
            <span>{{ $product->title }}</span>
          </x-table.row>
          <x-table.row class="border-none col-span-1 flex justify-end">
            <span>{{ $product->quantity }}</span>
          </x-table.row>
        </div>
        <div class="grid grid-cols-4">
          <x-table.row class="border-none col-span-3">
            <span>{{ $product->description }}</span>
          </x-table.row>
          <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
            <span>CHF</span>
            <span>{{ $product->price }}</span>
          </x-table.row>
        </div>
        <div class="grid grid-cols-4">
          <x-table.row class="border-none col-span-3">
            <span>Verpackung und Versand</span>
          </x-table.row>
          <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
            <span>CHF</span>
            <span>{{ $product->shipping }}</span>
          </x-table.row>
        </div>
        <div class="grid grid-cols-4 !border-b border-b-black font-europa-bold font-bold">
          <x-table.row class="border-none col-span-3">
            <span>Total</span>
          </x-table.row>
          <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
            <span>CHF</span>
            <span>{!! number_format($product->price + $product->shipping, 2, '.', '&thinsp;') !!}</span>
          </x-table.row>
        </div>
      </div>
    @endforeach
    <div class="grid grid-cols-4 border-y border-y-black font-europa-bold font-bold">
      <x-table.row class="border-none col-span-3">
        <span>Gesamttotal</span>
      </x-table.row>
      <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
        <span>CHF</span>
        <span>{!! number_format($order->total, 2, '.', '&thinsp;') !!}</span>
      </x-table.row>
    </div>
    <x-table.row class="font-europa-bold font-bold mt-32">
      <span>Lieferadresse</span>
    </x-table.row>
    @if ($order->use_invoice_address)
      @if ($order->salutation)
        <x-table.row>
          <span>{{ $order->salutation }}</span>
        </x-table.row>
      @endif
      <x-table.row>
        <span>{{ $order->invoice_name }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $order->invoice_address }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $order->invoice_location }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $order->country }}</span>
      </x-table.row>
    @else
      <x-table.row>
        <span>{{ $order->shipping_full_name }}</span>
      </x-table.row>
      @if ($order->shipping_company)
        <x-table.row>
          <span>{{ $order->shipping_company }}</span>
        </x-table.row>
      @endif
      <x-table.row>
        <span>{{ $order->shipping_address }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $order->shipping_location }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $order->shipping_country }}</span>
      </x-table.row>
    @endif
    <x-table.row class="font-europa-bold font-bold mt-32">
      <span>Zahlung</span>
    </x-table.row>
    <div class="grid grid-cols-4 border-b border-b-black">
      <x-table.row class="col-span-3">
        <span>{{ $order->payment_info }}</span>
      </x-table.row>
      <x-table.row class="col-span-1 flex justify-between 2xl:pl-16">
        <span>CHF</span>
        <span>{{ $order->total }}</span>
      </x-table.row>
      <x-table.row class="col-span-3">
        <span>Order ID</span>
      </x-table.row>
      <x-table.row class="col-span-1 flex justify-end">
        <span>{{ $order->order_number }}</span>
      </x-table.row>
    </div>
    <div class="mt-32">
      <x-product.instructions />
    </div>
  </div>
</div>
@endsection