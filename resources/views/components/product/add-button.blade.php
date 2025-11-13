@props(['inCart' => false, 'disabled' => false])

<button
	type="button"
	wire:click="addToCart"
	wire:loading.attr="disabled"
	class="w-full bg-black text-white px-6 py-3 rounded-lg font-muoto hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
	{{ ($inCart || $disabled) ? 'disabled' : '' }}>
	@if($inCart)
		<span>Im Warenkorb</span>
	@else
		<span wire:loading.remove wire:target="addToCart">In den Warenkorb</span>
		<span wire:loading wire:target="addToCart">Wird hinzugefügt...</span>
	@endif
</button>
