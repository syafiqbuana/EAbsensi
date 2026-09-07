<x-layouts.auth>
    <div class="min-h-screen flex items-center justify-center px-4 ">

        <flux:card class="light-mode-input space-y-6 w-full max-w-md mx-auto">
            <div>
                <flux:heading class="text-center font-bold" size="xl">{{ config('app.name') }}</flux:heading>
                <flux:text class="mt-2 text-center text-[#979696]">Selamat datang!</flux:text>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="space-y-6">
                    <flux:field>
                        <flux:label class="text-black">Email</flux:label>
                        <flux:input name="email" type="email" :value="old('email')" required autofocus
                            class=" rounded-[15px]" placeholder="Masukkan email" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <div class="mb-3 flex justify-between">
                            <flux:label class="text-black">Password</flux:label>
                        </div>

                        <flux:input name="password" required autocomplete="current-password" class="rounded-[15px] "
                            type="password" placeholder="Masukkan password" />
                        <flux:error name="password" />
                    </flux:field>

                    <div class="flex items-center mt-4">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-2 mt-6 text-black">
                    <flux:button type="submit" variant="primary" color="teal" class="w-full">Masuk</flux:button>
                </div>
            </form>
        </flux:card>

    </div>
</x-layouts.auth>
