<a
  href="javascript:;"
  x-on:click="menu = !menu"
  x-show="!menu"
  aria-label="Menü anzeigen"
  {{ $attributes->merge(['class' => '']) }}>
  <x-icons.burger class="w-full h-auto" />
</a>
