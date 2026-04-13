<div class="relative">

  @if($errors->any())
    <x-form.alert type="error">
      Bitte füllen Sie die markierten Pflichtfelder aus.
    </x-form.alert>
  @endif

  <x-layout.row class="border-b">
    <label class="flex items-center gap-x-20 cursor-pointer">
      <input
        type="checkbox"
        wire:model.live="sameAsInvoice"
        class="peer sr-only"
      >
      <x-icons.radio-unchecked class="peer-checked:hidden" />
      <x-icons.radio-checked class="hidden peer-checked:block" />
      <span>Die Lieferadresse entspricht der Rechnungsadresse</span>
    </label>
  </x-layout.row>

  <form wire:submit="save" class="lg:grid lg:grid-cols-6 lg:gap-x-20 mt-40">

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="salutation" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Anrede
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="salutation" wire:model="salutation" placeholder="Anrede" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="firstname" :required="!$sameAsInvoice" :error="$errors->has('firstname')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Vorname
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="firstname" wire:model="firstname" placeholder="Vorname" :error="$errors->has('firstname')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="lastname" :required="!$sameAsInvoice" :error="$errors->has('lastname')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Nachname
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="lastname" wire:model="lastname" placeholder="Nachname" :error="$errors->has('lastname')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="street" :required="!$sameAsInvoice" :error="$errors->has('street')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Strasse
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="street" wire:model="street" placeholder="Strasse" :error="$errors->has('street')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="street_number" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Hausnummer
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="street_number" wire:model="street_number" placeholder="Hausnummer" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="zip" :required="!$sameAsInvoice" :error="$errors->has('zip')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        PLZ
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="zip" wire:model="zip" placeholder="PLZ" :error="$errors->has('zip')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="city" :required="!$sameAsInvoice" :error="$errors->has('city')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Ort
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40">
      <x-form.input id="city" wire:model="city" placeholder="Ort" :error="$errors->has('city')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-layout.row class="hidden lg:block lg:col-span-2 h-40 border-b">
      <x-form.label for="country" :required="!$sameAsInvoice" :error="$errors->has('country')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}">
        Land
      </x-form.label>
    </x-layout.row>
    <x-layout.row class="col-span-4 h-40 border-b">
      <x-form.input id="country" wire:model="country" placeholder="Schweiz" :error="$errors->has('country')" class="{{ $sameAsInvoice ? 'text-ash' : '' }}" />
    </x-layout.row>

    <x-form.button type="submit" :title="'Zahlung'" class="col-span-6 mt-40" />

  </form>
</div>
