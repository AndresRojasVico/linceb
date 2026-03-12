<x-layouts::app :title="__('Proyectos usuario')">
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
                    <flux:table.column>Customer</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell>Lindsey Aminoff</flux:table.cell>
                        <flux:table.cell>Jul 29, 10:45 AM</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="green" size="sm" inset="top bottom">Paid</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell variant="strong">$49.00</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Hanna Lubin</flux:table.cell>
                        <flux:table.cell>Jul 28, 2:15 PM</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="green" size="sm" inset="top bottom">Paid</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell variant="strong">$312.00</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Kianna Bushevi</flux:table.cell>
                        <flux:table.cell>Jul 30, 4:05 PM</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="zinc" size="sm" inset="top bottom">Refunded</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell variant="strong">$132.00</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Gustavo Geidt</flux:table.cell>
                        <flux:table.cell>Jul 27, 9:30 AM</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="green" size="sm" inset="top bottom">Paid</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell variant="strong">$31.00</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>

        </div>
    </div>
</x-layouts::app>