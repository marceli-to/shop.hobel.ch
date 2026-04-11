@props([
  'amount' => '0.00',
  'showCurrency' => true
])
<span {{ $attributes->merge(['class' => $showCurrency ? 'flex gap-x-15 justify-between w-125' : 'flex justify-end w-125']) }} >
  @if($showCurrency)
    <span>Fr.</span>
  @endif
  <span>{{ number_format($amount, 2, '.', '’') }}</span>
</span>