<div
  class="
    bg-white
    w-[70%]
    lg:w-col-padded-5
    fixed
    right-0
    z-30
    pr-20
    lg:top-[var(--header-height-lg)]
    h-dvh
    lg:h-[calc(100dvh_-_var(--header-height-lg))]
    {{ (request()->routeIs('page.landing')) ? 'pt-138' : 'pt-109' }}
    lg:pt-0"
  x-cloak
  x-show="menu"
  @click.outside="menu = false"
  x-transition:enter="transition ease-out duration-100"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-0"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0">

  <nav class="lg:-mt-3">

    <ul class="flex flex-col gap-y-20 items-end lg:items-start">

      @foreach($menuItems ?? [['url' => route('page.tables'), 'title' => 'Tische']] as $item)
        <x-menu.item
          :url="$item['url'] ?? '#'"
          :title="$item['title'] ?? ''" />
      @endforeach

    </ul>

  </nav>

</div>
