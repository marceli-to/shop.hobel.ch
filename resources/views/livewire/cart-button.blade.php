<div>
	@if($inCart)
		<!-- In Cart: Show +/- controls -->
		<div class="flex items-center gap-2">
			<button
				wire:click="decrement"
				class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
				{{ $quantity <= 1 ? 'disabled' : '' }}
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
				</svg>
			</button>

			<span class="w-12 text-center font-semibold">{{ $quantity }}</span>

			<button
				wire:click="increment"
				class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors {{ $maxStock && $quantity >= $maxStock ? 'opacity-50 cursor-not-allowed' : '' }}"
				{{ $maxStock && $quantity >= $maxStock ? 'disabled' : '' }}
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
				</svg>
			</button>
		</div>
	@else
		<!-- Not in Cart: Show quantity selector + Add button -->
		<div class="space-y-3">
			<div class="flex items-center gap-2">
				<button
					wire:click="decrement"
					class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
					{{ $quantity <= 1 ? 'disabled' : '' }}
				>
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
					</svg>
				</button>

				<span class="w-12 text-center font-semibold">{{ $quantity }}</span>

				<button
					wire:click="increment"
					class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors {{ $maxStock && $quantity >= $maxStock ? 'opacity-50 cursor-not-allowed' : '' }}"
					{{ $maxStock && $quantity >= $maxStock ? 'disabled' : '' }}
				>
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
					</svg>
				</button>
			</div>

			<button
				wire:click="addToCart"
				wire:loading.attr="disabled"
				class="w-full bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
			>
				<span wire:loading.remove wire:target="addToCart">In den Warenkorb</span>
				<span wire:loading wire:target="addToCart">Wird hinzugefügt...</span>
			</button>
		</div>
	@endif
</div>