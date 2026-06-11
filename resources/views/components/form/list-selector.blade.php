@props([
  'items',
  'label' => null,
  'selected' => null,
  'valueKey' => 'id',
  'labelKey' => 'name',
  'class' => '',
  ])

@php
  $modelName = $attributes->wire('model')->value();
@endphp

<div
  x-data="{
    open: false,
    close() {
      this.open = false;
      this.$nextTick(() => {
        document.querySelector('[data-product-configurator]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }
  }"
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

  <div x-show="open" x-cloak>
    @foreach($items as $item)

      @php
        $itemValue = data_get($item, $valueKey);
        $itemLabel = data_get($item, $labelKey);
      @endphp

      <label class="block cursor-pointer">
        <x-layout.row class="cursor-pointer">

          <input
            type="radio"
            value="{{ $itemValue }}"
            @if($modelName) wire:model.live="{{ $modelName }}" @endif
            x-on:change="close()"
            class="peer sr-only">

          <span class="w-full truncate">
            {{ $itemLabel }}
          </span>

        </x-layout.row>
      </label>
    @endforeach
  </div>
</div>
