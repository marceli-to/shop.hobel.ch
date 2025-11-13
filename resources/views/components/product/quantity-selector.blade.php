@props(['quantity', 'maxStock' => null])

<div class="flex items-center gap-2">
	<button
		type="button"
		wire:click="decrement"
		class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
		{{ $quantity <= 1 ? 'disabled' : '' }}>
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
		</svg>
	</button>

	<span class="w-12 text-center font-muoto">{{ $quantity }}</span>

	<button
		type="button"
		wire:click="increment"
		class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors {{ $maxStock && $quantity >= $maxStock ? 'opacity-50 cursor-not-allowed' : '' }}"
		{{ $maxStock && $quantity >= $maxStock ? 'disabled' : '' }}>
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
		</svg>
	</button>
</div>
