@include('pdf.partials.header')
@include('pdf.partials.css.portrait')
@include('pdf.partials.css.invoice')
<header class="page-header">
  <img src="{{ asset('/img/logo.svg') }}" height="100" width="100">
  <h1>Rechnung</h1>
</header>
<footer class="page-footer">
  Fiefelstein/Flüeler, Binzstrasse 39, 8045 Zürich, anliegen@fiefelstein.ch
</footer>
<div class="page">
  <div class="page-address">
    {{ $data->invoice_name }}<br>
    {{ $data->invoice_address }}<br>
    {{ $data->invoice_location }}
  </div>
  <div class="page-content">
    <div class="page-content-header">
      <div class="font-bold">Rechnung {{ $data->order_number }}</div>
      <div>Zürich, {{ \Carbon\Carbon::now()->locale('de_CH')->isoFormat('D. MMMM YYYY') }}</div>
    </div>
    @foreach($data->orderProducts as $product)
      <table class="order-details">
        <tr>
          <td class="order-detail-item font-bold">
            {{ $product->title }}
          </td>
          <td class="order-detail-item order-detail-item--currency">
          </td>
          <td class="order-detail-item order-detail-item--price align-right font-bold">
            {{ $product->quantity }}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item">
            {{ $product->description }}
          </td>
          <td class="order-detail-item order-detail-item--currency">
            CHF
          </td>
          <td class="order-detail-item order-detail-item--price align-right">
            {!! number_format($product->price, 2, '.', '') !!}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item">
            Verpackung und Versand
          </td>
          <td class="order-detail-item order-detail-item--currency">
            CHF
          </td>
          <td class="order-detail-item order-detail-item--price align-right">
            {!! number_format($product->shipping, 2, '.', '') !!}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item font-bold">
            Total
          </td>
          <td class="order-detail-item order-detail-item--currency font-bold">
            CHF
          </td>
          <td class="order-detail-item order-detail-item--price align-right font-bold">
            {!! number_format($product->price + $product->shipping, 2, '.', '') !!}
          </td>
        </tr>
      </table>
    @endforeach
    <table class="order-details">
      <tr>
        <td class="order-detail-item font-bold">
          Gesamttotal
        </td>
        <td class="order-detail-item order-detail-item--currency font-bold">
          CHF
        </td>
        <td class="order-detail-item order-detail-item--price align-right font-bold">
          {{ $data->total }}
        </td>
      </tr>
    </table>
    <table class="order-details">
      <tr>
        <td class="order-detail-item order-detail-item--address font-bold">
        Lieferadresse
        </td>
      </tr>
      @if ($data->use_invoice_address)
        @if ($data->salutation)
          <tr>
            <td class="order-detail-item">
              {{ $data->salutation }}
            </td>
          </tr>
        @endif
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->invoice_name }}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->invoice_address }}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->invoice_location }}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->country }}
          </td>
        </tr>
      @else
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->shipping_full_name }}
          </td>
        </tr>
        @if ($data->shipping_company)
          <tr>
            <td class="order-detail-item order-detail-item--address">
              {{ $data->shipping_company }}
            </td>
          </tr>
        @endif
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->shipping_address }}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->shipping_location }}
          </td>
        </tr>
        <tr>
          <td class="order-detail-item order-detail-item--address">
            {{ $data->shipping_country }}
          </td>
        </tr>
      @endif
    </table>
    <table class="order-details">
      <tr>
        <td colspan="3" class="order-detail-item order-detail-item--address font-bold">
          Zahlung
        </td>
      </tr>
      <tr>
        <td class="order-detail-item">
          {{ $data->payment_info }}
        </td>
        <td class="order-detail-item order-detail-item--currency font-bold">
          CHF
        </td>
        <td class="order-detail-item order-detail-item--price align-right font-bold">
          {{ $data->total }}
        </td>
      </tr>
      
    </table>
    <p>
      Herzlichen Dank für Ihre Bestellung und freundliche Grüsse<br>Fiefelstein/Flüeler
    </p>
  </div>
</div>
@include('pdf.partials.footer')