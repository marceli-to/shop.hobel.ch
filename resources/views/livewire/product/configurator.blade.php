<div class="space-y-6">
	@if($this->product && $this->product->isConfigurable())
		<!-- Configuration Options -->
		<div class="space-y-4">
			@foreach($this->product->getConfigurationAttributes() as $attribute)
				<div>
					<label class="block text-sm font-muoto mb-2">
						{{ $attribute['label'] }}
						@if($attribute['required'] ?? false)
							<span class="text-red-500">*</span>
						@endif
					</label>

					<div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
						@foreach($attribute['options'] as $option)
							@php
								$isSelected = isset($configuration[$attribute['key']]) && $configuration[$attribute['key']]['option'] === $option['value'];
								$priceModifier = $option['price_modifier'] ?? 0;
							@endphp
							<button
								type="button"
								wire:click="selectOption('{{ $attribute['key'] }}', '{{ $option['value'] }}')"
								class="px-4 py-3 border-2 rounded-lg transition-all text-left {{ $isSelected ? 'border-black bg-black text-white' : 'border-gray-200 hover:border-gray-400' }}"
							>
								<div class="font-muoto">{{ $option['label'] }}</div>
								@if($priceModifier > 0)
									<div class="text-xs {{ $isSelected ? 'text-gray-200' : 'text-gray-500' }}">
										+CHF {{ number_format($priceModifier, 2, '.', '\'') }}
									</div>
								@endif
								@if(isset($option['description']) && $option['description'])
									<div class="text-xs mt-1 {{ $isSelected ? 'text-gray-300' : 'text-gray-500' }}">
										{{ $option['description'] }}
									</div>
								@endif
							</button>
						@endforeach
					</div>
				</div>
			@endforeach
		</div>

		<!-- Configuration Summary -->
		@if(!empty($configuration))
			<div class="bg-gray-50 p-4 rounded-lg">
				<h4 class="font-muoto text-sm mb-2">Ihre Konfiguration:</h4>
				<div class="space-y-1 text-sm">
					@foreach($configuration as $key => $config)
						<div class="flex justify-between">
							<span class="text-gray-600">{{ $config['label'] }}</span>
							@if($config['price'] > 0)
								<span class="font-muoto">+CHF {{ number_format($config['price'], 2, '.', '\'') }}</span>
							@endif
						</div>
					@endforeach
				</div>
			</div>
		@endif
	@endif

	<!-- Price Display -->
	<div class="flex items-baseline gap-2">
		<span class="text-2xl font-muoto">
			CHF {{ number_format($this->configuredPrice, 2, '.', '\'') }}
		</span>
		@if($this->product && $this->product->isConfigurable() && $this->configuredPrice > $this->product->price)
			<span class="text-sm text-gray-500 line-through">
				CHF {{ number_format($this->product->price, 2, '.', '\'') }}
			</span>
		@endif
	</div>

	<!-- Quantity Selector & Add to Cart -->
	<div class="space-y-3">
		<x-product.quantity-selector :quantity="$quantity" :maxStock="$maxStock" />
		<x-product.add-button :inCart="$inCart" :disabled="!$this->isConfigurationValid" />

		@if(!$this->isConfigurationValid && $this->product && $this->product->isConfigurable())
			<p class="text-sm text-red-600">Bitte wählen Sie alle erforderlichen Optionen aus.</p>
		@endif

		@if(session('error'))
			<p class="text-sm text-red-600">{{ session('error') }}</p>
		@endif
	</div>
</div>
