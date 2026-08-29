<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Himno;
use App\Models\Himnario;
use App\Models\Category;
use App\Models\Autor;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public bool $buscarEnLetra = false;
    public string $himnarioFilter = '';
    public string $categoryFilter = '';
    public string $autorFilter = '';
    public string $sort = 'alfabetico';

    public function buscar()
    {
        $this->resetPage();
    }

    public function updatedBuscarEnLetra() { $this->resetPage(); }
    public function updatedHimnarioFilter() { $this->resetPage(); }
    public function updatedCategoryFilter() { $this->resetPage(); }
    public function updatedAutorFilter() { $this->resetPage(); }
    public function updatedSort() { $this->resetPage(); }

    #[Computed]
    public function himnarios()
    {
        return Himnario::orderBy('nombre')->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::paraHimnos()->orderBy('name')->get();
    }

    #[Computed]
    public function autores()
    {
        return Autor::orderBy('name')->get();
    }

    #[Computed]
    public function himnos()
    {
        return Himno::query()
            ->where('status', 'published')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('titulo', 'like', "%{$this->search}%")
                       ->orWhere('numero', 'like', "%{$this->search}%");
                    if ($this->buscarEnLetra) {
                        $q2->orWhereHas('estrofas', fn ($q3) => $q3->where('texto', 'like', "%{$this->search}%"));
                    }
                });
            })
            ->when($this->himnarioFilter, fn ($q) => $q->where('himnario_id', $this->himnarioFilter))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->autorFilter, fn ($q) => $q->where('autor_id', $this->autorFilter))
            ->with(['himnario', 'autor'])
            ->when(
                $this->sort === 'numero',
                fn ($q) => $q->orderBy('numero'),
                fn ($q) => $q->orderBy('titulo')
            )
            ->paginate(15);
    }
};
?>

<div>
    <h1 class="fw-bold text-center mb-4">Himnos y Cánticos del Evangelio</h1>

    <div class="row mb-3 g-2 align-items-center">
        <div class="col-lg-6">
            <form wire:submit="buscar" class="input-group">
                <input type="text" wire:model="search" class="form-control" placeholder="Buscar por N° o Título...">
                <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-lg-end">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" id="buscarEnLetra" wire:model.live="buscarEnLetra">
                <label class="form-check-label small" for="buscarEnLetra">Búsqueda en Letra</label>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-2">
        <div class="col-md-3">
            <select class="form-select" wire:model.live="himnarioFilter">
                <option value="">Filtrar Himnario</option>
                @foreach ($this->himnarios as $himnario)
                    <option value="{{ $himnario->id }}">{{ $himnario->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="categoryFilter">
                <option value="">Filtrar Categoría</option>
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="autorFilter">
                <option value="">Filtrar Autor</option>
                @foreach ($this->autores as $autor)
                    <option value="{{ $autor->id }}">{{ $autor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="sort">
                <option value="alfabetico">Ordenar Alfabéticamente</option>
                <option value="numero">Ordenar por Número</option>
            </select>
        </div>
    </div>

    <div class="d-flex flex-column gap-2">
        @forelse ($this->himnos as $himno)
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    <div class="flex-grow-1 row">
                        <div class="col-md-5 fw-semibold">{{ $himno->titulo }}</div>
                        <div class="col-md-4 text-muted">{{ $himno->himnario->nombre }}</div>
                        <div class="col-md-3 text-muted">{{ $himno->autor->name ?? '—' }}</div>
                    </div>
                    <a href="{{ route('himnos.show', ['himnario' => $himno->himnario, 'numero' => $himno->numero]) }}"
                       class="btn btn-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <p class="text-muted text-center">No se encontraron himnos.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $this->himnos->links() }}
    </div>
</div>
