{{-- Fila 3: Ubicación y clasificación + Adjudicación --}}
<div class="grid grid-cols-1 md:grid-cols-2 border-b border-neutral-200 dark:border-neutral-700">

    <div class="p-6 border-r border-neutral-200 dark:border-neutral-700">
        <div class="flex items-center gap-2 mb-4">
            <flux:icon.map-pin class="size-4 text-neutral-400" />
            <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest">Ubicación y clasificación</p>
        </div>
        <div class="space-y-3 text-sm">
            @if($proyecto->cpv)
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">CPV</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->cpv }}</p>
            </div>
            @endif
            @if($proyecto->lugar_ejecucion || $proyecto->codigo_nuts)
            <div class="flex items-start gap-2">
                <flux:icon.map-pin class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                <div>
                    @if($proyecto->codigo_nuts)
                    <p class="text-xs text-neutral-400 mb-0.5">{{ $proyecto->codigo_nuts }}</p>
                    @endif
                    @if($proyecto->lugar_ejecucion)
                    <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->lugar_ejecucion }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="p-6">
        <div class="flex items-center gap-2 mb-4">
            <flux:icon.trophy class="size-4 text-neutral-400" />
            <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest">Adjudicación</p>
        </div>
        @if($proyecto->empresa_adjudicataria || $proyecto->fecha_adjudicacion)
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Fecha</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">
                    {{ $proyecto->fecha_adjudicacion ? \Carbon\Carbon::parse($proyecto->fecha_adjudicacion)->format('d/m/Y') : '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Empresa</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $proyecto->empresa_adjudicataria ?? '—' }}</p>
            </div>
            @if($proyecto->importe_adjudicacion_sin_iva)
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Importe sin IVA</p>
                <p class="font-semibold text-amber-700 dark:text-amber-400">{{ number_format($proyecto->importe_adjudicacion_sin_iva, 2, ',', '.') }} €</p>
            </div>
            @endif
            @if($proyecto->importe_adjudicacion_con_iva)
            <div>
                <p class="text-xs text-neutral-400 mb-0.5">Importe con IVA</p>
                <p class="font-semibold text-neutral-800 dark:text-neutral-200">{{ number_format($proyecto->importe_adjudicacion_con_iva, 2, ',', '.') }} €</p>
            </div>
            @endif
        </div>
        @else
        <p class="text-xs text-neutral-400 italic leading-relaxed">Se actualizará tras la resolución del órgano de contratación y la publicación de la adjudicación.</p>
        @endif
    </div>
</div>