@props(
  [
    'name',
    'value',
    'label',
    'checked' => false,
    'data' => null,
    'iconClass' => ''
  ]
)
<div {{ $attributes->merge(['class' => 'flex items-center space-x-14']) }}>
  <input 
    type="checkbox" 
    name="{{ $name }}" 
    value="{{ $value }}"
    id="{{ $name }}"
    {{ $checked ? 'checked' : '' }} 
    class="{{ $iconClass }} w-10 h-10 !outline-none appearance-none !bg-transparent !border-0 !ring-transparent focus:ring-offset-0 bg-[url(../icons/radio-unchecked.svg)] checked:bg-[url(../icons/radio-checked.svg)] bg-[length:10px_10px] bg-no-repeat" />
    <label for="{{ $name }}" class="block -mt-1 hover:cursor-pointer select-none">
      {!! $label !!}
    </label>
</div>