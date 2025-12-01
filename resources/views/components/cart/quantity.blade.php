@props(['quantity', 'maxStock' => null])

<div class="flex items-center border-black border h-40">

	<button
		type="button"
		wire:click="decrement"
		class="w-[30%] flex items-center justify-end cursor-pointer h-full {{ $quantity <= 1 ? '' : '' }}"
		{{ $quantity <= 1 ? 'disabled' : '' }}>
    <x-icons.minus />
	</button>

	<span class="w-[40%] text-center">
    {{ $quantity }}
  </span>

	<button
		type="button"
		wire:click="increment"
		class="w-[30%] flex items-center justify-start cursor-pointer h-full {{ $maxStock && $quantity >= $maxStock ? '' : '' }}"
		{{ $maxStock && $quantity >= $maxStock ? 'disabled' : '' }}>
    <x-icons.plus />
	</button>
  
</div>
