@php
$fontPath = resource_path('sidecar-browsershot/fonts/');
$fontMedium = base64_encode(file_get_contents($fontPath . 'Muoto-Medium.woff2'));
$fontRegular = base64_encode(file_get_contents($fontPath . 'Muoto-Regular.woff2'));
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Rechnung {{ $order->order_number }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    
    @font-face {
      font-family: 'Muoto';
      src: url('data:font/woff2;base64,{{ $fontMedium }}') format('woff2');
      font-weight: 500;
      font-style: normal;
    }
    
    @font-face {
      font-family: 'Muoto';
      src: url('data:font/woff2;base64,{{ $fontRegular }}') format('woff2');
      font-weight: 400;
      font-style: normal;
    }

    @page :first {
      margin-top: 6mm;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Muoto', sans-serif;
      font-size: 9pt;
      line-height: 14pt;
      font-weight: 400;
      color: #000;
    }

    strong {
      font-weight: normal;
    }

    .font-medium {
      font-weight: 500;
    }

    /* Sender info - top left at 6mm */
    .sender-info {
      position: absolute;
      top: 0mm;
      left: 0;
      font-size: 9pt;
      line-height: 1.2;
    }

    /* Website - top right */
    .sender-website {
      position: absolute;
      top: 8.4mm;
      left: 171.5mm;
      font-size: 15pt;
      font-weight: 400;
    }

    /* Date - below sender info */
    .date {
      position: absolute;
      top: 25mm;
      left: 42mm;
      font-size: 9pt;
    }

    /* Recipient address - starts at 93mm from top */
    .recipient {
      position: absolute;
      top: 45mm;
      left: 42mm;
      font-size: 9pt;
    }

    /* Invoice header - starts at 92mm from top */
    .invoice-header {
      position: absolute;
      top: 84mm;
      left: 42mm;
      font-size: 14pt;
      font-weight: 400;
    }

    /* Contact details - bottom left */
    .contact-area {
      position: absolute;
      top: 240mm;
      left: 0mm;
      width: 42mm;
    }

    .contact-area .contact-block {
      margin-bottom: 3mm;
    }

    /* Main content area */
    .content {
      position: absolute;
      top: 114mm;
      left: 42mm;
    }

    /* Invoice item table */
    .invoice-item {
      width: 150mm;
      border-collapse: collapse;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .invoice-item + .invoice-item {
      margin-top: 7.5mm;
    }

    .invoice-item .col-qty {
      border-top: 0.15mm solid black;
      width: 8mm;
      vertical-align: top;
      padding: 1.5mm 0 0 0;
    }

    .invoice-item .col-content {
      border-top: 0.15mm solid black;
      vertical-align: top;
    }

    .invoice-row {
      display: flex;
      border-bottom: 0.15mm solid black;
    }

    .invoice-row-label {
      display: flex;
      align-items: center;
      width: 110mm;
      padding: 1.5mm 0;
    }

    .invoice-row-fr {
      width: 6mm;
      padding: 1.5mm 0;
    }

    .invoice-row-price {
      width: 26mm;
      padding: 1.5mm 0;
      text-align: right;
    }

    .invoice-detail-row {
      display: flex;
      align-items: center;
      border-bottom: 0.15mm solid black;
      padding: 1.5mm 0;
    }

    /* Totals table */
    .invoice-totals {
      width: 150mm;
      border-collapse: collapse;
      margin-top: 9mm;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .invoice-totals .col-qty {
      border-top: 0.15mm solid transparent;
      width: 8mm;
      vertical-align: top;
      padding: 1.5mm 0 0 0;
    }

    .invoice-totals .col-content {
      border-top: 0.15mm solid black;
      vertical-align: top;
    }

    /* Payment table */
    .invoice-payment {
      width: 150mm;
      border-collapse: collapse;
      margin-top: 9mm;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .invoice-payment .col-qty {
      border-top: 0.15mm solid transparent;
      width: 8mm;
      vertical-align: top;
      padding: 1.5mm 0 0 0;
    }

    .invoice-payment .col-content {
      border-top: 0.15mm solid black;
      vertical-align: top;
    }

    .no-page-break {
      page-break-inside: avoid;
    }

  </style>
</head>
<body>
  <div class="page">

    <!-- Sender info - top left -->
    <div class="sender-info">
      <span class="font-medium">HOBEL</span><br>
      Genossenschaft<br>
      für Möbel<br>
      und Innenausbau
    </div>

    <!-- Website - top right -->
    <div class="sender-website">
      hobel.ch
    </div>

    <!-- Date -->
    <div class="date">
      Zürich, {{ $order->created_at->locale('de')->isoFormat('D. MMMM YYYY') }}
    </div>

    <!-- Recipient address -->
    <div class="recipient">
      @if ($order->invoice_salutation){{ $order->invoice_salutation }}<br>@endif
      {{ $order->invoice_name }}<br>
      {{ $order->invoice_address }}<br>
      {{ $order->invoice_location }}
    </div>

    <!-- Invoice header -->
    <div class="invoice-header">
      Rechnung Nr. {{ $order->order_number }}
    </div>

    <!-- Contact details - bottom left -->
    <div class="contact-area">
      @include('pdf.partials.contact-details')
    </div>

    <!-- Main content -->
    <div class="content">

      <!-- Items -->
      @foreach($order->items as $index => $item)
        <table cellpadding="0" cellspacing="0" class="invoice-item">
          <tr>
            <td class="col-qty font-medium">{{ $item->quantity }}</td>
            <td class="col-content">
              <div class="invoice-row">
                <div class="invoice-row-label font-medium">{{ $item->product_name }}</div>
                <div class="invoice-row-fr">Fr.</div>
                <div class="invoice-row-price">{!! number_format($item->subtotal, 2, '.', "'") !!}</div>
              </div>
              @if($item->product_description)
                <div class="invoice-detail-row">{{ $item->product_description }}</div>
              @endif
              <div class="invoice-row">
                <div class="invoice-row-label">{{ $order->shipping > 0 ? 'Versand' : 'Abholung' }}</div>
                <div class="invoice-row-fr">Fr.</div>
                <div class="invoice-row-price">{!! number_format($item->quantity > 0 && $loop->first ? $order->shipping : 0, 2, '.', "'") !!}</div>
              </div>
            </td>
          </tr>
        </table>
      @endforeach

      <div class="no-page-break">
        <!-- Totals -->
        <table cellpadding="0" cellspacing="0" class="invoice-totals">
          <tr>
            <td class="col-qty">&nbsp;</td>
            <td class="col-content">
              <div class="invoice-row">
                <div class="invoice-row-label">Netto</div>
                <div class="invoice-row-fr">Fr.</div>
                <div class="invoice-row-price">{!! number_format($order->subtotal + $order->shipping, 2, '.', "'") !!}</div>
              </div>
              <div class="invoice-row">
                <div class="invoice-row-label">MwSt.</div>
                <div class="invoice-row-fr">Fr.</div>
                <div class="invoice-row-price">{!! number_format($order->tax, 2, '.', "'") !!}</div>
              </div>
              <div class="invoice-row">
                <div class="invoice-row-label font-medium">Total</div>
                <div class="invoice-row-fr">Fr.</div>
                <div class="invoice-row-price">{!! number_format($order->total, 2, '.', "'") !!}</div>
              </div>
            </td>
          </tr>
        </table>

        <!-- Payment -->
        <table cellpadding="0" cellspacing="0" class="invoice-payment">
          <tr>
            <td class="col-qty">&nbsp;</td>
            <td class="col-content">
              <div class="invoice-row">
                <div class="invoice-row-label">
                  @if($order->payment_method === 'invoice')
                    Zahlung: Rechnung / {{ $order->created_at->addDays(30)->format('d.m.Y') }}
                  @else
                    Zahlung: {{ ucfirst($order->payment_method) }} / {{ $order->paid_at ? $order->paid_at->format('d.m.Y, H:i') : '' }}
                  @endif
                </div>
                <div class="invoice-row-fr">Fr.</div>
                <div class="invoice-row-price">{!! number_format($order->total, 2, '.', "'") !!}</div>
              </div>
            </td>
          </tr>
        </table>
        
      </div>

    </div>

  </div>
</body>
</html>
