<x-layouts::auth :title="__('Acceso para Empresas')">
    <div class="flex flex-col gap-8">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white mb-2">Acceso para Empresas</h2>
            <p class="text-neutral-500 dark:text-neutral-400">Bienvenido de nuevo. Introduce tus credenciales para continuar.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Correo Electrónico')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="ejemplo@empresa.com"
                icon="envelope"
            />

            <!-- Password -->
            <div class="flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <flux:label>{{ __('Contraseña') }}</flux:label>
                    @if (Route::has('password.request'))
                        <flux:link class="text-sm font-medium" :href="route('password.request')" wire:navigate>
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </flux:link>
                    @endif
                </div>
                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                    icon="lock-closed"
                />
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Recordar sesión en este dispositivo')" :checked="old('remember')" />

            <flux:button variant="primary" type="submit" class="w-full py-6 text-lg font-semibold bg-neutral-900 hover:bg-neutral-800 dark:bg-white dark:text-neutral-900" icon-trailing="arrow-right">
                {{ __('Acceso para Empresas') }}
            </flux:button>
        </form>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-neutral-100 dark:border-neutral-800">
            @if (Route::has('register'))
                <div class="text-sm text-neutral-600 dark:text-neutral-400">
                    <span>{{ __('¿No tienes una cuenta?') }}</span>
                    <flux:link :href="route('register')" wire:navigate class="font-bold">{{ __('Solicita un demo') }}</flux:link>
                </div>
            @endif

            <flux:link href="#" class="flex items-center gap-2 text-sm font-medium text-neutral-600 dark:text-neutral-400">
                {{ __('Soporte Técnico') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>
