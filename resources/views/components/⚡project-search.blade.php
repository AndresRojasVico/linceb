<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $tab = 'disponibles';
    
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function projects()
    {
        if ($this->tab === 'mis_proyectos') {
            $query = Auth::user()->projects();
        } else {
            $query = Project::where('fecha_presentacion', '>=', now());
        }

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('objeto_contratacion', 'like', $term)
                  ->orWhere('organo_contratacion', 'like', $term)
                  ->orWhere('expediente', 'like', $term);
            });
        }

        return $query->orderBy('fecha_publicacion', 'desc')->paginate(6);
    }

    #[Computed]
    public function iniciados(): array
    {
        return Auth::check()
            ? Auth::user()->projects()->pluck('projects.id')->toArray()
            : [];
    }
};
?>

<div>
    {{-- Barra de búsqueda + pestañas --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por título, organismo o expediente..."
                icon="magnifying-glass"
                clearable
            />
        </div>
        <div class="flex gap-2 shrink-0">
            <flux:button
                wire:click="$set('tab', 'disponibles')"
                :variant="$tab === 'disponibles' ? 'primary' : 'ghost'"
                size="sm"
            >
                Disponibles
            </flux:button>
            <flux:button
                wire:click="$set('tab', 'mis_proyectos')"
                :variant="$tab === 'mis_proyectos' ? 'primary' : 'ghost'"
                size="sm"
            >
                Mis proyectos
            </flux:button>
        </div>
    </div>

    {{-- Resultados --}}
    @if($this->projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-neutral-400">
            <flux:icon.magnifying-glass class="size-10 mb-3 opacity-40" />
            <p class="text-sm">No se encontraron proyectos.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->projects as $project)
                <x-project-card
                    :project="$project"
                    :ya-iniciado="in_array($project->id, $this->iniciados)"
                />
            @endforeach
        </div>
        <div class="mt-6">
            {{ $this->projects->links() }}
        </div>
    @endif
</div>