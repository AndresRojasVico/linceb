<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="p-3">
                    <p class="text-sm">{{ auth()->user()->name }} -- {{auth()->user()->role?->name }}</p>
                    <flux:text variant="muted">{{ auth()->user()->company?->name }}</flux:text>

                    <p>Sectores:</p>
                    <ul class="list-disc ml-5 text-sm">
                        @if(auth()->user()->company?->sectors)
                            @foreach (auth()->user()->company->sectors as $sector)
                                <li>{{ $sector->name }}</li>
                            @endforeach
                        @else
                            <li class="text-zinc-500">Sin sectores asignados</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="p-3">
                    @php
                        // Filtramos las tareas para que solo se muestren las que tienen estado 'Pendiente'
                        $pendingTasks = auth()->user()->tasks->filter(function ($task) {
                            return $task->state?->name === 'Pendiente';
                        });
                    @endphp

                    <p class="text-sm font-medium">Tareas pendientes: {{ $pendingTasks->count() }}</p>

                    <ul class="list-disc ml-5 mt-2 text-sm">
                        @forelse ($pendingTasks as $task)
                            <li>{{ $task->name }}</li>
                        @empty
                            <li class="text-zinc-500 italic">No hay tareas pendientes</li>
                        @endforelse
                    </ul>
                </div>

            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <flux:card>
                    <flux:heading size="lg">Are you sure?</flux:heading>
                    <flux:text class="mt-2 mb-4">
                        Your post will be deleted permanently.<br>
                        This action cannot be undone.
                    </flux:text>
                    <flux:button variant="danger">Delete</flux:button>
                </flux:card>
                <p class="text-sm font-medium p-3">proyectos: {{ auth()->user()->projects->count() }}</p>
            </div>
        </div>
        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />

        </div>
    </div>
</x-layouts::app>