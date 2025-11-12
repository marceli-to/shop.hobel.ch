<div>
	<!-- Overlay -->
	@if($show)
		<div
			wire:click="close"
			class="fixed inset-0 bg-white/50 z-40 transition-opacity duration-300"
			x-transition:enter="transition ease-out duration-300"
			x-transition:enter-start="opacity-0"
			x-transition:enter-end="opacity-100"
		></div>
	@endif

	<!-- Mini Cart Panel -->
	<div
		class="fixed top-0 right-0 h-full w-full md:w-96 bg-white shadow-xl z-50 transform transition-transform duration-300 {{ $show ? 'translate-x-0' : 'translate-x-full' }}"
	>
		<div class="flex flex-col h-full">
			<!-- Header -->
			<div class="flex items-center justify-between p-4 border-b">
				<h2 class="text-xl font-muoto">Warenkorb</h2>
				<button
					wire:click="close"
					class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
					aria-label="Schließen"
				>
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
					</svg>
				</button>
			</div>

			<!-- Cart Items -->
			<div class="flex-1 overflow-y-auto p-4">
				@if(empty($cart['items']))
					<div class="text-center py-12">
						<svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
						</svg>
						<p>Ihr Warenkorb ist leer</p>
					</div>
				@else
					<div class="space-y-4">
						@foreach($cart['items'] as $item)
							<div class="flex gap-4 pb-4 border-b" wire:key="mini-cart-item-{{ $item['uuid'] }}">
								@if($item['image'])
									<img
										src="/img/{{ $item['image'] }}?w=160&h=160&fit=crop"
										alt="{{ $item['name'] }}"
										class="w-20 h-20 object-cover rounded-lg"
									>
								@else
									<div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
										<span class="text-xs">Kein Bild</span>
									</div>
								@endif

								<div class="flex-1 min-w-0">
									<h3 class="font-muoto text-sm truncate">{{ $item['name'] }}</h3>
									<p class="text-xs mt-1">
										CHF {{ number_format($item['price'], 2) }}
									</p>

									<!-- Cart Button for this item -->
									<div class="mt-2">
										<livewire:cart-button
											:productUuid="$item['uuid']"
											:key="'cart-mini-button-' . $item['uuid']"
										/>
									</div>
								</div>

								<button
									wire:click="removeItem('{{ $item['uuid'] }}')"
									class="self-start p-1 hover:bg-gray-100 rounded transition-colors"
									aria-label="Entfernen"
								>
									<svg class="w-5 h-5 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
									</svg>
								</button>
							</div>
						@endforeach
					</div>
				@endif
			</div>

			<!-- Footer -->
			@if(!empty($cart['items']))
				<div class="border-t p-4 bg-gray-50">
					<div class="flex items-center justify-between mb-4">
						<span class="font-semibold">Zwischensumme</span>
						<span class="text-xl font-muoto">
							CHF {{ number_format($cart['total'] ?? 0, 2) }}
						</span>
					</div>

					<a
						href="{{ route('cart.index') }}"
						wire:click="close"
						class="block w-full bg-black text-white text-center px-6 py-3 rounded-lg font-muoto hover:bg-gray-800 transition-colors"
					>
						Zum Warenkorb
					</a>
				</div>
			@endif
		</div>
	</div>
</div>
