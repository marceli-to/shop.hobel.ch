<div class="w-full mt-32" x-data="{ quantity: {{ $itemsInCart }} }">
  <div class="flex items-center justify-between border-y border-black min-h-32">
    <button 
      x-on:click="quantity > 1 ? quantity-- : null" 
      class="w-full min-h-30 text-center flex items-center justify-center leading-none">
      <x-icons.minus />
    </button>
    <input 
      x-model="quantity" 
      type="number" 
      class="w-48 min-h-30 p-0 text-sm text-center border-none appearance-none focus:outline-none focus:border-none outline-none !ring-0 !shadow-none" 
      min="1">
    <button 
      x-on:click="quantity++" 
      class="w-full min-h-30 text-center flex items-center justify-center leading-none">
      <x-icons.plus />
    </button>
  </div>
  <button 
    wire:click="addToCart(quantity)" 
    wire:loading.class="pointer-events-none !bg-flame !border-flame !text-white"
    class="min-h-32 mt-32 font-bold leading-none w-full bg-white border border-black hover:border-flame hover:bg-flame hover:text-white transition-all">
    <span wire:loading.class="hidden">Erwerben</span>
    <span wire:loading class="hidden">Wird erworben...</span>
  </button>
</div>