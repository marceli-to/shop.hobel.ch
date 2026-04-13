<footer 
  class="
    bg-white
    w-full
    min-h-[var(--footer-height-sm)] 
    lg:min-h-[var(--footer-height-lg)]
    pb-40
    px-20
    lg:px-0">

  <div 
    class="
      flex 
      flex-col 
      justify-end 
      gap-y-36
      lg:gap-y-0
      min-h-[inherit] 
      lg:grid 
      lg:grid-cols-12 
      lg:gap-x-20">

    <div 
      class="
        lg:col-span-2 
        lg:col-start-8 
        lg:self-end 
        lg:order-2 
        {{ request()->routeIs('page.landing') ? 'lg:hidden' : '' }}">
      <x-misc.claim />
    </div>

    <div 
      class="
        lg:col-span-2 
        lg:col-start-10
        lg:order-3 
        lg:flex 
        lg:justify-end 
        lg:self-end
        lg:pr-20">
      <x-misc.social-media />
    </div>

    <div 
      class="
        lg:pl-20
        lg:col-span-6 
        lg:order-1 
        lg:self-end 
        [&_a]:no-underline 
        [&_a]:underline-offset-2 
        [&_a]:decoration-1
        [&_a]:hover:underline">
      <x-misc.address />
    </div>

  </div>

</footer>

@livewireScripts
@vite('resources/js/app.js')

</body>

</html>
<!-- made with ❤ by wbg.ch & marceli.to -->