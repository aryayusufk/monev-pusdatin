@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-normal text-base text-txt-base-default']) }}>
    {{ $value ?? $slot }}
</label>
