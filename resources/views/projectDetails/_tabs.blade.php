{{-- Tabs: Descripción / Documentación / Historial --}}
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