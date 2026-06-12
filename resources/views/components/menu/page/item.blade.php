@props(['url', 'title', 'target' => null])

<li>
  <a
    href="{{ $url }}"
    aria-label="{{ $title }}"
    @if($target) target="{{ $target }}" rel="noopener" @endif
    class="font-sans text-md lg:text-lg leading-none">
    {{ $title }}
  </a>
</li>
