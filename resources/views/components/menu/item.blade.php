@props(['url' => '', 'title' => '', 'current' => '', 'class' => ''])
<a 
  href="{{ $url }}" 
  title="{{ $title }}" 
  class="{{ $class }} hover:text-flame transition-colors {{ $current ? 'text-flame' : '' }}">
  {{ $title }}
</a>