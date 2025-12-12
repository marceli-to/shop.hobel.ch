<div class="relative">

  <form wire:submit="save" class="lg:grid lg:grid-cols-6 lg:gap-x-20">

    <x-layout.row class="col-span-3 lg:border-b">
      <label class="flex items-center cursor-pointer w-full h-80">
        <span class="block w-35">
          <input 
            type="radio" 
            name="payment_method" 
            value="creditcard"
            wire:model="payment_method"
            class="peer sr-only">
          <x-icons.radio-unchecked class="peer-checked:hidden" />
          <x-icons.radio-checked class="hidden peer-checked:block" />
        </span>
        <span class="flex items-center gap-x-20">
          <x-icons.payment-creditcard class="w-60 h-auto text-black" />
          <span>Kreditkarte</span>
        </span>
      </label>
    </x-layout.row>

    <x-layout.row class="col-span-3 border-b">
      <label class="flex items-center cursor-pointer w-full h-80">
        <span class="block w-35">
          <input 
            type="radio" 
            name="payment_method" 
            value="invoice"
            wire:model="payment_method"
            class="peer sr-only">
          <x-icons.radio-unchecked class="peer-checked:hidden" />
          <x-icons.radio-checked class="hidden peer-checked:block" />
        </span>
        <span class="flex items-center gap-x-20">
          <x-icons.payment-invoice />
          <span>Rechnung</span>
        </span>
      </label>
    </x-layout.row>

    <x-form.button type="submit" :title="'Zusammenfassung'" class="col-span-6 mt-40" />

  </form>
</div>
