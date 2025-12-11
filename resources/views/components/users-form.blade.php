@props([
    'title' => 'Form Title',
    'buttonLabel' => 'Button Label',
    'roles' => [],
    'bidangs' => [],
    'user' => null,
])

<div class="flex flex-col w-full p-6 gap-6 rounded-lg bg-bg-base-default border border-bor-base-default">
    <div class="flex flex-row w-full justify-between">
        <x-icon-button onclick="history.back()">
            <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
        </x-icon-button>
        <h1 class="text-2xl font-semibold text-acc-base-default">{{ $title }}</h1>
    </div>

    <form action="{{ $user ? route('users.update', $user) : route('users.store') }}" method="POST" enctype="multipart/form-data"
        class="flex flex-col w-full gap-6 text-base font-normal text-txt-base-default">
        @csrf
        @if ($user)
            @method('PUT')
        @endif

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name', $user->name ?? '')" required
                autofocus autocomplete="name" />
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block w-full" type="text" name="username" :value="old('username', $user->username ?? '')" required
                autofocus autocomplete="username" />
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required
                autocomplete="new-password" />
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="role" :value="__('Role')" />
            <x-select-input id="role" name="role" class="block w-full">
                <option value="" disabled selected>Pilih Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                        {{ $role->label }}
                    </option>
                @endforeach
            </x-select-input>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="bidang_id" :value="__('Bidang')" />
            <x-select-input id="bidang_id" name="bidang_id" class="block w-full">
                <option value="" disabled @selected(!old('bidang_id', $user->bidang_id ?? null))>Pilih Bidang</option>
                @foreach ($bidangs as $bidang)
                    <option value="{{ $bidang->id }}" @selected(old('bidang_id', $user->bidang_id ?? null) == $bidang->id)>{{ $bidang->nama }}
                    </option>
                @endforeach
            </x-select-input>
        </div>

        <div class="flex justify-end">
            <x-primary-button>
                {{ __($buttonLabel) }}
            </x-primary-button>
    </form>
</div>
