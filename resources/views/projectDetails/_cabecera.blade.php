{{-- Cabecera: badges, título, botones de acción --}}
<div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <span class="text-xs font-bold uppercase tracking-wide px-2 py-1 rounded-full {{ $estadoClass }}">
                    {{ strtoupper($estado) }}
                </span>
                @if($proyecto->tipo_contrato)
                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full {{ $tipoColor }}">
                    <flux:icon :icon="$tipoIcon" class="size-3" />
                    {{ $proyecto->tipo_contrato }}
                </span>
                @endif
                <span class="text-sm text-neutral-500">Expediente: {{ $proyecto->expediente ?? '—' }}</span>
            </div>

            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white mb-4">{{ $proyecto->sumario ?? 'Proyecto sin sumario' }}</h1>

            <div class="flex items-center gap-2">
                @if($userProject)
                <flux:button variant="primary" size="sm" disabled>
                    Proyecto ya iniciado
                </flux:button>
                <flux:button variant="primary" size="sm" href="{{ route('project_drop', $proyecto->id) }}" onclick="return confirm('¿Estás seguro de que quieres soltar este proyecto? Se eliminará tu participación y notas asociadas.')">
                    Soltar proyecto
                </flux:button>
                @else
                <flux:button href="{{ route('project_create', $proyecto->id) }}" variant="primary" size="sm">
                    Iniciar proyecto
                </flux:button>
                @endif

                @if($proyecto->url_ppt)
                <flux:button size="sm" href="{{ $proyecto->url_ppt }}" target="_blank">
                    Pliego
                </flux:button>
                @endif
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-1 text-sm text-blue-600 hover:underline shrink-0">
            <flux:icon.arrow-left class="size-4" /> Volver
        </a>
    </div>
</div>