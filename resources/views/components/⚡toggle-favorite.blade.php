<?php

// Importamos el modelo que representa la tabla user_project_favorites
use App\Models\UserProjectFavorite;

// Auth nos permite obtener el id del usuario que está logueado
use Illuminate\Support\Facades\Auth;

use Livewire\Component;

new class extends Component
{
    // Recibe el id del proyecto desde la card (se lo pasamos con :project-id="$project->id")
    public int $projectId;

    // Controla si el corazón está rojo (true) o gris (false)
    public bool $isFavorite = false;

    // mount() se ejecuta UNA SOLA VEZ al cargar el componente
    // Consulta la BD para saber si este proyecto ya es favorito del usuario
    public function mount(): void
    {
        $this->isFavorite = UserProjectFavorite::where('user_id', Auth::id())
            ->where('project_id', $this->projectId)
            ->exists(); // devuelve true o false
    }

    // toggle() se ejecuta cada vez que el usuario hace click en el corazón
    public function toggle(): void
    {
        if ($this->isFavorite) {
            // Si ya era favorito → lo eliminamos de la BD
            UserProjectFavorite::where('user_id', Auth::id())
                ->where('project_id', $this->projectId)
                ->delete();
            $this->isFavorite = false;
        } else {
            // Si no era favorito → lo insertamos en la BD
            UserProjectFavorite::create([
                'user_id'    => Auth::id(),
                'project_id' => $this->projectId,
            ]);
            $this->isFavorite = true;
        }

        // Notifica al componente stat-card-project para que actualice su contador de favoritos
        $this->dispatch('favoritesUpdated');
    }
};
?>

<div>
    {{-- wire:click="toggle" le dice a Livewire que al hacer click ejecute el método toggle() --}}
    <button wire:click="toggle" class="cursor-pointer">
        @if($isFavorite)
        {{-- Corazón rojo: el proyecto ES favorito --}}
        <flux:icon.heart variant="solid" class="size-5  text-red-500" />
        @else
        {{-- Corazón gris: el proyecto NO es favorito --}}
        <flux:icon.heart class="size-5 text-neutral-300 hover:text-red-400" />
        @endif
    </button>
</div>