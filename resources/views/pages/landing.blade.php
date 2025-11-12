@extends('app')

@section('content')
	<div class="container mx-auto px-4">
		<!-- Hero Section -->
		<section class="py-16 text-center">
			<h1 class="text-5xl md:text-6xl font-bold mb-6">
				{{ env('APP_NAME') }}
			</h1>
			<a
				href="{{ route('products.index') }}"
				class="inline-block bg-black text-white px-8 py-4 rounded-lg font-semibold hover:bg-gray-800 transition-colors duration-300"
			>
				Zu den Produkten
			</a>
		</section>
	</div>
@endsection
