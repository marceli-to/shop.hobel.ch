<header class="sticky lg:fixed top-0 lg:left-16 3xl:left-[calc((100vw/12)_-_118px)] z-70 pt-20 w-full lg:w-132 h-145 lg:h-79 bg-white lg:bg-transparent flex justify-between xl:justify-start">
  <x-icons.logo />
  @if (!Route::is('order.*'))
    <livewire:cart-icon />
    <livewire:cart />
  @endif
  <x-menu.button />
</header>
<x-menu.wrapper />