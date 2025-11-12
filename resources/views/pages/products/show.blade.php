@extends('app')

@section('content')
	<div class="container mx-auto px-4 py-8">
		<div class="mb-6">
			<a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
				← Zurück zu den Produkten
			</a>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
			<!-- Product Image -->
			<div class="bg-gray-100 rounded-lg overflow-hidden">
				@if($product->image)
					<x-media.image
						:src="$product->image"
						:alt="$product->name"
						:width="800"
						fit="crop"
						class="w-full h-auto object-cover"
					/>
				@else
					<div class="w-full h-96 flex items-center justify-center bg-gray-200">
						<span class="text-gray-400 text-lg">Kein Bild verfügbar</span>
					</div>
				@endif
			</div>

			<!-- Product Details -->
			<div class="flex flex-col">
				<h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>

				<div class="mb-6">
					<span class="text-3xl font-bold text-gray-900">
						CHF {{ number_format($product->price, 2) }}
					</span>
				</div>

				@if($product->description)
					<div class="mb-6">
						<h2 class="text-lg font-semibold mb-2">Beschreibung</h2>
						<p class="text-gray-700 leading-relaxed">
							{{ $product->description }}
						</p>
					</div>
				@endif

				<!-- Stock Status -->
				<div class="mb-6">
					@if($product->stock > 0)
						<div class="flex items-center text-green-600">
							<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
							</svg>
							<span class="font-medium">Auf Lager ({{ $product->stock }} verfügbar)</span>
						</div>
					@else
						<div class="flex items-center text-red-600">
							<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
							</svg>
							<span class="font-medium">Nicht verfügbar</span>
						</div>
					@endif
				</div>

				<!-- Add to Cart Button -->
				@if($product->stock > 0)
					<div class="mb-8">
						<livewire:cart-button :productUuid="$product->uuid" />
					</div>
				@else
					<div class="mb-8">
						<button
							disabled
							class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-semibold cursor-not-allowed"
						>
							Nicht verfügbar
						</button>
					</div>
				@endif

				<!-- Additional Info -->
				<div class="mt-auto pt-6 border-t">
					<h3 class="font-semibold mb-2">Produkt-ID</h3>
					<p class="text-sm text-gray-600">{{ $product->uuid }}</p>
				</div>
			</div>
		</div>
	</div>
@endsection
