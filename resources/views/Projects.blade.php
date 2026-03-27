<x-layouts::app :title="__('Proyectos')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        {{-- Stats --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="flex items-center gap-4 bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700">
                <flux:avatar size="lg"
                    src="{{ auth()->user()->image_path ? Storage::url(auth()->user()->image_path) : 'https://unavatar.io/x/calebporzio' }}" />
                <div>
                    <div class="flex items-end gap-2">
                        <flux:heading class="font-bold" size="lg">{{ auth()->user()->name }}</flux:heading>
                        @if(auth()->user()->role?->name == "Admin")
                        <flux:text>{{ auth()->user()->role?->name }}</flux:text>
                        @endif
                    </div>
                    <flux:text class="mt-0">{{ auth()->user()->company?->name }}</flux:text>
                </div>
            </div>
            <x-stat-card title="Tareas" value="{{ auth()->user()->tasks->count() }}" icon="clipboard-document-check" color="primary" nboton="Tareas" />
            <livewire:stat-card-project />
        </div>

        {{-- Grid de tarjetas --}}
        <div class="p-4 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:heading class="mb-4">Proyectos en plazo</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($projects as $project)
                <x-project-card :project="$project" :ya-iniciado="in_array($project->id, $iniciados)" />
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        </div>

    </div>
</x-layouts::app>