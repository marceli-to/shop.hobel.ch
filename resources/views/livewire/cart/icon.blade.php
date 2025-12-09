<div class="relative">

	<button
		wire:click="toggleMiniCart"
		class="relative cursor-pointer"
		aria-label="Warenkorb öffnen">

    <x-icons.cart />

		@if($cartItemCount > 0)
      <div class="absolute top-0 -left-40 lg:left-auto lg:-right-50">
        <x-dynamic-component :component="'icons.cart.' . $cartItemCount" class="w-26 h-auto" />
      </div>
		@endif

	</button>
  
</div>