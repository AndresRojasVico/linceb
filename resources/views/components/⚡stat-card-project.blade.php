<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    // Total de proyectos asociados al usuario
    public int $projectCount = 0;

    // Total de proyectos marcados como favoritos por el usuario
    public int $favoritesCount = 0;

    // Se ejecuta una sola vez al montar el componente: carga los contadores iniciales
    public function mount(): void
    {
        $this->projectCount   = Auth::user()->projects()->count();
        $this->favoritesCount = Auth::user()->favoriteProjects()->count();
    }

    // Escucha el evento 'favoritesUpdated' que lanza el componente toggle-favorite
    // cuando el usuario marca o desmarca un proyecto como favorito
    #[On('favoritesUpdated')]
    public function refreshFavorites(): void
    {
        // Reconsulta la BD para reflejar el nuevo total de favoritos
        $this->favoritesCount = Auth::user()->favoriteProjects()->count();
    }
};
?>

<flux:card class="p-4">
    <flux:text>Proyectos</flux:text>
    <div class="flex gap-3 ">
        {{-- Fila horizontal: icono carpeta + total proyectos | icono corazón + total favoritos --}}
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <flux:icon name="folder" size="lg" class="opacity-80" />
                <flux:text class="font-extrabold text-2xl text-green-500">
                    {{ $projectCount }}
            </div>
            <flux:button size="xs" variant="primary">Proyectos</flux:button>
        </div>

        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <flux:icon name="heart" variant="solid" size="lg" class="text-red-500 opacity-80" />
                <flux:text class="font-extrabold text-2xl text-red-500">
                    {{ $favoritesCount }}
                </flux:text>
            </div>
            <flux:button size="xs" variant="primary">Favoritos</flux:button>
        </div>

    </div>

</flux:card>