{{-- Layout principal de la aplicación con el título "Añadir miembro" --}}
<x-layouts::app :title="__('Añadir miembro')">

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="p-6 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">

            {{-- Cabecera --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">Añadir miembro</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Rellena los datos para dar de alta un nuevo miembro del equipo.</p>
                </div>
                <flux:button href="{{ route('team-add') }}" icon="arrow-left" variant="ghost">Volver</flux:button>
            </div>

            {{-- Mensajes de éxito --}}
            @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                {{ session('success') }}
            </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('team-store') }}" class="flex flex-col gap-5 max-w-lg">
                @csrf

                <flux:input
                    name="name"
                    :label="__('Nombre')"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    placeholder="Nombre" />

                <flux:input
                    name="surname"
                    :label="__('Apellidos')"
                    :value="old('surname')"
                    type="text"
                    placeholder="Apellidos" />

                <flux:input
                    name="email"
                    :label="__('Correo electrónico')"
                    :value="old('email')"
                    type="email"
                    required
                    placeholder="email@ejemplo.com" />

                <flux:input
                    name="phone"
                    :label="__('Teléfono')"
                    :value="old('phone')"
                    type="text"
                    placeholder="+34 600 000 000" />

                <flux:input
                    name="password"
                    :label="__('Contraseña')"
                    type="password"
                    required
                    placeholder="Mínimo 8 caracteres"
                    viewable />

                <flux:input
                    name="password_confirmation"
                    :label="__('Confirmar contraseña')"
                    type="password"
                    required
                    placeholder="Repite la contraseña"
                    viewable />

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button href="{{ route('team-store') }}" variant="ghost">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" icon="user-plus">Crear miembro</flux:button>
                </div>
            </form>

        </div>
    </div>

</x-layouts::app>