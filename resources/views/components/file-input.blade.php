@props([
    'disabled' => false,
    'accept' => 'application/pdf', // default: hanya PDF
])

<input
    type="file"
    @disabled($disabled)
    accept="{{ $accept }}"
    {{ $attributes->merge(['class' => 'border-bor-base-default']) }}
>
