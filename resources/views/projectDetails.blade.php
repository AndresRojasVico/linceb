<x-layouts::app :title="__('Detalles del Proyecto')">
    @php
    $diasRestantes = $proyecto->fecha_presentacion
    ? (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($proyecto->fecha_presentacion), false)
    : null;
    $duracionTexto = null;
    if ($proyecto->duracion_contrato) {
    $unidad = match($proyecto->unidad_duracion ?? '') {
    'ANN' => 'años', 'MON' => 'meses', 'DAY' => 'días',
    default => $proyecto->unidad_duracion ?? '',
    };
    $duracionTexto = $proyecto->duracion_contrato . ' ' . $unidad;
    }
    $docsCount = collect([
    $proyecto->enlace_perfil_contratante,
    $proyecto->url_ppt,
    $proyecto->link,
    $proyecto->plataforma_origen,
    ])->filter()->count();

    // Badge estado (igual lógica que project-card)
    $estado = $proyecto->estado ?? 'ABIERTA';
    $estadoClass = match(true) {
    str_contains(strtolower($estado), 'adjudic') => 'bg-blue-100 text-blue-700',
    str_contains(strtolower($estado), 'urgente') => 'bg-amber-100 text-amber-700',
    default => 'bg-green-100 text-green-700',
    };

    // Badge tipo contrato (igual lógica que project-card)
    $tipo = strtolower($proyecto->tipo_contrato ?? '');
    [$tipoColor, $tipoIcon] = match(true) {
    str_contains($tipo, 'servicio') => ['bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300', 'briefcase'],
    str_contains($tipo, 'suministro') => ['bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300', 'cube'],
    str_contains($tipo, 'obra') => ['bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300', 'wrench-screwdriver'],
    str_contains($tipo, 'concesion') || str_contains($tipo, 'concesión') => ['bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300', 'building-library'],
    default => ['bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-300', 'tag'],
    };
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden dark:bg-neutral-900 dark:border-neutral-700">

            {{-- Cabecera --}}
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

            {{-- Fila 1: Información económica + Fechas clave --}}
            <div class="grid grid-cols-1 md:grid-cols-2 border-b border-neutral-200 dark:border-neutral-700">

                <div class="p-6 border-r border-neutral-200 dark:border-neutral-700">
                    <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest mb-4">Información económica</p>
                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <p class="text-xs text-neutral-400 uppercase leading-tight mb-1">Presupuesto base sin IVA</p>
                            <p class="text-lg font-bold text-neutral-900 dark:text-white">
                                {{ $proyecto->valor_estimado_total ? number_format($proyecto->valor_estimado_total, 2, ',', '.') : '—' }}
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

            {{-- Tabs --}}
            <div x-data="{ tab: 'descripcion' }">
                <div class="flex border-b border-neutral-200 dark:border-neutral-700 px-6">
                    <button @click="tab = 'descripcion'"
                        :class="tab === 'descripcion' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                        class="px-4 py-3 text-xs uppercase tracking-widest transition -mb-px">
                        Descripción completa
                    </button>
                    <button @click="tab = 'documentacion'"
                        :class="tab === 'documentacion' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                        class="px-4 py-3 text-xs uppercase tracking-widest transition -mb-px">
                        Documentación relacionada ({{ $docsCount }})
                    </button>
                    <button @click="tab = 'historial'"
                        :class="tab === 'historial' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
                        class="px-4 py-3 text-xs uppercase tracking-widest transition -mb-px">
                        Histórico de cambios
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="tab === 'descripcion'">
                        <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line">{{ $proyecto->objeto_contratacion ?? 'No hay descripción disponible.' }}</p>
                    </div>

                    <div x-show="tab === 'documentacion'" class="space-y-3">
                        @if($proyecto->url_ppt)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                            <flux:icon.document-text class="size-5 text-blue-500 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Pliego (PPT)</p>
                                <a href="{{ $proyecto->url_ppt }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate block">{{ $proyecto->url_ppt }}</a>
                            </div>
                        </div>
                        @endif
                        @if($proyecto->enlace_perfil_contratante)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                            <flux:icon.link class="size-5 text-blue-500 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Perfil del contratante</p>
                                <a href="{{ $proyecto->enlace_perfil_contratante }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate block">{{ $proyecto->enlace_perfil_contratante }}</a>
                            </div>
                        </div>
                        @endif
                        @if($proyecto->link)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                            <flux:icon.globe-alt class="size-5 text-blue-500 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Enlace ATOM</p>
                                <a href="{{ $proyecto->link }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate block">{{ $proyecto->link }}</a>
                            </div>
                        </div>
                        @endif
                        @if($proyecto->plataforma_origen)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                            <flux:icon.server class="size-5 text-neutral-500 shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Plataforma origen</p>
                                <p class="text-xs text-neutral-500">{{ $proyecto->plataforma_origen }}</p>
                            </div>
                        </div>
                        @endif
                        @if($docsCount === 0)
                        <p class="text-sm text-neutral-400 italic">No hay documentación relacionada disponible.</p>
                        @endif
                    </div>

                    <div x-show="tab === 'historial'">
                        <p class="text-sm text-neutral-400 italic">Histórico de cambios no disponible.</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Mi participación --}}
        @if($userProject)
        <div class="bg-white rounded-xl border border-blue-200 dark:bg-neutral-900 dark:border-blue-900 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon.user-circle class="size-4 text-blue-500" />
                    <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest">Mi participación</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-start gap-2">
                        <flux:icon.tag class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                        <div class="w-full">
                            <span class="text-neutral-400">Estado:</span>
                            <form id="form-participacion" action="{{ route('project_details.status', $proyecto->id) }}" method="POST" class="mt-1">
                                @csrf
                                @method('PATCH')
                                <select name="project_status_id"
                                    class="rounded-md border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" @selected($userProject->pivot->project_status_id == $status->id)>
                                        {{ $status->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                            @if(session('status_updated'))
                            <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                <flux:icon.check-circle class="size-3" /> {{ session('status_updated') }}
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <flux:icon.clock class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                        <div>
                            <span class="text-neutral-400">Última actualización:</span>
                            <p class="font-medium mt-0.5">
                                {{ $userProject->pivot->updated_at ? \Carbon\Carbon::parse($userProject->pivot->updated_at)->format('d/m/Y H:i') : '—' }}
                            </p>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex items-start gap-2">
                        <flux:icon.chat-bubble-left-ellipsis class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                        <div class="w-full">
                            <span class="text-neutral-400">Notas:</span>
                            <textarea name="notes" form="form-participacion" rows="4"
                                class="mt-1 w-full rounded-md border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"
                                placeholder="Escribe tus notas aquí...">{{ $userProject->pivot->notes }}</textarea>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" form="form-participacion"
                            class="flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                            <flux:icon.check class="size-4" /> Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-layouts::app>