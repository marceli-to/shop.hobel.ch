@props(['title', 'open' => false])

@php
  $panelId = 'accordion-panel-' . uniqid();
@endphp

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }">
  <button
    type="button"
    @click="open = !open"
    :aria-expanded="open"
    aria-controls="{{ $panelId }}"
    class="min-h-40 w-full flex items-center justify-between border-t border-b border-black cursor-pointer"
  >
    <span class="font-sans">{{ $title }}</span>
    <x-icons.chevron-down
      class="transition-transform duration-200"
      ::class="{ 'rotate-180': open }"
    />
  </button>

  <div id="{{ $panelId }}" x-show="open" x-collapse>
    {{ $slot }}
  </div>
</div>
