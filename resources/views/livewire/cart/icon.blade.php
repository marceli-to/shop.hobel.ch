<div class="relative">
	<button
		wire:click="toggleMiniCart"
		class="relative cursor-pointer"
		aria-label="Warenkorb öffnen">

    <x-icons.cart />

		@if($cartItemCount > 0)
      <div class="absolute top-0 -right-50">
        <x-dynamic-component :component="'icons.cart.' . $cartItemCount" />
      </div>
		@endif

	</button>
</div>