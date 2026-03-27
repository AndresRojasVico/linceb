{{-- Layout principal de la aplicación con el título "Dashboard" --}}
<x-layouts::app :title="__('Dashboard')">

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

        {{-- Tabla de proyectos activos, ordenados por fecha de presentación ascendente --}}
        <div class="p-4 bg-white relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-active-projects-table :projects="auth()->user()->projects->sortBy('fecha_presentacion')" />
        </div>

    </div>
</x-layouts::app>