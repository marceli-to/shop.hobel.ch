@extends('app')
@section('content')
<div class="md:grid md:grid-cols-12 md:gap-x-16 mb-20 lg:mb-0 mt-16 md:pb-64 relative idea-page">
  <div class="md:col-span-6 lg:col-span-3 lg:col-start-3 mb-16 lg:mb-0">
    <div class="font-europa-light font-light text-lg leading-[1.1]">
      {!! nl2br($data->quote_text) !!}
    </div>
    {{ $data->quote_author }}
    <div class="mt-32 idea-page__text">
      {!! $data->text !!}
    </div>
  </div>
  <div class="md:col-span-6 lg:col-span-4 lg:col-start-6">
    <h2 class="!mb-16">Partner/innen</h2>
    @foreach($data->partner as $partner)
      <div class="mb-16 3xl:max-w-lg">
        <h3 class="text-lg">
          @if ($partner['website'])
            <a 
              href="{{ $partner['website'] }}" 
              target="_blank" 
              rel="noopener noreferrer"
              title="{{ $partner['title'] }}">
              {{ $partner['title'] }}
            </a>
          @else
            {{ $partner['title'] }}
          @endif
        </h3>
        {{ $partner['description'] }}
      </div>
    @endforeach
  </div>
</div>
@endsection