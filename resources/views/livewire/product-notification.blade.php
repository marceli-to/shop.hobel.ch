<div 
  x-data="{ showForm: false }"
  @hide-submited-form.window="showForm = false">
  <div class="px-16">
    @if ($product->state->value() === 'not_available')
      Gerne benachrichtigen wir Sie, sobald {{ $product->title }} wieder verfügbar ist.
    @elseif ($product->state->value() === 'on_request')
      Hinterlassen Sie uns Ihre E-Mail-Adresse und wir melden uns bei Ihnen.
    @endif
  </div>

  <div x-show="!showForm" class="mt-16">
    <button 
      @click="showForm = true"
      class="min-h-32 font-bold leading-none w-full bg-white border border-black hover:border-flame hover:bg-flame hover:text-white transition-all">
      @if ($product->state->value() === 'not_available')
        Benachrichtigen
      @elseif ($product->state->value() === 'on_request')
        Anfragen
      @endif
    </button>
    
    @if (session()->has('message'))
      <div class="mt-16 px-16">
        {{ session('message') }}
      </div>
    @endif
  </div>

  <div 
    x-cloak 
    x-show="showForm" 
    class="mt-16">
    <form wire:submit.prevent="submit">
      @error('email') 
        <div class="mb-16 text-flame font-europa-bold font-bold">{{ $message }}</div>
      @enderror
      <input 
        autofocus
        type="text" 
        name="email" 
        placeholder="Ihre E-Mail-Adresse"
        @blur="if (!event.target.value) showForm = false"
        wire:model.defer="email"
        class="text-sm bg-flame text-white text-center font-europa-bold font-bold italic placeholder:text-center placeholder:italic placeholder:text-white placeholder:font-europa-bold placeholder:font-bold w-full border-none min-h-32 !ring-0 p-0" />
      <button 
        type="submit"
        class="min-h-32 mt-16 font-bold leading-none w-full bg-white border border-black hover:border-flame hover:bg-flame hover:text-white transition-all"
        wire:loading.class="pointer-events-none !bg-flame !border-flame !text-white">
        <span wire:loading.class="hidden">Senden</span>
        <span wire:loading class="hidden">Wird gesendet...</span>
      </button>
    </form>
  </div>

</div>
