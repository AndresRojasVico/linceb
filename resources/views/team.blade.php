<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <x-user-card :user="auth()->user()" />

            <x-stat-card title="Tareas" value="{{ auth()->user()->tasks->count() }}"
                icon="clipboard-document-check" color="primary" nboton="Tareas" />

            <livewire:stat-card-project />
        </div>

        <div class="p-4 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">

            <p>DATOS DEL EQUIPO </p>
        </div>

    </div>
</x-layouts::app>