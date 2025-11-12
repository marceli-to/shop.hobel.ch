<header class="sticky top-0 z-30 bg-white border-b shadow-sm">
	<div class="container mx-auto px-4">
		<div class="flex items-center justify-between h-16">
			<!-- Logo / Brand -->
			<a href="{{ route('home') }}" class="text-xl font-bold">
				{{ env('APP_NAME') }}
			</a>

			<!-- Navigation -->
			<nav class="hidden md:flex items-center space-x-6">
				<a href="{{ route('products.index') }}" class="hover:text-gray-600 transition-colors">
					Produkte
				</a>
				<a href="{{ route('cart.index') }}" class="hover:text-gray-600 transition-colors">
					Warenkorb
				</a>
			</nav>

			<!-- Cart Icon -->
			<div class="flex items-center">
				<livewire:cart-icon />
			</div>
		</div>
	</div>

	<!-- Mini Cart -->
	<livewire:cart-mini />
</header>