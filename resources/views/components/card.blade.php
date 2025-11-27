@props([
    'text' => 'Text',
])

<div
    class="min-w-[240px] flex flex-col justify-center items-center bg-bg-base-default border border-bor-base-default rounded-lg p-6 gap-6">
    <p class="w-full text-center text-2xl font-semibold">{{ $text }}</p>
</div>
