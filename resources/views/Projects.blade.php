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
            <x-stat-card title="Proyectos" value="{{ auth()->user()->projects->count() }}" icon="folder" color="primary" nboton="Proyectos" />
        </div>

        {{-- Grid de tarjetas --}}
        <div class="p-4 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:heading class="mb-4">Proyectos en plazo</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($projects as $project)
                <div class="relative flex flex-col justify-between rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-5 shadow-sm hover:shadow-md transition">

                    {{-- Cabecera: badge + bookmark --}}
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wide px-2 py-1 rounded-full
                            {{ match(true) {
                                str_contains(strtolower($project->estado ?? ''), 'adjudic') => 'bg-blue-100 text-blue-700',
                                str_contains(strtolower($project->estado ?? ''), 'urgente') => 'bg-amber-100 text-amber-700',
                                default => 'bg-green-100 text-green-700'
                            } }}">
                            {{ strtoupper($project->estado ?? 'ABIERTA') }}
                        </span>
                        <flux:icon.bookmark class="size-5 text-neutral-300 hover:text-neutral-500 cursor-pointer" />
                    </div>

                    {{-- Título --}}
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 text-sm leading-snug mb-2">
                        {{ $project->objeto_contratacion ?? $project->sumario ?? '—' }}
                    </h3>

                    {{-- Órgano --}}
                    <div class="flex items-center gap-1.5 text-xs text-neutral-500 mb-4">
                        <flux:icon.building-office-2 class="size-4 shrink-0" />
                        {{ $project->organo_contratacion ?? '—' }}
                    </div>

                    {{-- Importe --}}
                    <div class="mb-4">
                        <p class="text-xs text-neutral-400 uppercase tracking-wide">Importe</p>
                        <p class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $project->presupuesto_sin_impuestos ? number_format($project->presupuesto_sin_impuestos, 0, ',', '.') . ' €' : '—' }}
                        </p>
                    </div>

                    {{-- Fecha --}}
                    <div class="flex items-center gap-1 text-xs mb-5">
                        <flux:icon.calendar-days class="size-4 text-neutral-400 shrink-0" />
                        <x-fecha-presentacion :fecha="$project->fecha_presentacion" />
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center gap-2">
                        <flux:button variant="primary" size="sm" class="flex-1">Iniciar proyecto</flux:button>
                        <flux:button size="sm" href="{{ route('project_details', ['id' => $project->id]) }}">Ver detalles</flux:button>
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        </div>

    </div>
</x-layouts::app>
