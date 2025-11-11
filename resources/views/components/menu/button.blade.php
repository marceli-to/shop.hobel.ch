<a 
  href="javascript:;"
  x-on:click="menu = ! menu"
  class="block mt-60 lg:mt-80 w-25 h-25 lg:fixed lg:left-[calc((100vw/12)_+_14px)]">
  <span x-show="menu === false">
    <x-icons.burger class="w-full h-20" />
  </span>
  <span x-cloak x-show="menu === true">
    <x-icons.cross-large />
  </span>
</a>