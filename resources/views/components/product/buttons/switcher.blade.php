@props(['value', 'label', 'active' => false])
<a 
  href="javascript:;" 
  class="flex items-center space-x-6 group"
  data-variation-btn="{{ $value }}">
  <span class="w-12 h-12 !outline-none appearance-none !bg-transparent !border-0 !ring-transparent focus:ring-offset-0 {{ $active ? 'bg-[url(../icons/radio-checked.svg)]' : 'bg-[url(../icons/radio-unchecked.svg)]' }} group-hover:bg-[url(../icons/radio-checked.svg)] bg-[length:12px_12px] bg-no-repeat"></span>
  <label>{{ $label }}</label>
</a>
