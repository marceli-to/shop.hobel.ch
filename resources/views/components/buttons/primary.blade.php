@props(['route' => null, 'label', 'type' => 'link'])
@if ($type === 'link')
  <a 
    href="{{ $route }}" 
    {{ $attributes->merge(['class' => 'min-h-32 w-full flex items-center leading-none space-x-6 hover:text-flame group-hover:text-flame border-y border-y-black hover:border-y-flame transition-all']) }}
    title="Bestellung abschliessen">
    <x-icons.chevron-right-tiny class="w-6 h-auto" />
    <span>{{ $label }}</span>
  </a>
@else
  <button 
    {{ $attributes->merge(['class' => 'min-h-32 w-full flex items-center leading-none space-x-6 hover:text-flame group-hover:text-flame border-y border-y-black hover:border-y-flame transition-all']) }}
    type="submit">
    <x-icons.chevron-right-tiny class="w-6 h-auto" />
    <span>{{ $label }}</span>
  </button>
@endif