@props(['inCart' => false, 'disabled' => false])

<button
	type="button"
	wire:click="addToCart"
	wire:loading.attr="disabled"
	class="font-muoto-regular font-regular w-full border border-black h-40 cursor-pointer hover:bg-olive hover:border-olive hover:text-white transition-colors"
	{{ ($inCart || $disabled) ? 'disabled' : '' }}>
	<span wire:loading.remove wire:target="addToCart">In den Warenkorb</span>
  <span wire:loading wire:target="addToCart">Wird hinzugefügt...</span>
</button>
