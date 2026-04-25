<x-layouts::app :title="__('Detalles del Proyecto')">

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        @if(session('status_updated'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm dark:bg-green-900/20 dark:border-green-800 dark:text-green-400">
            <flux:icon.check-circle class="size-4 shrink-0" />
            {{ session('status_updated') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden dark:bg-neutral-900 dark:border-neutral-700">
            @include('projectDetails._cabecera')
            @include('projectDetails._fila-economica')
            @include('projectDetails._fila-contrato')
            @include('projectDetails._fila-ubicacion')
            @include('projectDetails._tabs')
        </div>

        @include('projectDetails._participacion')

    </div>

</x-layouts::app>