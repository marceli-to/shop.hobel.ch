@props(['text' => '', 'class' => ''])
<article class="product-card {{ $class }}">
  {!! nl2br($text) !!}
</article>