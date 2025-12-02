<button {{ $attributes->merge(['type' => 'button', 'class' => 'flex flex-row gap-2 items-center justify-center w-[36px] h-[36px] p-2 bg-acc-base-secondary border border-transparent rounded-full font-normal text-base leading-none text-txt-brand-on hover:bg-acc-base-tertiary transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
