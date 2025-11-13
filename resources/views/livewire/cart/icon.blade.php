<div class="relative">
	<button
		wire:click="toggleMiniCart"
		class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200"
		aria-label="Warenkorb öffnen"
	>
		<x-icons.cart class="w-8 h-8" />

		@if($cartItemCount > 0)
			<span class="absolute -top-1 -right-1 bg-black text-white text-xs font-muoto rounded-full w-6 h-6 flex items-center justify-center">
				{{ $cartItemCount }}
			</span>
		@endif
	</button>
</div>