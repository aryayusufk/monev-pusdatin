@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-bor-base-default rounded-lg shadow-sm text-sm']) }}>
