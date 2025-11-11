@extends('app')
@section('content')
@if ($errors->any())
  <x-form.errors>
    Bitte füllen Sie alle Pflichtfelder aus.
  </x-form.errors>
@else
  <x-layout.page-title>
    Lieferadresse
  </x-layout.page-title>
@endif
<div class="md:grid md:grid-cols-12 gap-x-16 lg:mt-30 pb-20 lg:pb-40">
  <div class="hidden md:block md:col-span-4 lg:col-span-2 lg:col-start-2">
    <x-order.menu order_step="{{ $order_step }}" />
  </div>
  <div class="md:col-span-8 lg:col-span-5 xl:col-span-4">
    <form method="POST" action="{{ route('order.shipping-address-store') }}">
      @csrf
      <div class="space-y-1">

        @if($can_use_invoice_address)
          <x-table.row class="border-b border-b-black !min-h-34">
            <x-form.checkbox 
              name="use_invoice_address" 
              value="1" 
              label="Die Lieferadresse entspricht der Rechnungsadresse" 
              checked="{{ $cart['shipping_address']['use_invoice_address'] ?? old('use_invoice_address') }}" />
          </x-table.row>
        @endif

        <x-table.row class="{{ $can_use_invoice_address ? '!mt-32' : '' }}">
          <x-form.input 
            name="firstname" 
            placeholder="Vorname"
            required="true"
            value="{{ $cart['shipping_address']['firstname'] ?? old('firstname') }}" />
        </x-table.row>
        <x-table.row>
          <x-form.input 
            name="name" 
            placeholder="Nachname" 
            required="true"
            value="{{ $cart['shipping_address']['name'] ?? old('name') }}" />
        </x-table.row>
        <x-table.row>
          <x-form.input 
            name="company" 
            placeholder="Firma" 
            value="{{ $cart['shipping_address']['company'] ?? old('company') }}" />
        </x-table.row>
        <x-table.row>
          <x-form.input 
            name="street" 
            placeholder="Strasse" 
            required="true"
            value="{{ $cart['shipping_address']['street'] ?? old('street') }}" />
        </x-table.row>
        <x-table.row>
          <x-form.input 
            name="street_number" 
            placeholder="Hausnummer" 
            value="{{ $cart['shipping_address']['street_number'] ?? old('street_number') }}" />
        </x-table.row>
        <x-table.row>
          <x-form.input 
            name="zip" 
            placeholder="PLZ" 
            required="true"
            value="{{ $cart['shipping_address']['zip'] ?? old('zip') }}" />
        </x-table.row>
        <x-table.row>
          <x-form.input 
            name="city" 
            placeholder="Ort" 
            required="true"
            value="{{ $cart['shipping_address']['city'] ?? old('city') }}" />
        </x-table.row>
        <x-table.row class="border-b border-b-black">
          <x-form.select 
            name="country" 
            placeholder="Land" 
            required="true"
            :options="config('countries.delivery')"
            value="{{ $cart['shipping_address']['country'] ?? old('country') }}" />
        </x-table.row>
      </div>
      <x-table.row class="border-none mt-32">
        <x-buttons.primary label="Zahlung" type="button" />
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