@props(['projects'])

<div class="p-4 bg-white relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
    <flux:heading>Proyectos activos</flux:heading>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Expediente</flux:table.column>
            <flux:table.column>Organos de Contratación</flux:table.column>
            <flux:table.column>Importe sin iva</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column>Fecha presentacion</flux:table.column>
            <flux:table.column>Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($projects as $project)
            <flux:table.row>
                <flux:table.cell>{{ $project->expediente }}</flux:table.cell>
                <flux:table.cell>{{ Str::words($project->organo_contratacion, 5, '...') }}</flux:table.cell>
                <flux:table.cell>{{ number_format($project->presupuesto_sin_impuestos, 2, ',', '.') }}</flux:table.cell>
                <flux:table.cell>
                    <x-status-badge :status="$project->pivot->status->name" />
                </flux:table.cell>
                <flux:table.cell>
                    <x-fecha-presentacion :fecha="$project->fecha_presentacion" />
                </flux:table.cell>
                <flux:table.cell>
                    <flux:button size="sm"><a href="{{ route('project_details', ['id' => $project->id]) }}">Ver detalles</a></flux:button>
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
