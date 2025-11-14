<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center p-2 bg-acc-base-default border border-transparent rounded-lg font-normal text-base text-txt-brand-on hover:bg-acc-base-secondary transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
