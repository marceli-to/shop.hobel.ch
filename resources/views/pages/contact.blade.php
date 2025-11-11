@extends('app')
@section('content')
<div class="md:grid md:grid-cols-12 md:gap-x-16 mb-20 lg:mb-0 mt-16 md:pb-64 relative contact-page">
  <div class="md:col-span-6 lg:col-span-3 lg:col-start-3 mb-16 lg:mb-0 text-lg font-europa-light font-light contact-page__imprint">
    {!! $data->imprint !!}
  </div>
  <div class="md:col-span-6 lg:col-span-4 lg:col-start-6 contact-page__toc">
    <h2>{!! $data->toc_title !!}</h2>
    @foreach($data->toc_items as $item)
      <h3 class="flex space-x-16">
        @if ($item['number'])
        <span>{{ $item['number'] }}</span>
        @endif
        <span>{{ $item['title'] }}</span>
      </h3>
      <div>
        {!! $item['text'] !!}
      </div>
    @endforeach
    <div class="contact-page__toc__privacy">
      {!! $data->privacy !!}
    </div>
  </div>
</div>
@endsection