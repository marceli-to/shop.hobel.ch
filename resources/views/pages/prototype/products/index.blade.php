@extends('app')

@section('content')
	<div class="container mx-auto px-4 py-8">
		<h1 class="text-3xl font-muoto mb-8">Produkte</h1>

		@if($products->isEmpty())
			<div class="text-center py-12">
				<p class="text-lg">Keine Produkte verfügbar.</p>
			</div>
		@else
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
				@foreach($products as $product)
					<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
						@if($product->image)
							<a href="{{ route('product.show', $product->slug) }}" class="block">
								<x-media.image
									:src="$product->image"
									:alt="$product->name"
									:width="400"
									:height="300"
									fit="crop"
									class="w-full h-64 object-cover"
								/>
							</a>
						@endif

						<div class="p-4">
							<a href="{{ route('product.show', $product->slug) }}" class="block">
								<h2 class="text-xl font-muoto mb-2 hover:text-blue-600 transition-colors">
									{{ $product->name }}
								</h2>
							</a>

							@if($product->description)
								<p class="text-sm mb-4 line-clamp-2">
									{{ $product->description }}
								</p>
							@endif

							<div class="flex items-center justify-between">
								<span class="text-2xl">
									CHF {{ number_format($product->price, 2) }}
								</span>

								@if($product->stock > 0)
									<span class="text-sm text-green-600">
										Auf Lager
									</span>
								@else
									<span class="text-sm text-red-600">
										Nicht verfügbar
									</span>
								@endif
							</div>
						</div>
					</div>
				@endforeach
			</div>

			@if($products->hasPages())
				<div class="mt-8">
					{{ $products->links() }}
				</div>
			@endif
		@endif
	</div>
@endsection
