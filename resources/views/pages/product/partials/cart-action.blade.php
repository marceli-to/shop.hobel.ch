@if($product->flat_rate_shipping)
  <div class="mt-40" x-ref="cartBtn">
    @livewire('cart.button', ['productUuid' => $productUuid], key($wireKey))
  </div>
@else
  <div class="mt-40">
    <p>Bitte kontaktieren Sie uns (E-Mail: <a href="mailto:shop@hobel.ch" class="font-sans">shop@hobel.ch</a>)</p>
  </div>
@endif
