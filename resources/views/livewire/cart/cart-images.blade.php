<div class="flex flex-col gap-y-20 pl-half-col">
  @if(!empty($cart['items']))
    @foreach($cart['items'] as $item)
      @if($item['image'])
        <div wire:key="cart-image-{{ $item['cart_key'] ?? $item['uuid'] }}">
          <img 
            src="{{ url('/img/' . $item['image'] . '?w=300&h=300&fit=contain&bg=ffffff') }}" 
            alt="{{ $item['name'] }}"
            class="w-full"
          >
        </div>
      @endif
    @endforeach
  @endif
</div>
