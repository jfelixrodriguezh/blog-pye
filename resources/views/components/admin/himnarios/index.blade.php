<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Himnario;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;
    public ?Himnario $editing = null;

    public string $nombre = '';
    public string $slug = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedNombre($value)
    {
        if (! $this->editing) {
            $this->slug = Str::slug($value);
        }
    }

    #[Computed]
    public function himnarios()
    {
        return Himnario::query()
            ->when($this->search, fn ($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->withCount('himnos')
            ->orderBy('nombre')
            ->paginate(10);
    }

    public function create()
    {
        $this->reset(['editing', 'nombre', 'slug']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($himnarioId)
    {
        $himnario = Himnario::findOrFail($himnarioId);
        $this->editing = $himnario;
        $this->nombre = $himnario->nombre;
        $this->slug = $himnario->slug;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nombre' => ['required', 'min:2', 'max:255', Rule::unique('himnarios', 'nombre')->ignore($this->editing?->id)],
        ]);

        $this->slug = Str::slug($this->nombre);

        if ($this->editing) {
            $this->editing->update(['nombre' => $this->nombre, 'slug' => $this->slug]);
        } else {
            Himnario::create(['nombre' => $this->nombre, 'slug' => $this->slug]);
        }

        $this->showModal = false;
        session()->flash('success', 'Himnario guardado correctamente.');
    }

    public function delete($himnarioId)
    {
        $himnario = Himnario::findOrFail($himnarioId);

        if ($himnario->himnos()->exists()) {
            session()->flash('error', "No puedes eliminar \"{$himnario->nombre}\" porque tiene himnos asociados.");
            return;
        }

        $himnario->delete();
        session()->flash('success', 'Himnario eliminado.');
    }
};
?>

<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live.debounce.400ms="search"
                   class="form-control" placeholder="Buscar himnario...">
        </div>
        <div class="col-md-6 text-end">
            <button type="button" wire:click="create" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo Himnario
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Nombre</th>
                        <th class="border-0">Himnos</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->himnarios as $himnario)
                        <tr wire:key="himnario-{{ $himnario->id }}">
                            <td class="ps-4 fw-semibold">{{ $himnario->nombre }}</td>
                            <td class="text-muted">{{ $himnario->himnos_count }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" wire:click="edit({{ $himnario->id }})" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar ' + @js($himnario->nombre) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $himnario->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">No hay himnarios todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->himnarios->links() }}
    </div>

    <div x-data x-show="$wire.showModal" x-cloak class="modal-overlay-centered">
        <div @click.away="$wire.showModal = false"
             style="background:#fff; border-radius:12px; width:100%; max-width:480px; margin:16px;">
            <form wire:submit="save">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $editing ? 'Editar himnario' : 'Nuevo himnario' }}</h5>
                    <button type="button" class="btn-close" @click="$wire.showModal = false" aria-label="Cerrar"></button>
                </div>
                <div class="p-3">
                    <label class="form-label">Nombre del Himnario</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" wire:model="nombre">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="d-flex justify-content-end gap-2 p-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" @click="$wire.showModal = false">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
