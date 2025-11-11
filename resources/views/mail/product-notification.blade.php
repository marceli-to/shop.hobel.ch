<x-mail::message>
  <div class="main text-base">
    <h1 style="padding-bottom: 20px;">Anfrage</h1>
    @if ($product->state->value() == 'on_request')
      <div>Ein Kunde möchte ein Produkt auf Anfrage bestellen:</div>
    @endif
    @if ($product->state->value() == 'not_available')
      <div>Ein Kunde möchte Informationen bezüglich der Verfügbarkeit eines Produkts:</div>
    @endif
    <div class="table" style="padding-top: 20px;">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td>Produkt</td>
          <td>{{ $product->title }}</td>
        </tr>
        <tr>
          <td>E-Mail</td>
          <td>{{ $email }}</td>
        </tr>
      </table>
    </div>
    <div class="pt-xl">
      <a href="https://www.instagram.com/fiefelstein/" target="_blank" title="fiefelstein.ch auf Instagram">
        <img src="{{ config('app.url') }}/img/instagram.png" alt="fiefelstein.ch auf Instagram" height="20" width="20" style="display:block; height:auto; width: 20px;">
      </a>
    </div>
  </div>
</x-mail::message>
