<div class="w-full {{ $showButton ? 'space-y-40' : '' }}">

	<x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" :class="$class ?? ''" />

	@if ($showButton)
		<x-cart.button :inCart="$inCart" />
	@endif

</div>