@props([
  'min' => null,
  'max' => null,
  'step' => '1',
])
<input
  type="range"
  step="{{ $step }}"
  @if($min !== null) min="{{ $min }}" @endif
  @if($max !== null) max="{{ $max }}" @endif
  {{ $attributes->class([
    'w-full h-16 cursor-pointer appearance-none bg-transparent focus:outline-none align-middle',
    '[&::-webkit-slider-runnable-track]:h-1 [&::-webkit-slider-runnable-track]:bg-black',
    '[&::-moz-range-track]:h-1 [&::-moz-range-track]:bg-black',
    '[&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-16 [&::-webkit-slider-thumb]:w-16 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border [&::-webkit-slider-thumb]:border-black [&::-webkit-slider-thumb]:[margin-top:-7.5px] [&::-webkit-slider-thumb]:[box-shadow:none]',
    '[&::-moz-range-thumb]:h-16 [&::-moz-range-thumb]:w-16 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-white [&::-moz-range-thumb]:border [&::-moz-range-thumb]:border-black [&::-moz-range-thumb]:box-border [&::-moz-range-thumb]:[box-shadow:none]',
  ]) }} />
