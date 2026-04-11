<div class="w-full {{ $showButton ? 'space-y-40' : '' }}">
	@if($maxStock !== null && $maxStock < 1)
		<p>Produkt derzeit nicht verfügbar</p>
	@else
		<x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" :class="$class ?? ''" />

		@if ($showButton)
			<x-cart.button :inCart="$inCart" />
		@endif
	@endif
</div>
