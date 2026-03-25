<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <x-user-card :user="auth()->user()" />

            <x-stat-card title="Tareas" value="{{ auth()->user()->tasks->count() }}"
                icon="clipboard-document-check" color="primary" nboton="Tareas" />

            <x-stat-card title="Proyectos" value="{{ auth()->user()->projects->count() }}"
                icon="folder" color="primary" nboton="Proyectos" />
        </div>

        <x-active-projects-table :projects="auth()->user()->projects->sortBy('fecha_presentacion')" />

    </div>
</x-layouts::app>