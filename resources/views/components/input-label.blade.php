@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-normal text-sm text-txt-base-default']) }}>
    {{ $value ?? $slot }}
</label>
