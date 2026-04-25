{{-- Fila 2: Detalles del contrato + Órgano contratante --}}
<div class="grid grid-cols-1 md:grid-cols-2 border-b border-neutral-200 dark:border-neutral-700">

    <div class="p-6 border-r border-neutral-200 dark:border-neutral-700">
        <div class="flex items-center gap-2 mb-4">
            <flux:icon.document-text class="size-4 text-neutral-400" />
            <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest">Detalles del contrato</p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Tipo</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->tipo_contrato ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Procedimiento</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->procedimiento ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Tramitación</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->tramitacion ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Duración</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $duracionTexto ?? '—' }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="flex items-center gap-2 mb-4">
            <flux:icon.building-office-2 class="size-4 text-neutral-400" />
            <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest">Órgano contratante</p>
        </div>
        <div class="flex items-start gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center shrink-0">
                <flux:icon.building-office class="size-5 text-neutral-500" />
            </div>
            <div>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->organo_contratacion ?? '—' }}</p>
                @if($proyecto->nif_organo_contratacion)
                <p class="text-xs text-neutral-500">NIF: {{ $proyecto->nif_organo_contratacion }}</p>
                @endif
            </div>
        </div>
        @if($proyecto->enlace_perfil_contratante)
        <a href="{{ $proyecto->enlace_perfil_contratante }}" target="_blank"
            class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline font-semibold uppercase tracking-wide">
            Ver perfil del contratante
            <flux:icon.arrow-top-right-on-square class="size-3" />
        </a>
        @endif
    </div>
</div>