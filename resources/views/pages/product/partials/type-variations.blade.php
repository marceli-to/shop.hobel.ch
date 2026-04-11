@php
  $childrenData = $product->children->map(fn($c) => [
    'uuid' => $c->uuid,
    'label' => $c->label,
    'price' => (float) $c->price,
  ])->values();
  $firstChild = $product->children->first();
@endphp

<div
  x-data="{
    children: {{ Js::from($childrenData) }},
    selectedUuid: '{{ $firstChild->uuid }}',
    get selected() {
      return this.children.find(c => c.uuid === this.selectedUuid) || this.children[0];
    },
    formatPrice(price) {
      return new Intl.NumberFormat('de-CH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price);
    }
  }"
  x-init="$watch('selectedUuid', value => {
    const el = $refs.cartBtn?.querySelector('[wire\\:id]');
    if (el) Livewire.find(el.getAttribute('wire:id')).call('switchProduct', value);
  })"
>
  <x-layout.row>
    <span x-text="selected.label"></span>
  </x-layout.row>

  @include('pages.product.partials.attributes')
  @include('pages.product.partials.delivery-time')

  <x-layout.row class="border-b border-b-black">
    <span>CHF <span x-text="formatPrice(selected.price)"></span></span>
  </x-layout.row>

  <div class="mt-40 relative">
    <select
      x-model="selectedUuid"
      class="w-full border border-black px-12 py-6 h-40 pr-32 bg-white appearance-none cursor-pointer focus:outline-none">
      @foreach($product->children as $child)
        <option value="{{ $child->uuid }}">
          {{ $child->label }}
        </option>
      @endforeach
    </select>
    <x-icons.chevron-down class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none" />
  </div>

  @include('pages.product.partials.cart-action', [
    'productUuid' => $firstChild->uuid,
    'wireKey' => 'cart-btn-variations',
    'xRef' => 'cartBtn',
  ])

  @include('pages.product.partials.description')
</div>
