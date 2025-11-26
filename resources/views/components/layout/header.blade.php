@props([
  'dynamic' => false,
  'title' => 'Shop',
  'showMobileTitle' => true,
  'showDesktopTitle' => true,
])

<header
  @if($dynamic)
  x-data="dynamicHeader"
  data-dynamic-header="true"
  @endif
  class="
   bg-white
    w-full
    top-0
    z-40
    pt-20
    lg:pt-32
    sticky
    h-(--header-height-sm)
    lg:h-(--header-height-lg)
    @if($dynamic)
    transition-[height]
    will-change-[height]
    duration-300
    lg:duration-0
    @endif"
  :class="{ '!bg-white' : menu }">

  <div
    class="
      lg:grid
      lg:grid-cols-12
      lg:gap-20
      h-full
      px-20
      lg:px-0">

    <!-- Title desktop, Backlink -->
    <div
      class="
        hidden
        lg:block
        lg:col-span-6
        lg:pl-20">

      @if($title)
      <h1
        class="
          text-lg
          leading-none">
        {{ $title }}
      </h1>
      @endif

    </div>

    <!-- Logo, Title mobile, Menu buttons -->
    <div
      class="
        flex
        justify-between
        items-start
        w-full
        lg:pt-8
        lg:col-span-5
        lg:col-start-8">

      <!-- Logo, Title mobile, Backlink -->
      <div
        class="
          flex
          items-center
          gap-x-44
          lg:order-2
          lg:pr-45">

        <a
          href="/"
          @if($dynamic)
          data-dynamic-logo
          @endif
          class="
            block
            w-auto
            @if(!$dynamic)
            h-(--logo-height-sm)
            @endif
            lg:h-80
            @if($dynamic)
            transition-[height]
            will-change-[height]
            duration-300
            lg:duration-0
            @endif">
            <x-icons.logo class="w-auto h-full" />
        </a>

        @if($showMobileTitle && $title)
        <!-- Title, Backlink -->
        <h1 class="
          font-muoto-regular
          text-xxs
          leading-none
          lg:hidden">
          {{ $title }}
        </h1>
        @endif

      </div>

      <!-- Menu/Cart Buttons -->
      <div
        class="
          lg:order-1
          lg:-mt-2
          flex
          gap-x-40
          lg:gap-x-80">

        <div class="lg:order-2">
          <x-icons.cart />
        </div>

        <!-- Menu Buttons -->
        <div
          class="
            w-40
            h-30
            flex
            items-center
            justify-center
            lg:order-1">

          <x-menu.buttons.show class="w-40 h-auto" />
          <x-menu.buttons.hide class="w-30 h-auto" />

        </div>

      </div>

    </div>

  </div>

</header>

<x-menu.wrapper />
