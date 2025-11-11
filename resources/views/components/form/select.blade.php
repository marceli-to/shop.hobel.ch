@props(
  [
    'placeholder' => '', 
    'value' => '',
    'name' => '',
    'required' => false,
    'options' => []
  ]
)

<select 
  name="{{ $name }}"
  {{ $attributes->merge(['class' => 'w-full relative bg-[url("../icons/chevron-down-tiny.svg")] bg-[length:11px_auto] bg-[right_center] border-0 focus:ring-0 text-sm color-black placeholder:text-black p-0 appearance-none']) }}>
  @foreach($options as $option)
    <option value="{{ $option }}" {{ $option == $value ? 'selected' : '' }}>
      {{ $option }}
    </option>
  @endforeach
</select>