<div class="relative">

  @if($order)
  <div class="lg:grid lg:grid-cols-6 gap-x-20">

    <!-- Confirmation Details -->
    <div class="lg:col-span-4 flex flex-col gap-y-40">

      <!-- Thank you message -->
      <div>
        <x-layout.row class="border-t-0">
          <span>Herzlichen Dank für Ihre Bestellung. Die Bestellbestätigung erhalten Sie per E-Mail an {{ $order->invoice_email }}.</span>
        </x-layout.row>
      </div>

      <div>

        <!-- Order Items -->
        @foreach($order->items as $item)
          <x-layout.row class="justify-between">
            <span class="font-sans">{{ $item->product_name }}</span>
            <x-cart.money :amount="$item->product_price * $item->quantity" />
          </x-layout.row>
        @endforeach

        <!-- Shipping -->
        @if($order->shipping > 0)
          <x-layout.row class="justify-between">
            <span>Versand</span>
            <x-cart.money :amount="$order->shipping" />
          </x-layout.row>
        @endif

        <!-- Subtotal (Net) -->
        <x-layout.row class="justify-between">
          <span>Netto</span>
          <x-cart.money :amount="$order->subtotal" />
        </x-layout.row>

        <!-- Tax -->
        <x-layout.row class="justify-between">
          <span>MwSt. ({{ config('invoice.tax_rate') }}%)</span>
          <x-cart.money :amount="$order->tax" />
        </x-layout.row>

        <!-- Total -->
        <x-layout.row class="justify-between font-sans border-b">
          <span>Total</span>
          <x-cart.money :amount="$order->total" />
        </x-layout.row>

      </div>
      
      <div>
        <!-- Payment Info -->
        <x-layout.row class="justify-between border-b">
          <span>Zahlung: {{ $order->payment_method === 'invoice' ? 'Rechnung' : 'Kreditkarte' }} / {{ $order->created_at->format('d.m.Y, H:i') }}</span>
          <x-cart.money :amount="$order->total" />
        </x-layout.row>
      
      </div>
      
      <!-- Addresses -->
      <div class="flex flex-col gap-y-40">
        <x-ui.accordion title="Rechnungsadresse">
          @if($order->invoice_salutation)
            <x-layout.row class="border-t-0">
              <span>{{ $order->invoice_salutation }}</span>
            </x-layout.row>
          @endif
          <x-layout.row class="{{ !$order->invoice_salutation ? 'border-t-0' : '' }}">
            <span>{{ $order->invoice_name }}</span>
          </x-layout.row>
          <x-layout.row>
            <span>{{ $order->invoice_address }}</span>
          </x-layout.row>
          <x-layout.row>
            <span>{{ $order->invoice_location }}</span>
          </x-layout.row>
          <x-layout.row class="border-b">
            <span>{{ $order->invoice_country }}</span>
          </x-layout.row>
        </x-ui.accordion>

        <x-ui.accordion title="Lieferadresse">
          @if($order->use_invoice_address)
            <x-layout.row class="border-t-0 border-b">
              <span>Identisch mit Rechnungsadresse</span>
            </x-layout.row>
          @else
            @if($order->shipping_salutation)
              <x-layout.row class="border-t-0">
                <span>{{ $order->shipping_salutation }}</span>
              </x-layout.row>
            @endif
            <x-layout.row class="{{ !$order->shipping_salutation ? 'border-t-0' : '' }}">
              <span>{{ $order->shipping_name }}</span>
            </x-layout.row>
            <x-layout.row>
              <span>{{ $order->shipping_address }}</span>
            </x-layout.row>
            <x-layout.row>
              <span>{{ $order->shipping_location }}</span>
            </x-layout.row>
            <x-layout.row class="border-b">
              <span>{{ $order->shipping_country }}</span>
            </x-layout.row>
          @endif
        </x-ui.accordion>
      </div>

      <!-- Delivery Info -->
      <x-layout.row class="border-t-0">
        <span>Die Lieferfrist für Ihren Tisch beträgt ca. 4 Wochen. Gerne kontaktieren wir Sie, sobald er abholbereit ist.</span>
      </x-layout.row>

      <!-- Back to Shop -->
      <x-form.button route="{{ route('page.landing') }}" :title="'Zurück zum Shop'" />

    </div>

    <!-- Empty right column for layout consistency -->
    <div class="hidden lg:block lg:col-span-2"></div>

  </div>
  @else
    <x-layout.row>
      <span>Bestellung nicht gefunden.</span>
    </x-layout.row>
  @endif

</div>
