<x-layouts::app :title="__('Detalles del Proyecto')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="p-6 bg-white relative flex-1 rounded-xl border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 space-y-6">

            {{-- Cabecera --}}
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <flux:icon.folder-open class="size-8 text-blue-500 mt-1 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">{{ $proyecto->expediente ?? '—' }}</p>
                        <flux:heading size="xl">{{ $proyecto->sumario ?? 'Proyecto sin sumario' }}</flux:heading>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-1 text-sm text-blue-600 hover:underline shrink-0">
                    <flux:icon.arrow-left class="size-4" /> Volver
                </a>
            </div>

            {{-- Estado y situación --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                    <flux:icon.check-badge class="size-5 text-neutral-400 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">Estado</p>
                        <p class="mt-0.5 font-semibold">{{ $proyecto->estado ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                    <flux:icon.signal class="size-5 text-neutral-400 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">Situación</p>
                        @php
                            $situacion = $proyecto->vigente_anulada_archivada ?? null;
                            $badgeClass = match($situacion) {
                                'VIGENTE'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'ANULADA'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'ARCHIVADA' => 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300',
                                default     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                            };
                        @endphp
                        @if($situacion)
                            <span class="mt-1 inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $badgeClass }}">
                                {{ $situacion }}
                            </span>
                        @else
                            <p class="mt-0.5 font-semibold">—</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Objeto de contratación --}}
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <flux:icon.clipboard-document-list class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Objeto de contratación</p>
                </div>
                <p class="text-sm">{{ $proyecto->objeto_contratacion ?? '—' }}</p>
            </div>

            {{-- Presupuesto --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                    <flux:icon.banknotes class="size-6 text-green-500 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">Presupuesto sin IVA</p>
                        <p class="text-xl font-bold text-green-700 dark:text-green-400">
                            {{ $proyecto->presupuesto_sin_impuestos ? number_format($proyecto->presupuesto_sin_impuestos, 2, ',', '.') . ' €' : '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-4">
                    <flux:icon.banknotes class="size-6 text-neutral-400 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">Presupuesto con IVA</p>
                        <p class="text-lg font-semibold">
                            {{ $proyecto->presupuesto_con_impuestos ? number_format($proyecto->presupuesto_con_impuestos, 2, ',', '.') . ' €' : '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-4">
                    <flux:icon.calculator class="size-6 text-neutral-400 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">Valor estimado total</p>
                        <p class="text-lg font-semibold">
                            {{ $proyecto->valor_estimado_total ? number_format($proyecto->valor_estimado_total, 2, ',', '.') . ' €' : '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tipo de contrato --}}
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.document-text class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Tipo de contrato</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <span class="text-neutral-400">Tipo:</span>
                        <p class="font-medium">{{ $proyecto->tipo_contrato ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Subtipo:</span>
                        <p class="font-medium">{{ $proyecto->subtipo_contrato ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">CPV:</span>
                        <p class="font-medium">{{ $proyecto->cpv ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Duración:</span>
                        <p class="font-medium">
                            @if($proyecto->duracion_contrato)
                                {{ $proyecto->duracion_contrato }}
                                @if($proyecto->unidad_duracion)
                                    @php
                                        $unidad = match($proyecto->unidad_duracion) {
                                            'ANN' => 'años',
                                            'MON' => 'meses',
                                            'DAY' => 'días',
                                            default => $proyecto->unidad_duracion,
                                        };
                                    @endphp
                                    {{ $unidad }}
                                @endif
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Procedimiento y tramitación --}}
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.adjustments-horizontal class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Procedimiento y tramitación</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <span class="text-neutral-400">Sistema:</span>
                        <p class="font-medium">{{ $proyecto->sistema_contratacion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Procedimiento:</span>
                        <p class="font-medium">{{ $proyecto->procedimiento ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Tramitación:</span>
                        <p class="font-medium">{{ $proyecto->tramitacion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Forma presentación:</span>
                        <p class="font-medium">{{ $proyecto->forma_presentacion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Tipo administración:</span>
                        <p class="font-medium">{{ $proyecto->tipo_administracion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Sobre umbral:</span>
                        <p class="font-medium">
                            @if($proyecto->sobre_umbral !== null)
                                {{ $proyecto->sobre_umbral ? 'Sí' : 'No' }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Directiva:</span>
                        <p class="font-medium">{{ $proyecto->directiva_aplicacion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Financiación europea:</span>
                        <p class="font-medium">
                            @if($proyecto->financiacion_europea !== null)
                                {{ $proyecto->financiacion_europea ? 'Sí' : 'No' }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
                @if($proyecto->descripcion_financiacion)
                <div class="mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-700 text-sm">
                    <span class="text-neutral-400">Descripción financiación:</span>
                    <p class="font-medium mt-0.5">{{ $proyecto->descripcion_financiacion }}</p>
                </div>
                @endif
                @if($proyecto->subcontratacion_permitido !== null)
                <div class="mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-700 text-sm flex gap-6">
                    <div>
                        <span class="text-neutral-400">Subcontratación:</span>
                        <p class="font-medium">{{ $proyecto->subcontratacion_permitido ? 'Permitida' : 'No permitida' }}</p>
                    </div>
                    @if($proyecto->subcontratacion_porcentaje)
                    <div>
                        <span class="text-neutral-400">Porcentaje:</span>
                        <p class="font-medium">{{ $proyecto->subcontratacion_porcentaje }}%</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Órgano de contratación --}}
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.building-office-2 class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Órgano de contratación</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="md:col-span-2">
                        <span class="text-neutral-400">Nombre:</span>
                        <p class="font-medium">{{ $proyecto->organo_contratacion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">NIF:</span>
                        <p class="font-medium">{{ $proyecto->nif_organo_contratacion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Lugar ejecución:</span>
                        <p class="font-medium">{{ $proyecto->lugar_ejecucion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Código NUTS:</span>
                        <p class="font-medium">{{ $proyecto->codigo_nuts ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">ID Órgano:</span>
                        <p class="font-medium">{{ $proyecto->id_organo_contratacion ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach([
                    ['label' => 'Fecha publicación',  'icon' => 'newspaper',      'value' => $proyecto->fecha_publicacion],
                    ['label' => 'Fecha presentación', 'icon' => 'paper-airplane', 'value' => $proyecto->fecha_presentacion],
                    ['label' => 'Fecha solicitud',    'icon' => 'calendar-days',  'value' => $proyecto->fecha_solicitud],
                ] as $item)
                <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                    <flux:icon :name="$item['icon']" class="size-5 text-neutral-400 shrink-0" />
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wide">{{ $item['label'] }}</p>
                        <p class="mt-0.5 font-semibold">
                            {{ $item['value'] ? \Carbon\Carbon::parse($item['value'])->format('d/m/Y H:i') : '—' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Adjudicación --}}
            @if($proyecto->fecha_adjudicacion || $proyecto->empresa_adjudicataria)
            <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/10 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.trophy class="size-4 text-amber-500" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Adjudicación</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <span class="text-neutral-400">Fecha:</span>
                        <p class="font-medium">
                            {{ $proyecto->fecha_adjudicacion ? \Carbon\Carbon::parse($proyecto->fecha_adjudicacion)->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-neutral-400">Empresa adjudicataria:</span>
                        <p class="font-medium">{{ $proyecto->empresa_adjudicataria ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">NIF adjudicatario:</span>
                        <p class="font-medium">{{ $proyecto->nif_adjudicatario ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Importe sin IVA:</span>
                        <p class="font-semibold text-amber-700 dark:text-amber-400">
                            {{ $proyecto->importe_adjudicacion_sin_iva ? number_format($proyecto->importe_adjudicacion_sin_iva, 2, ',', '.') . ' €' : '—' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Importe con IVA:</span>
                        <p class="font-semibold">
                            {{ $proyecto->importe_adjudicacion_con_iva ? number_format($proyecto->importe_adjudicacion_con_iva, 2, ',', '.') . ' €' : '—' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Nº ofertas:</span>
                        <p class="font-medium">{{ $proyecto->num_ofertas ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Nº ofertas PYME:</span>
                        <p class="font-medium">{{ $proyecto->num_ofertas_pyme ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">Adjudicado a PYME:</span>
                        <p class="font-medium">
                            @if($proyecto->adjudicado_a_pyme !== null)
                                {{ $proyecto->adjudicado_a_pyme ? 'Sí' : 'No' }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Documentación y enlaces --}}
            @if($proyecto->enlace_perfil_contratante || $proyecto->url_ppt || $proyecto->link || $proyecto->plataforma_origen)
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.link class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Documentación y enlaces</p>
                </div>
                <div class="flex flex-col gap-2 text-sm">
                    @if($proyecto->enlace_perfil_contratante)
                    <div class="flex items-center gap-2">
                        <span class="text-neutral-400 shrink-0">Perfil contratante:</span>
                        <a href="{{ $proyecto->enlace_perfil_contratante }}" target="_blank"
                            class="text-blue-600 hover:underline truncate">{{ $proyecto->enlace_perfil_contratante }}</a>
                    </div>
                    @endif
                    @if($proyecto->url_ppt)
                    <div class="flex items-center gap-2">
                        <span class="text-neutral-400 shrink-0">Pliego (PPT):</span>
                        <a href="{{ $proyecto->url_ppt }}" target="_blank"
                            class="text-blue-600 hover:underline truncate">{{ $proyecto->url_ppt }}</a>
                    </div>
                    @endif
                    @if($proyecto->link)
                    <div class="flex items-center gap-2">
                        <span class="text-neutral-400 shrink-0">Enlace ATOM:</span>
                        <a href="{{ $proyecto->link }}" target="_blank"
                            class="text-blue-600 hover:underline truncate">{{ $proyecto->link }}</a>
                    </div>
                    @endif
                    @if($proyecto->plataforma_origen)
                    <div class="flex items-center gap-2">
                        <span class="text-neutral-400 shrink-0">Plataforma origen:</span>
                        <p class="font-medium">{{ $proyecto->plataforma_origen }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Mi participación (pivot user_projects) --}}
            @if($userProject)
            <div class="rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.user-circle class="size-4 text-blue-500" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Mi participación</p>
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
            @endif

        </div>
    </div>
</x-layouts::app>
