@props([
  'quantity', 
  'maxStock' => null, 
  'class' => 'border-black'
])

<div class="w-full flex items-center border {{ $class }} h-40">

	<button
		type="button"
		wire:click="decrement"
		class="w-[30%] flex items-center justify-center cursor-pointer h-full {{ $quantity <= 1 ? '' : '' }}"
		{{ $quantity <= 1 ? 'disabled' : '' }}>
    <x-icons.minus />
	</button>

	<input
    type="number"
    wire:model.blur="quantity"
    min="1"
    {{ $maxStock ? 'max=' . $maxStock : '' }}
    class="w-[40%] h-full text-center font-sans bg-transparent !outline-none no-spinner" />

	<button
		type="button"
		wire:click="increment"
		class="w-[30%] flex items-center justify-center cursor-pointer h-full {{ $maxStock && $quantity >= $maxStock ? '' : '' }}"
		{{ $maxStock && $quantity >= $maxStock ? 'disabled' : '' }}>
    <x-icons.plus />
	</button>
  
</div>
