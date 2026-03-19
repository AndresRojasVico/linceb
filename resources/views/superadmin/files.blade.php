<x-layouts::sadmin :title="__('Super Admin')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="flex items-center gap-4 bg-white p-4 rounded-xl">
                <div>
                    <flux:avatar size="lg"
                        src="{{ auth()->user()->image_path ? Storage::url(auth()->user()->image_path) : 'https://unavatar.io/x/calebporzio' }}" />
                </div>
                <div>
                    <div class=" flex items-end gap-2">
                        <flux:heading ing class="font-bold" size="lg">{{ auth()->user()->name }}</flux:heading>
                        @if(auth()->user()->role?->name == "Admin")
                            <flux:text>{{auth()->user()->role?->name }}</flux:text>
                        @endif
                    </div>

                    <flux:text class="mt-0">{{ auth()->user()->company?->name }}</flux:text>
                </div>
            </div>


            <x-stat-card title="Empresas" value="{{ auth()->user()->tasks->count() }}" icon="clipboard-document-check"
                color="primary" nboton="Empresas" />
            <x-stat-card title="Usuarios" value="{{ auth()->user()->projects->count() }}" icon="folder" color="primary"
                nboton="Usuarios" />

        </div>

        <div
            class=" p-4  bg-white relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:heading>Formulario para subir los ficheros</flux:heading>
            <flux:text class="mt-2">En estos enlaces se pueden descargar los ficheros</flux:text>

            <flux:link
                href="https://www.hacienda.gob.es/es-ES/GobiernoAbierto/Datos%20Abiertos/Paginas/LicitacionesContratante.aspx"
                target="_blank">Ministerio de acienda</flux:link>
            <br>

            <flux:link
                href="https://www.hacienda.gob.es/es-ES/GobiernoAbierto/Datos%20Abiertos/Paginas/licitaciones_plataforma_contratacion.aspx"
                target="_blank" rel="noopener noreferrer">Todos los enlaces contratos menores y mayores </flux:link>
            <br><br>

            <flux:field>
                <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <flux:input type="file" name="atom_file" label="Attachments" accept=".atom" />
                    <br>
                    <flux:button type="submit">Subir fichero</flux:button>
                    <flux:button href="{{ route('files.update') }}">Actualizar base de datos</flux:button>
                </form>
            </flux:field>

            @if(session('success'))
                <div
                    class="mt-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mt-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::sadmin>