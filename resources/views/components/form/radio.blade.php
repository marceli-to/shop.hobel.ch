@props(
  [
    'name',
    'value',
    'label',
    'checked' => false,
    'data' => null
  ]
)
<div class="flex items-center space-x-14">
  <input 
    type="radio" 
    name="{{ $name }}" 
    value="{{ $value }}"
    id="{{ $value }}"
    {{ $checked ? 'checked' : '' }} 
    class="w-10 h-10 !outline-none appearance-none !bg-transparent !border-0 !ring-transparent focus:ring-offset-0 bg-[url(../icons/radio-unchecked.svg)] checked:bg-[url(../icons/radio-checked.svg)] bg-[length:10px_10px] bg-no-repeat" />
    <label for="{{ $value }}" class="block -mt-1 hover:cursor-pointer select-none">
      {{ $label ?? $slot }}
    </label>
</div>