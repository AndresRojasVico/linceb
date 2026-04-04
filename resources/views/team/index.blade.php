{{-- Layout principal de la aplicación con el título "Equipo" --}}
<x-layouts::app :title="__('Equipo')">

    {{-- Contenedor principal del dashboard: disposición en columna con altura completa --}}
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Grid de tarjetas de resumen: 3 columnas en pantallas medianas --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            {{-- Tarjeta con información del usuario autenticado --}}
            <x-user-card :user="auth()->user()" />
            {{-- Tarjeta estadística: total de tareas asignadas al usuario --}}
            <x-stat-card title="Tareas" value="{{ auth()->user()->tasks->count() }}"
                icon="clipboard-document-check" color="primary" nboton="Tareas" />
            {{-- Tarjeta estadística: proyectos y favoritos del usuario (componente Livewire reactivo) --}}
            <livewire:stat-card-project />
        </div>


        {{-- Grid de contenido principal: --}}
        <div class="p-4 bg-white h-full dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex flex-col">
                    {{-- Título de la sección --}}
                    <h2 class="text-2xl font-bold text-black-400 dark:text-neutral-800">Equipo</h2>
                    <p class="">Administrar los agentes de tu organizacion y su carga de trabajo actual.</p>
                </div>
                <div>
                    <flux:button icon="user-plus" variant="primary" :href="route('team-add')">Añadir miembro</flux:button>
                </div>
            </div>
            {{-- Componente de tabla personalizada para mostrar los miembros del equipo --}}
            <x-team-tablet :users="$users" />
        </div>
    </div>
</x-layouts::app>