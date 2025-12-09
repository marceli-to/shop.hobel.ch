@props([
  'size' => 'lg'
])

@if ($size == 'lg')
  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => '']) }}>
    <path d="M28.09 0L15 13.09L1.91 0L0 1.91L13.09 15L0 28.09L1.91 30L15 16.91L28.09 30L30 28.09L16.91 15L30 1.91L28.09 0Z" fill="currentColor"/>
  </svg>
@endif

@if ($size == 'md')
  <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => '']) }}>
    <path d="M20.94 0L11 9.94L1.06 0L0 1.06L9.94 11L0 20.94L1.06 22L11 12.06L20.94 22L22 20.94L12.06 11L22 1.06L20.94 0Z" fill="currentColor"/>
  </svg>
@endif

@if ($size == 'sm')
  <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => '']) }}>
    <path d="M10.94 0L6 4.94L1.06 0L0 1.06L4.94 6L0 10.94L1.06 12L6 7.06L10.94 12L12 10.94L7.06 6L12 1.06L10.94 0Z" fill="currentColor"/>
  </svg>
@endif