<x-layouts::app :title="__('Detalles del Proyecto')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="p-6 bg-white relative flex-1 rounded-xl border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900">

            {{-- Cabecera --}}
            <div class="flex items-start justify-between mb-6">
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
            {{-- Enlace al perfil del contratante --}}
            @if($proyecto->enlace_perfil_contratante)
            <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-4">
                <flux:icon.link class="size-5 text-blue-500 shrink-0" />
                <div class="min-w-0">
                    <p class="text-xs text-neutral-500 uppercase tracking-wide mb-1">Perfil del contratante</p>
                    <a href="{{ $proyecto->enlace_perfil_contratante }}" target="_blank" class="text-blue-600 hover:underline break-all text-sm">
                        {{ $proyecto->enlace_perfil_contratante }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Estado y situación --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                        <p class="mt-0.5 font-semibold">{{ $proyecto->vigente_anulada_archivada ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Objeto de contratación --}}
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <flux:icon.clipboard-document-list class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Objeto de contratación</p>
                </div>
                <p class="text-sm">{{ $proyecto->objeto_contratacion ?? '—' }}</p>
            </div>

            {{-- Presupuesto --}}
            <div class="flex items-center gap-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-6">
                <flux:icon.banknotes class="size-6 text-green-500 shrink-0" />
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Presupuesto sin impuestos</p>
                    <p class="text-xl font-bold text-green-700 dark:text-green-400">
                        {{ $proyecto->presupuesto_sin_impuestos ? number_format($proyecto->presupuesto_sin_impuestos, 2, ',', '.') . ' €' : '—' }}
                    </p>
                </div>
            </div>

            {{-- Órgano de contratación --}}
            <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.building-office-2 class="size-4 text-neutral-400" />
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Órgano de contratación</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <span class="text-neutral-400">Nombre:</span>
                        <p class="font-medium">{{ $proyecto->organo_contratacion ?? '—' }}</p>
                    </div>

                    <div>
                        <span class="text-neutral-400">Ciudad:</span>
                        <p class="font-medium">{{ $proyecto->lugar_ejecucion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400">NIF:</span>
                        <p class="font-medium">{{ $proyecto->nif_organo_contratacion ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach([
                ['label' => 'Fecha publicación', 'icon' => 'newspaper', 'value' => $proyecto->fecha_publicacion],
                ['label' => 'Fecha presentación', 'icon' => 'paper-airplane', 'value' => $proyecto->fecha_presentacion],
                ['label' => 'Fecha solicitud', 'icon' => 'calendar-days', 'value' => $proyecto->fecha_solicitud],
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



            {{-- Mi participación (pivot user_projects) --}}
            @if($userProject)
            <div class="rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10 p-4 mb-6">
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