@props(
  [
    'type' => 'text', 
    'placeholder' => '', 
    'value' => '',
    'name' => '',
    'required' => false
  ]
)
 <input 
  type="{{ $type }}" 
  name="{{ $name }}" 
  placeholder="{{ $placeholder }}{{ $required ? ' *' : '' }}" 
  class="text-sm color-black placeholder:text-black w-full border-none !ring-0 p-0 @error($name) text-flame placeholder:text-flame @enderror"
  value="{{ $value }}">

