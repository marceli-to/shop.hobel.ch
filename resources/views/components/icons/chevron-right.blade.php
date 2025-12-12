@props([
  'size' => 'lg'
])
@if ($size == 'sm')
  <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => '']) }}>
    <path d="M1.06 12L0 10.94L4.94 6L0 1.06L1.06 0L7.06 6L1.06 12Z" fill="currentColor"/>
  </svg>
@endif
