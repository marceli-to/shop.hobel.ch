<div class="{{ $showButton ? 'space-y-3' : '' }}">
	<x-product.quantity-selector :quantity="$quantity" :maxStock="$maxStock" />

	@if($showButton)
		<x-product.add-button :inCart="$inCart" />
	@endif
</div>