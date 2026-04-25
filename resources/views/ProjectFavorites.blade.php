<x-layouts::app :title="__('Favoritos')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        {{-- Stats --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <x-user-card :user="auth()->user()" />
            <x-stat-card title="Tareas" value="{{ auth()->user()->tasks->count() }}"
                icon="clipboard-document-check" color="primary" nboton="Tareas" />
            <livewire:stat-card-project />
        </div>

        {{-- Buscador + grid de tarjetas --}}
        <div class="p-4 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:heading class="mb-4">Proyectos favoritos</flux:heading>
            <livewire:favorites-search />
        </div>

    </div>
</x-layouts::app>
