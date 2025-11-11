<div class="mb-32 last-of-type:mb-0 divide-y divide-black border-t border-t-black">
  {{-- <div class="grid grid-cols-4 border-b border-b-black">
    <x-table.row class="border-none col-span-3">
      <span>Nettobetrag</span>
    </x-table.row>
    <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
      <span>CHF</span>
      <span>{{ number_format($subtotal, 2, '.', '&thinsp;') }}</span>
    </x-table.row>
    <x-table.row class="col-span-3">
      <span>MwSt. ({{ config('invoice.tax_rate') }}%)</span>
    </x-table.row>
    <x-table.row class="col-span-1 flex justify-between 2xl:pl-16">
      <span>CHF</span>
      <span>{{ number_format($tax, 2, '.', '&thinsp;') }}</span>
    </x-table.row>
  </div> --}}
  <div class="grid grid-cols-4 !border-b border-b-black font-europa-bold font-bold">
    <x-table.row class="border-none col-span-3">
      <span>Gesamttotal</span>
    </x-table.row>
    <x-table.row class="border-none col-span-1 flex justify-between 2xl:pl-16">
      <span>CHF</span>
      <span>{!! number_format($total, 2, '.', '&thinsp;') !!}</span>
    </x-table.row>
  </div>
</div>