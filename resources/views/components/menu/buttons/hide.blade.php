<a
  href="javascript:;"
  x-cloak
  x-on:click="menu = !menu"
  x-show="menu"
  aria-label="Menü verbergen"
  {{ $attributes->merge(['class' => '']) }}>
  <x-icons.cross class="w-full h-auto" :size="'lg'" />
</a>
