@props([
  'amount' => '0.00'
])
<span class="flex gap-x-15 justify-between w-125">
  <span>Fr.</span>
  <span>{{ number_format($amount, 2, '.', '’') }}</span>
</span>