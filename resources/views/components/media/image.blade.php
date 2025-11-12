<picture>
	@foreach($formats as $format)
		@if($format !== 'jpg' && $format !== 'jpeg')
			<source
				srcset="{{ $buildUrl($format) }}"
				type="{{ $getMimeType($format) }}"
			>
		@endif
	@endforeach

	<img
		src="{{ $buildUrl('jpg') }}"
		alt="{{ $alt }}"
		@if($width) width="{{ $width }}" @endif
		@if($height) height="{{ $height }}" @endif
		@if($class) class="{{ $class }}" @endif
		loading="{{ $loading }}"
		{{ $attributes }}
	>
</picture>