@extends('app')
@section('content')
@if ($errors->any())
  <x-form.errors>
    Bitte Zahlungsmethode auswählen.
  </x-form.errors>
@else
  <x-layout.page-title>
    Zahlung
  </x-layout.page-title>
@endif
<div class="md:grid md:grid-cols-12 gap-x-16 lg:mt-30 pb-20 lg:pb-40">
  <div class="hidden md:block md:col-span-4 lg:col-span-2 lg:col-start-2">
    <x-order.menu order_step="{{ $order_step }}" />
  </div>
  <div class="md:col-span-8 lg:col-span-5 xl:col-span-4">
    <form method="POST" action="{{ route('order.payment-method-store') }}">
      @csrf
      <x-table.row class="border-b border-b-black min-h-34">
        <span>Zahlungsmittel</span>
      </x-table.row>
      <x-table.row class="!min-h-64 !mt-1 flex items-center !border-t-0">
          {{-- <x-form.radio 
            name="payment_method" 
            value="credit_card" 
            checked="{{ isset($cart['payment_method']) && $cart['payment_method'] == 'credit_card' ? true : false  }}">
            <div class="flex gap-x-16">
              @foreach (config('invoice.payment_methods') as $payment_method)
                <x-dynamic-component :component="'icons.' .  $payment_method['key']" />
              @endforeach
            </div>
          </x-form.radio> --}}
        <x-form.radio 
          name="payment_method" 
          value="credit_card" 
          checked="true">
          <div class="flex gap-x-16">
            Kreditkarte
            {{-- @foreach (config('invoice.payment_methods') as $payment_method)
              <x-dynamic-component :component="'icons.' .  $payment_method['key']" />
            @endforeach --}}
          </div>
        </x-form.radio>
      </x-table.row>
      <x-table.row class="border-none mt-1">
        <x-buttons.primary label="Weiter" type="button" class="!min-h-33" />
      </x-table.row>
    </form>
  </div>
  <div class="hidden lg:block lg:col-span-2 xl:col-span-2">
    @foreach($cart['items'] as $item)
      <x-media.picture :image="$item['image']" :alt="$item['title']" class="hidden md:block md:mb-16 xl:max-w-[240px]" />
    @endforeach
  </div>
</div>
@endsection