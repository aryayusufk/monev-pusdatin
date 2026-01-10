<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex flex-row gap-2 items-center justify-center p-3 bg-acc-base-default border border-transparent rounded-lg font-normal text-sm leading-none text-txt-brand-on hover:bg-acc-base-secondary transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
