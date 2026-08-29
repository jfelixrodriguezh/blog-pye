<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Himno;
use App\Models\Himnario;
use App\Models\Category;
use App\Models\Autor;
use App\Models\Tono;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public bool $buscarEnLetra = false;
    public string $himnarioFilter = '';
    public string $categoryFilter = '';
    public string $autorFilter = '';
    public string $tonoFilter = '';

    public function buscar()
    {
        $this->resetPage();
    }
    public function updatedBuscarEnLetra() { $this->resetPage(); }
    public function updatedHimnarioFilter() { $this->resetPage(); }
    public function updatedCategoryFilter() { $this->resetPage(); }
    public function updatedAutorFilter() { $this->resetPage(); }
    public function updatedTonoFilter() { $this->resetPage(); }

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
    public function tonos()
    {
        return Tono::orderBy('id')->get();
    }

    #[Computed]
    public function himnos()
    {
        return Himno::query()
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
            ->when($this->tonoFilter, fn ($q) => $q->where('tono_id', $this->tonoFilter))
            ->with(['himnario', 'category', 'autor', 'tono'])
            ->orderBy('numero')
            ->paginate(15);
    }

    public function delete($himnoId)
    {
        $himno = \App\Models\Himno::findOrFail($himnoId);
        $himno->delete();
        session()->flash('success', 'Himno eliminado.');
    }
};
?>

<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-3 g-2 align-items-center">
        <div class="col-md-5">
            <form wire:submit="buscar" class="input-group">
                <input type="text" wire:model="search"
                    class="form-control" placeholder="Buscar por N° o Título...">
                <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
        </div>
        <div class="col-md-7 d-flex align-items-center gap-2 flex-wrap justify-content-md-end">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" id="buscarEnLetra" wire:model.live="buscarEnLetra">
                <label class="form-check-label small" for="buscarEnLetra">Búsqueda en Letra</label>
            </div>
        </div>
    </div>

    <div class="row mb-3 g-2">
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
            <select class="form-select" wire:model.live="tonoFilter">
                <option value="">Filtrar Tono</option>
                @foreach ($this->tonos as $tono)
                    <option value="{{ $tono->id }}">{{ $tono->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">N°</th>
                        <th class="border-0">Título</th>
                        <th class="border-0">Himnario</th>
                        <th class="border-0">Categoría</th>
                        <th class="border-0">Autor</th>
                        <th class="border-0">Tono</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->himnos as $himno)
                        <tr wire:key="himno-{{ $himno->id }}">
                            <td class="ps-4">{{ $himno->numero }}</td>
                            <td class="fw-semibold">{{ $himno->titulo }}</td>
                            <td class="text-muted">{{ $himno->himnario->nombre }}</td>
                            <td class="text-muted">{{ $himno->category->name ?? '—' }}</td>
                            <td class="text-muted">{{ $himno->autor->name ?? '—' }}</td>
                            <td class="text-muted">{{ $himno->tono->nombre ?? '—' }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('himnos.show', ['himnario' => $himno->himnario, 'numero' => $himno->numero]) }}" target="_blank" class="btn btn-sm btn-light text-muted" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.himnos.edit', $himno) }}" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar el himno ' + @js($himno->titulo) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $himno->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">No hay himnos todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->himnos->links() }}
    </div>
</div>
