<div class="hidden md:block sm:ml-16">
  <a 
    href="javascript:;"
    class="js-filter-reset-btn">
    Alle
  </a>
   / 
  @foreach($categories as $category)
    <a 
      href="javascript:;" 
      class="js-filter-btn"
      data-category="{{ $category->id }}">
      {{ $category->name }}
    </a>
    @unless($loop->last) /  @endunless
  @endforeach
</div>