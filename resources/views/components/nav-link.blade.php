@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex p-2 text-center items-center justify-center bg-acc-base-default text-base font-normal text-txt-brand-on transition duration-150 ease-in-out'
            : 'flex p-2 text-center items-center justify-center text-base font-normal text-txt-base-default transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
