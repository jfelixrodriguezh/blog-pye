<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Tag;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;
    public ?Tag $editing = null;

    public string $name = '';
    public string $slug = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function tags()
    {
        return Tag::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(10);
    }

    public function create()
    {
        $this->reset(['editing', 'name', 'slug']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(Tag $tag)
    {
        $this->editing = $tag;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function updatedName($value)
    {
        // if (! $this->editing) {
            $this->slug = Str::slug($value);
        // }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'slug' => ['required', 'max:255', Rule::unique('tags', 'slug')->ignore($this->editing?->id)],
        ]);

        if ($this->editing) {
            $this->editing->update([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
        } else {
            Tag::create([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
        }

        $this->showModal = false;
        session()->flash('success', 'Tag guardado correctamente.');
    }

    public function delete(Tag $tag)
    {
        $tag->delete();
        session()->flash('success', 'Tag eliminado.');
    }
};
?>

<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live.debounce.400ms="search"
                   class="form-control" placeholder="Buscar por nombre...">
        </div>
        <div class="col-md-6 text-end">
            <button type="button" wire:click="create" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo tag
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Tag</th>
                        <th class="border-0">Slug</th>
                        <th class="border-0">Posts</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->tags as $tag)
                        <tr wire:key="tag-{{ $tag->id }}">
                            <td class="ps-4 fw-semibold">{{ $tag->name }}</td>
                            <td class="text-muted"><code>{{ $tag->slug }}</code></td>
                            <td class="text-muted">{{ $tag->posts_count }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" wire:click="edit({{ $tag->id }})" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar el tag ' + @js($tag->name) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $tag->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                    {{-- <button
                                        wire:click="delete({{ $tag->id }})"
                                        wire:confirm="¿Seguro que quieres eliminar '{{ $tag->name }}'?"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">No hay tags todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->tags->links() }}
    </div>

    <div x-data x-show="$wire.showModal" x-cloak class="modal-overlay-centered">
        <div @click.away="$wire.showModal = false"
             style="background:#fff; border-radius:12px; width:100%; max-width:520px; margin:16px;">
            <form wire:submit="save">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $editing ? 'Editar tag' : 'Agregar tag' }}</h5>
                    <button type="button" class="btn-close" @click="$wire.showModal = false" aria-label="Cerrar"></button>
                </div>

                <div class="p-3">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.live="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" wire:model="slug">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 p-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" @click="$wire.showModal = false">Descartar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
