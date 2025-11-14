<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="w-full flex flex-col gap-6">
        @csrf

        <!-- Name -->
        <div class="flex flex-col gap-2">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="flex flex-col gap-2">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block w-full" type="text" name="username" :value="old('username')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-2">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col gap-2">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex w-full items-center justify-center">
            <x-primary-button class="w-full">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
