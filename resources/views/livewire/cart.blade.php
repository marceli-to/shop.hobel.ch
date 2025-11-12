<div class="container mx-auto px-4 py-8">
	<h1 class="text-3xl font-muoto mb-8">Warenkorb</h1>

	@if(empty($cart['items']))
		<div class="bg-gray-50 rounded-lg p-12 text-center">
			<svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
			</svg>
			<h2 class="text-xl mb-2">Ihr Warenkorb ist leer</h2>
			<p class="mb-6">Fügen Sie Produkte hinzu, um mit dem Einkauf zu beginnen.</p>
			<a
				href="{{ route('products.index') }}"
				class="inline-block bg-black text-white px-6 py-3 rounded-lg font-muoto hover:bg-gray-800 transition-colors"
			>
				Produkte entdecken
			</a>
		</div>
	@else
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
			<!-- Cart Items -->
			<div class="lg:col-span-2 space-y-4">
				@foreach($cart['items'] as $item)
					<div class="bg-white border rounded-lg p-4" wire:key="cart-item-{{ $item['uuid'] }}">
						<div class="flex gap-4">
							@if($item['image'])
								<a href="{{ route('product.show', $item['uuid']) }}" class="flex-shrink-0">
									<img
										src="/img/{{ $item['image'] }}?w=192&h=192&fit=crop"
										alt="{{ $item['name'] }}"
										class="w-24 h-24 object-cover rounded-lg"
									>
								</a>
							@else
								<div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
									<span class="text-xs">Kein Bild</span>
								</div>
							@endif

							<div class="flex-1 min-w-0">
								<div class="flex justify-between items-start mb-2">
									<div>
										<h3 class="font-muoto text-lg">{{ $item['name'] }}</h3>
										@if($item['description'])
											<p class="text-sm mt-1">{{ $item['description'] }}</p>
										@endif
									</div>
									<button
										wire:click="removeItem('{{ $item['uuid'] }}')"
										class="p-2 hover:bg-gray-100 rounded-lg transition-colors ml-4"
										aria-label="Entfernen"
									>
										<svg class="w-5 h-5 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
										</svg>
									</button>
								</div>

								<div class="flex items-center justify-between mt-4">
									<div class="text-lg font-semibold">
										CHF {{ number_format($item['price'], 2) }}
									</div>

									<!-- Cart Button -->
									<livewire:cart-button
										:productUuid="$item['uuid']"
										:key="'cart-page-button-' . $item['uuid']"
									/>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>

			<!-- Cart Summary -->
			<div class="lg:col-span-1">
				<div class="bg-gray-50 rounded-lg p-6 sticky top-4">
					<h2 class="text-xl font-muoto mb-4">Zusammenfassung</h2>

					<div class="space-y-3 mb-6">
						<div class="flex justify-between">
							<span>Zwischensumme</span>
							<span>CHF {{ number_format($cart['total'] ?? 0, 2) }}</span>
						</div>
						<div class="flex justify-between">
							<span>Versand</span>
							<span>Wird beim Checkout berechnet</span>
						</div>
					</div>

					<div class="border-t pt-4 mb-6">
						<div class="flex justify-between text-xl font-muoto">
							<span>Total</span>
							<span>CHF {{ number_format($cart['total'] ?? 0, 2) }}</span>
						</div>
					</div>

					<a
						href="#"
						class="block w-full bg-black text-white text-center px-6 py-3 rounded-lg font-muoto hover:bg-gray-800 transition-colors mb-3"
					>
						Zur Kasse
					</a>

					<a
						href="{{ route('products.index') }}"
						class="block w-full bg-white text-black text-center px-6 py-3 rounded-lg font-muoto border border-gray-300 hover:bg-gray-50 transition-colors"
					>
						Weiter einkaufen
					</a>
				</div>
			</div>
		</div>
	@endif
</div>
