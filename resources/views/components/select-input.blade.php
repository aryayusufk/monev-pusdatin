@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-bor-base-default rounded-lg shadow-sm']) }}>
    {{ $slot }}
</select>
