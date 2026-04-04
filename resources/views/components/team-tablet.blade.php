@props(['users'])

<flux:table>
    <flux:table.columns>
        <flux:table.column>Agente</flux:table.column>
        <flux:table.column>Email</flux:table.column>
        <flux:table.column>Proyectos</flux:table.column>
        <flux:table.column>Acciones</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach ($users as $user)
        <flux:table.row>
            <flux:table.cell class="flex items-center gap-3">
                @if ($user->image_path)
                <flux:avatar :src="asset('storage/' . $user->image_path)" />
                @else
                <flux:avatar>{{ $user->initials() }}</flux:avatar>
                @endif
                <span class="font-medium">{{ $user->name }} {{ $user->surname }}</span>
            </flux:table.cell>

            <flux:table.cell>{{ $user->email }}</flux:table.cell>

            <flux:table.cell>{{ $user->projects_count }}</flux:table.cell>

            <flux:table.cell>
                <p>Ver mas acciones</p>
            </flux:table.cell>
        </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>