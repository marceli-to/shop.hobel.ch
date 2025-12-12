<div class="relative">

  @if($errors->any())
    <x-form.alert type="error">
      Bitte füllen Sie die markierten Pflichtfelder aus.
    </x-form.alert>
  @endif

  <form wire:submit="save" class="lg:grid lg:grid-cols-6 lg:gap-x-20">

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="salutation">
        Anrede
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="salutation" wire:model="salutation" placeholder="Anrede" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="firstname" :required="true" :error="$errors->has('firstname')">
        Vorname
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="firstname" wire:model="firstname" placeholder="Vorname" :error="$errors->has('firstname')" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="lastname" :required="true" :error="$errors->has('lastname')">
        Nachname
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="lastname" wire:model="lastname" placeholder="Nachname" :error="$errors->has('lastname')" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="street" :required="true" :error="$errors->has('street')">
        Strasse
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="street" wire:model="street" placeholder="Strasse" :error="$errors->has('street')" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="street_number">
        Hausnummer
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="street_number" wire:model="street_number" placeholder="Hausnummer" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="zip" :required="true" :error="$errors->has('zip')">
        PLZ
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="zip" wire:model="zip" placeholder="PLZ" :error="$errors->has('zip')" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40">
      <x-form.label for="city" :required="true" :error="$errors->has('city')">
        Ort
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40">
      <x-form.input id="city" wire:model="city" placeholder="Ort" :error="$errors->has('city')" />
    </x-misc.row>

    <x-misc.row class="hidden lg:block lg:col-span-2 h-40 border-b">
      <x-form.label for="country" :required="true" :error="$errors->has('country')">
        Land
      </x-form.label>
    </x-misc.row>
    <x-misc.row class="col-span-4 h-40 border-b">
      <x-form.input id="country" wire:model="country" placeholder="Schweiz" :error="$errors->has('country')" />
    </x-misc.row>

    <x-form.button type="submit" :title="'Zahlung'" class="col-span-6 mt-40" />

  </form>
</div>
