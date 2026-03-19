<x-layouts::app :title="__('Dashboard')">
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


            <x-stat-card title="Tareas" value="{{ auth()->user()->tasks->count() }}" icon="clipboard-document-check"
                color="primary" nboton="Tareas" />
            <x-stat-card title="Proyectos" value="{{ auth()->user()->projects->count() }}" icon="folder" color="primary"
                nboton="Proyectos" />

        </div>
        <div
            class=" p-4  bg-white relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">

            <flux:heading>Proyectos activos</flux:heading>


            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Expediente</flux:table.column>
                    <flux:table.column>Sumarios</flux:table.column>
                    <flux:table.column>Estado</flux:table.column>
                    <flux:table.column>Notas</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach (auth()->user()->projects as $myProject)
                        <flux:table.row>
                            <flux:table.cell>{{$myProject->expediente}}</flux:table.cell>
                            <flux:table.cell>{{$myProject->sumario}}</flux:table.cell>
                            <flux:table.cell>{{$myProject->pivot->status->name}}</flux:table.cell>
                            <flux:table.cell>{{$myProject->pivot->notes}}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

        </div>
    </div>
</x-layouts::app>