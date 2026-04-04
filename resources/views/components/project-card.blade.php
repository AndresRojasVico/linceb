@props(['project',
'yaIniciado' => false])

<div class="relative flex flex-col justify-between rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-5 shadow-sm hover:shadow-md transition">

    {{-- Cabecera: badge + bookmark --}}
    <div class="flex items-start justify-between mb-4">
        <span class="text-xs font-bold uppercase tracking-wide px-2 py-1 rounded-full
            {{ match(true) {
                str_contains(strtolower($project->estado ?? ''), 'adjudic') => 'bg-blue-100 text-blue-700',
                str_contains(strtolower($project->estado ?? ''), 'urgente') => 'bg-amber-100 text-amber-700',
                default => 'bg-green-100 text-green-700'
            } }}">
            {{ strtoupper($project->estado ?? 'ABIERTA') }}
        </span>
        @php
        $tipo = strtolower($project->tipo_contrato ?? '');
        [$tipoColor, $tipoIcon] = match(true) {
        str_contains($tipo, 'servicio') => ['bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300', 'briefcase'],
        str_contains($tipo, 'suministro') => ['bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300', 'cube'],
        str_contains($tipo, 'obra') => ['bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300', 'wrench-screwdriver'],
        str_contains($tipo, 'concesion') || str_contains($tipo, 'concesión') => ['bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300', 'building-library'],
        default => ['bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-300', 'tag'],
        };
        @endphp
        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full {{ $tipoColor }}">
            <flux:icon :icon="$tipoIcon" class="size-3" />
            {{ $project->tipo_contrato ?? '—' }}
        </span>
        @if($yaIniciado)
        <flux:icon.bookmark class="size-5 text-green-500 hover:text-neutral-500 cursor-pointer" />
        @else
        <flux:icon.bookmark class="size-5 text-neutral-300 hover:text-neutral-500 cursor-pointer" />
        @endif

        {{-- :project-id="$project->id" → le pasamos el id del proyecto como prop --}}
        {{-- :key="'fav-'.$project->id" → necesario cuando el componente está dentro de un bucle,
             garantiza que cada instancia de Livewire sea única y no se mezclen estados --}}
        <livewire:toggle-favorite :project-id="$project->id" :key="'fav-'.$project->id" />

    </div>

    {{-- Título --}}
    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 text-sm leading-snug mb-2 line-clamp-4">
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
    {{-- $yaIniciado viene del controlador: true si el usuario ya tiene este proyecto en user_projects --}}
    <div class="flex items-center gap-2">
        @if($yaIniciado)

        <flux:button variant="primary" size="sm" class="flex-1" disabled>

            Proyecto ya iniciado
        </flux:button>
        @else
        {{-- Proyecto no iniciado: botón activo que crea el registro en user_projects --}}
        <flux:button href="{{ route('project_create', $project->id) }}" variant="primary" size="sm" class="flex-1">Iniciar proyecto</flux:button>
        @endif
        <flux:button size="sm" href="{{ route('project_details', ['id' => $project->id]) }}">Ver detalles</flux:button>
    </div>

</div>