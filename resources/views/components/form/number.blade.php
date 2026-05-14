@props([
  'min' => null,
  'max' => null,
  'step' => '1',
])
<input
  type="number"
  step="{{ $step }}"
  @if($min !== null) min="{{ $min }}" @endif
  @if($max !== null) max="{{ $max }}" @endif
  x-data
  @blur="
    if ($el.value === '') return;
    let n = Number($el.value);
    if (Number.isNaN(n)) return;
    if ($el.min !== '' && n < Number($el.min)) n = Number($el.min);
    if ($el.max !== '' && n > Number($el.max)) n = Number($el.max);
    if (n !== Number($el.value)) {
      $el.value = n;
      $el.dispatchEvent(new Event('input', { bubbles: true }));
      $el.dispatchEvent(new Event('change', { bubbles: true }));
    }
  "
  {{ $attributes->class([
    'w-full bg-transparent outline-none placeholder:text-ash text-right',
    '[appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0 [&::-webkit-outer-spin-button]:m-0',
  ]) }} />
