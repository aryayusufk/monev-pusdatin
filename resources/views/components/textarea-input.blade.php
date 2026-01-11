@props(['disabled' => false])

<textarea @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-bor-base-default rounded-lg shadow-sm px-4 py-3 text-sm']) }}>{{ $slot }}</textarea>
