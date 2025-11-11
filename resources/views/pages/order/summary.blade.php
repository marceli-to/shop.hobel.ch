@extends('app')
@section('content')
@if ($errors->any())
  <x-form.errors>
    Bitte AGB und Datenschutzerklärung akzeptieren.
  </x-form.errors>
@else
  <x-layout.page-title>
    Zusammenfassung
  </x-layout.page-title>
@endif
<div class="md:grid md:grid-cols-12 gap-x-16 lg:mt-30 pb-20 lg:pb-40">
  <div class="hidden md:block md:col-span-4 lg:col-span-2 lg:col-start-2">
    <x-order.menu order_step="{{ $order_step }}" />
  </div>
  <div class="md:col-span-8 lg:col-span-5 xl:col-span-4">
    <form method="POST" action="{{ route('order.finalize') }}">
      @csrf
      <x-table.row class="font-europa-bold font-bold">
        <span>Warenkorb</span>
      </x-table.row>
      @foreach ($cart['items'] as $item)
        <div class="mb-32 last-of-type:mb-0 divide-y divide-black border-t border-t-black mt-1">
          <div class="grid grid-cols-4">
            <x-table.row class="border-none col-span-3 flex justify-between">
              <span>{{ $item['title'] }}</span>
            </x-table.row>
            <x-table.row class="border-none col-span-1 flex justify-end">
              <span>{{ $item['quantity'] }}</span>
            </x-table.row>
          </div>
          <div class="grid grid-cols-4">
            <x-table.row class="border-none col-span-3">
              <span>{{ $item['description'] }}</span>
            </x-table.row>
            <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
              <span>CHF</span>
              <span>{!! number_format($item['price'] * $item['quantity'], 2, '.', '&thinsp;') !!}</span>
            </x-table.row>
          </div>
          <div class="grid grid-cols-4 !border-b border-b-black">
            <x-table.row class="border-none col-span-3">
              <span>Verpackung und Versand</span>
            </x-table.row>
            <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
              <span>CHF</span>
              <span>{{ number_format($item['shipping'] * $item['quantity'], 2, '.', '&thinsp;') }}</span>
            </x-table.row>
          </div>
        </div>
      @endforeach
      <livewire:cart-total />
      <x-table.row class="font-europa-bold font-bold">
        <span>Rechnungsadresse</span>
      </x-table.row>
      @if ($cart['invoice_address']['firstname'] && $cart['invoice_address']['name'])
        <x-table.row>
          <span>{{ $cart['invoice_address']['firstname'] }} {{ $cart['invoice_address']['name'] }}</span>
        </x-table.row>
      @endif
      @if ($cart['invoice_address']['company'])
        <x-table.row>
          <span>{{ $cart['invoice_address']['company'] }}</span>
        </x-table.row>
      @endif
      <x-table.row>
        <span>{{ $cart['invoice_address']['street'] }} {{ $cart['invoice_address']['street_number'] ?? null }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $cart['invoice_address']['zip'] }} {{ $cart['invoice_address']['city'] }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $cart['invoice_address']['country'] }}</span>
      </x-table.row>
      <x-table.row>
        <span>{{ $cart['invoice_address']['email'] }}</span>
      </x-table.row>
      <x-table.row class="font-europa-bold font-bold mt-32">
        <span>Lieferadresse</span>
      </x-table.row>
      @if (isset($cart['shipping_address']['use_invoice_address']) && $cart['shipping_address']['use_invoice_address'])
        <x-table.row>
          <span>Die Lieferadresse entspricht der Rechnungsadresse</span>
        </x-table.row>
      @else
        @if ($cart['shipping_address']['firstname'] && $cart['shipping_address']['name'])
          <x-table.row>
            <span>{{ $cart['shipping_address']['firstname'] }} {{ $cart['shipping_address']['name'] }}</span>
          </x-table.row>
        @endif
        @if ($cart['shipping_address']['company'])
          <x-table.row>
            <span>{{ $cart['shipping_address']['company'] }}</span>
          </x-table.row>
        @endif
        <x-table.row>
          <span>{{ $cart['shipping_address']['street'] }} {{ $cart['shipping_address']['street_number'] ?? null }}</span>
        </x-table.row>
        <x-table.row>
          <span>{{ $cart['shipping_address']['zip'] }} {{ $cart['shipping_address']['city'] }}</span>
        </x-table.row>
        <x-table.row>
          <span>{{ $cart['shipping_address']['country'] }}</span>
        </x-table.row>
      @endif
      <x-table.row class="font-europa-bold font-bold mt-32">
        <span>Zahlungsmittel</span>
      </x-table.row>
      <x-table.row>
        {{-- <span>{{ $cart['payment_method']['name'] }}</span> --}}
        <span>Kreditkarte</span>
      </x-table.row>
      <x-table.row class="mt-32">
        <x-form.checkbox 
          name="accept_terms" 
          value="true" 
          label="Hiermit akzeptiere ich die <a href='/kontakt' title='Allgemeinen Geschäftsbedingungen' target='_blank' class='hover:text-flame'><strong>Allgemeinen Geschäftsbedingungen</strong></a> und die <a href='/kontakt' title='Datenschutzerklärung' target='_blank' class='hover:text-flame'><strong>Datenschutzerklärung</strong></a>." 
          checked="{{ $cart['accept_terms'] ?? old('accept_terms') }}"
          class="!items-start pt-4"
          iconClass="mt-5" />
      </x-table.row>
      <x-table.row class="border-none mt-32">
        <button 
          class="font-europa-bold font-bold min-h-32 w-full flex items-center justify-center leading-none space-x-6 hover:text-flame border border-black hover:border-flame transition-all"
          type="submit">
          <span>Kostenpflichtig bestellen</span>
        </button>
      </x-table.row>
    </form>
  </div>
</div>
@endsection