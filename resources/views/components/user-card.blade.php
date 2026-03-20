{{--
    Componente: user-card
    Uso: <x-user-card :user="auth()->user()" />

    Pasos:
    1. Recibe la prop 'user' con el modelo del usuario autenticado.
    2. Muestra el avatar: si tiene imagen propia usa Storage, si no, usa un avatar por defecto.
    3. Muestra el nombre del usuario en formato destacado.
    4. Si el usuario tiene rol 'Admin', muestra el nombre del rol junto al nombre.
    5. Muestra el nombre de la empresa a la que pertenece el usuario.
--}}

@props(['user'])

<div class="flex items-center gap-4 bg-white p-4 rounded-xl">

    {{-- Paso 2: avatar del usuario --}}
    <flux:avatar size="lg"
        src="{{ $user->image_path ? Storage::url($user->image_path) : 'https://unavatar.io/x/calebporzio' }}" />

    <div>
        {{-- Pasos 3 y 4: nombre y rol --}}
        <div class="flex items-end gap-2">
            <flux:heading class="font-bold" size="lg">{{ $user->name }}</flux:heading>
            @if($user->role?->name === 'Admin')
                <flux:text>{{ $user->role->name }}</flux:text>
            @endif
        </div>

        {{-- Paso 5: empresa del usuario --}}
        <flux:text class="mt-0">{{ $user->company?->name }}</flux:text>
    </div>

</div>
