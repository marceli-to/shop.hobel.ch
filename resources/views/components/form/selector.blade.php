@props([
  'items',
  'label' => null,
  'selected' => null,
  'valueKey' => 'id',
  'labelKey' => 'name',
  'imageKey' => 'image.file_path',
  'class' => '',
  ])

@php
  $modelName = $attributes->wire('model')->value();
@endphp

<div 
  x-data="{ open: false }" 
  class="{{ $class }}">

  <x-layout.row
    class="grid grid-cols-3 gap-x-20 cursor-pointer select-none"
    x-on:click="open = !open">

    <div class="col-span-1">
      {{ $label }}
    </div>

    <div class="col-span-2 flex items-center justify-end gap-10">
      <span class="truncate">{{ $selected }}</span>
      <x-icons.chevron-down class="transition-transform" x-bind:class="open ? '-rotate-180' : ''" />
    </div>

  </x-layout.row>

  <x-layout.row
    x-show="open"
    x-collapse
    x-cloak
    class="grid grid-cols-3 gap-x-20 items-start !border-t-0 pb-20">
    @foreach($items as $item)

      @php
        $itemValue = data_get($item, $valueKey);
        $itemLabel = data_get($item, $labelKey);
        $itemImage = data_get($item, $imageKey);
      @endphp

      <label class="block cursor-pointer pt-12">

        <input
          type="radio"
          value="{{ $itemValue }}"
          @if($modelName) wire:model.live="{{ $modelName }}" @endif
          x-on:change="open = false"
          class="peer sr-only">

        <span class="block mb-6 text-xxs truncate">
          {{ $itemLabel }}
        </span>

        <div class="aspect-square w-full overflow-hidden bg-neutral-200">
          @if($itemImage)
            <x-media.image :src="$itemImage" class="w-full h-full object-cover" />
          @endif
        </div>

      </label>
    @endforeach
  </x-layout.row>
</div>
