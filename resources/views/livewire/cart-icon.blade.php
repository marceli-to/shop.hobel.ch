<div>
  <a 
    href="javascript:;" 
    class="fixed top-20 right-16 inline-flex gap-x-10 z-100"
    x-on:click="$dispatch('toggle-cart')">
    @if ($cartItemCount > 0)
      <x-dynamic-component :component="'icons.quantities.' . $cartItemCount" class="w-20 h-21" />
      <x-icons.cart />
    @endif
  </a>
</div>