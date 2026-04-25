{{-- Fila 1: Información económica + Fechas clave --}}
<div class="grid grid-cols-1 md:grid-cols-2 border-b border-neutral-200 dark:border-neutral-700">

    <div class="p-6 border-r border-neutral-200 dark:border-neutral-700">
        <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest mb-4">Información económica</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-neutral-400 uppercase leading-tight mb-1">Presupuesto base sin IVA</p>
                <p class="text-lg font-bold text-neutral-900 dark:text-white">
                    {{ $proyecto->presupuesto_sin_impuestos ? number_format($proyecto->presupuesto_sin_impuestos, 2, ',', '.') : '—' }}
                </p>
                @if($proyecto->valor_estimado_total)
                <p class="text-sm text-neutral-500">€</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-neutral-400 uppercase leading-tight mb-1">Presupuesto (IVA)</p>
                <p class="text-lg font-bold text-neutral-900 dark:text-white">
                    {{ $proyecto->presupuesto_con_impuestos ? number_format($proyecto->presupuesto_con_impuestos, 2, ',', '.') : '—' }}
                </p>
                @if($proyecto->presupuesto_con_impuestos)
                <p class="text-sm text-neutral-500">€</p>
                @endif
            </div>
        </div>
    </div>

    <div class="p-6">
        <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest mb-4">Fechas clave</p>
        <div class="space-y-3">
            @if($proyecto->fecha_publicacion)
            <div class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-neutral-300 mt-1.5 shrink-0"></div>
                <div>
                    <p class="text-xs text-neutral-400 uppercase tracking-wide">Publicación</p>
                    <p class="font-semibold text-sm">{{ \Carbon\Carbon::parse($proyecto->fecha_publicacion)->format('d/m/Y') }}</p>
                </div>
            </div>
            @endif
            @if($proyecto->fecha_presentacion)
            <div class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></div>
                <div>
                    <p class="text-xs text-neutral-400 uppercase tracking-wide">Plazo presentación</p>
                    <div class="flex items-center gap-1 text-sm mt-0.5">
                        <flux:icon.calendar-days class="size-4 text-neutral-400 shrink-0" />
                        <x-fecha-presentacion :fecha="$proyecto->fecha_presentacion" />
                    </div>
                </div>
            </div>
            @endif
            @if($proyecto->updated_at)
            <div class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-neutral-300 mt-1.5 shrink-0"></div>
                <div>
                    <p class="text-xs text-neutral-400 uppercase tracking-wide">Última actualización</p>
                    <p class="font-semibold text-sm">{{ \Carbon\Carbon::parse($proyecto->updated_at)->format('d/m/Y') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>