<div class="{{ $showButton ? 'space-y-40' : '' }}">

	<x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" />

	@if ($showButton)
		<x-cart.button :inCart="$inCart" />
	@endif

</div>