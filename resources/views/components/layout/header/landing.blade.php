<header 
  x-data="dynamicHeader"
  data-dynamic-header="true"
  class="
   bg-white
    w-full
    top-0
    z-40
    pt-20
    lg:pt-32
    sticky
    transition-[height]
    will-change-[height]
    duration-300
    lg:duration-0
    h-[var(--header-height-sm)]
    lg:h-[var(--header-height-lg)]"   
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

      <h1 
        class="
          text-lg 
          leading-none">
        Seitentitel
      </h1>


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
          data-dynamic-logo
          class="
            block
            w-auto 
            lg:h-80 
            transition-[height] 
            will-change-[height] 
            duration-300 
            lg:duration-0">
            <x-icons.logo class="w-auto h-full" />
        </a>
        
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
