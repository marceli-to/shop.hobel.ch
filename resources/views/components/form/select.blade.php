<div class="relative w-full">
  <select
    {{ $attributes->class(['w-full border-none px-12 py-6 h-40 pr-24 text-right bg-white appearance-none cursor-pointer focus:outline-none truncate']) }}>
    {{ $slot }}
  </select>
  <x-icons.chevron-down class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none" />
</div>
