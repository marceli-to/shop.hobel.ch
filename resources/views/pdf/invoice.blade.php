@php
  $fontPath = resource_path('sidecar-browsershot/fonts/');
  $fontLight = base64_encode(file_get_contents($fontPath . 'Muoto-Light.woff2'));
  $fontRegular = base64_encode(file_get_contents($fontPath . 'Muoto-Regular.woff2'));
@endphp
<html lang="de">
<head>
<title>Rechnung {{ $order->order_number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  @font-face {
    font-family: 'Muoto';
    src: url('data:font/woff2;base64,{{ $fontLight }}') format('woff2');
    font-weight: 300;
    font-style: normal;
  }
  @font-face {
    font-family: 'Muoto';
    src: url('data:font/woff2;base64,{{ $fontRegular }}') format('woff2');
    font-weight: 400;
    font-style: normal;
  }
  strong {
    font-weight: 400;
  }
  body {
    font-family: 'Muoto', sans-serif;
    font-weight: 300;
  }

  th {
    border-bottom: .1mm solid #000000;
    text-align: left;
    font-weight: 500;
    padding-bottom: 2mm;
  }

  td {
    border-bottom: .1mm solid #000000;
    font-weight: 300;
    vertical-align: top;
    padding-bottom: 2mm;
    padding-top: 2mm;
  }

  tr {
    break-inside: avoid;
  }

  .keep-together {
    break-inside: avoid;
  }

  .font-size-xs {
    font-size: 9pt;
    line-height: 13pt;
  }

  .font-size-sm {
    font-size: 10pt;
    line-height: 15pt;
  }

  .font-size-md {
    font-size: 12pt;
    line-height: 16pt;
  }

  .font-size-lg {
    font-size: 16pt;
    line-height: 20pt;
  }

  .invoice-recipient {
    margin-top: 15mm;
  }

  .invoice-info {
    margin-top: 20mm;
  }

  .invoice-positions {
    margin-top: 10mm;
  }

  .payment-info {
    border: .3mm solid #000000;
    max-width: 80mm;
    margin-top: 10mm;
    padding: 1mm 2mm;
    break-inside: avoid;
    page-break-inside: avoid;
    display: inline-block;
    width: 80mm;
  }

  .payment-info table td {
    border: none;
    padding: 1mm 0;
  }

  .footer {
    margin-top: 20mm;
  }

</style>
</head>
<body class="font-light">

  <div>

    <!-- Invoice Recipient Address -->
    <div class="invoice-recipient font-size-sm">
      @if ($order->invoice_salutation)<strong>{{ $order->invoice_salutation }}</strong><br>@endif
      <strong>{{ $order->invoice_name }}</strong><br>
      {{ $order->invoice_address }}<br>
      {{ $order->invoice_location }}<br>
      {{ $order->invoice_country }}
    </div>

    <!-- Invoice Info -->
    <div class="invoice-info grid grid-cols-12">
      <h1 class="font-size-lg col-span-7">
        <strong>Rechnung</strong>
      </h1>
      <div class="font-size-sm col-span-5">
        <div class="flex justify-between">
          <span>Nummer</span>
          <span>{{ $order->order_number }}</span>
        </div>
        <div class="flex justify-between">
          <span>Datum</span>
          <span>{{ $order->created_at->format('d.m.Y') }}</span>
        </div>
        @if($order->payment_method === 'invoice')
          <div class="flex justify-between">
            <span class="font-medium">Zahlbar bis</span>
            <span class="font-medium">{{ $order->created_at->addDays(30)->format('d.m.Y') }}</span>
          </div>
        @endif
      </div>
    </div>

    <!-- Delivery Address (if different) -->
    @if(!$order->use_invoice_address)
      <div class="font-size-sm mt-8">
        <strong>Lieferadresse:</strong><br>
        @if ($order->shipping_salutation){{ $order->shipping_salutation }}<br>@endif
        {{ $order->shipping_name }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_location }}<br>
        {{ $order->shipping_country }}
      </div>
    @endif

    <!-- Invoice Positions -->
    <table class="invoice-positions font-size-sm w-full">
      <thead>
        <th style="width: 50%">Beschreibung</th>
        <th style="width: 15%; text-align: right">Menge</th>
        <th style="width: 17.5%; text-align: right">Preis</th>
        <th style="width: 17.5%; text-align: right">Total</th>
      </thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td>
            <strong>{{ $item->product_name }}</strong>
            @if($item->product_description)
              <br><span class="font-size-xs">{{ $item->product_description }}</span>
            @endif
          </td>
          <td style="text-align: right">{{ $item->quantity }}</td>
          <td style="text-align: right">Fr. {!! number_format($item->product_price, 2, '.', "'") !!}</td>
          <td style="text-align: right">Fr. {!! number_format($item->subtotal, 2, '.', "'") !!}</td>
        </tr>
        @endforeach
        <tr>
          <td colspan="3">Netto</td>
          <td style="text-align: right">Fr. {!! number_format($order->subtotal, 2, '.', "'") !!}</td>
        </tr>
        @if($order->shipping > 0)
        <tr>
          <td colspan="3">Versand</td>
          <td style="text-align: right">Fr. {!! number_format($order->shipping, 2, '.', "'") !!}</td>
        </tr>
        @endif
        <tr>
          <td colspan="3">MwSt. ({{ config('invoice.tax_rate') }}%)</td>
          <td style="text-align: right">Fr. {!! number_format($order->tax, 2, '.', "'") !!}</td>
        </tr>
        <tr>
          <td colspan="3" style="border-bottom: 0.6mm solid #000000"><strong>Total</strong></td>
          <td style="text-align: right; border-bottom: 0.6mm solid #000000"><strong>Fr. {!! number_format($order->total, 2, '.', "'") !!}</strong></td>
        </tr>
      </tbody>
    </table>

    <!-- Payment Info -->
    @if($order->payment_method === 'invoice')
      <div class="payment-info">
        <table class="w-full keep-together">
          <tr>
            <td colspan="2" class="font-size-xs"><strong>Bankverbindung</strong></td>
          </tr>
          <tr>
            <td class="font-size-xs" style="width: 30%;">Bank</td>
            <td class="font-size-xs">{{ config('invoice.bank_name', 'Bank') }}</td>
          </tr>
          <tr>
            <td class="font-size-xs">IBAN</td>
            <td class="font-size-xs">{{ config('invoice.iban', '') }}</td>
          </tr>
          <tr>
            <td class="font-size-xs" style="border: none;">Zugunsten</td>
            <td class="font-size-xs">{!! nl2br(config('invoice.beneficiary', '')) !!}</td>
          </tr>
        </table>
      </div>
    @else
      <div class="font-size-sm mt-8">
        <strong>Bezahlt per Kreditkarte</strong><br>
        @if($order->paid_at)
          Datum: {{ $order->paid_at->format('d.m.Y, H:i') }}
        @endif
      </div>
    @endif

    <!-- Footer -->
    <div class="footer font-size-xs">
      <strong>HOBEL</strong> Genossenschaft für Möbel und Innenausbau<br>
      shop.hobel.ch
    </div>

  </div>

</body>
</html>
