<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Podcast;

new class extends Component {
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;
    public ?Podcast $editing = null;

    public string $title = '';
    public string $slug = '';
    public string $description = '';
    public string $status = 'draft';
    public $newCover = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
    {
        if (! $this->editing) {
            $this->slug = Str::slug($value);
        }
    }

    #[Computed]
    public function podcasts()
    {
        return Podcast::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->withCount('episodes')
            ->latest()
            ->paginate(10);
    }

    public function create()
    {
        $this->reset(['editing', 'title', 'slug', 'description', 'newCover']);
        $this->status = 'draft';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($podcastId)
    {
        $podcast = Podcast::findOrFail($podcastId);

        $this->editing = $podcast;
        $this->title = $podcast->title;
        $this->slug = $podcast->slug;
        $this->description = $podcast->description ?? '';
        $this->status = $podcast->status;
        $this->newCover = null;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'slug' => ['required', 'max:255', Rule::unique('podcasts', 'slug')->ignore($this->editing?->id)],
            'description' => 'nullable|max:1000',
            'status' => 'required|in:draft,published,archived',
            'newCover' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
        ];

        if ($this->newCover) {
            if ($this->editing?->cover_image) {
                Storage::disk('public')->delete($this->editing->cover_image);
            }
            $data['cover_image'] = $this->newCover->store('podcasts', 'public');
        }

        if ($this->editing) {
            $this->editing->update($data);
        } else {
            Podcast::create($data);
        }

        $this->showModal = false;
        session()->flash('success', 'Podcast guardado correctamente.');
    }

    public function delete($podcastId)
    {
        $podcast = Podcast::findOrFail($podcastId);
        $podcast->delete();
        session()->flash('success', 'Podcast eliminado.');
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
                   class="form-control" placeholder="Buscar por título...">
        </div>
        <div class="col-md-6 text-end">
            <button type="button" wire:click="create" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo podcast
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Podcast</th>
                        <th class="border-0">Episodios</th>
                        <th class="border-0">Estado</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->podcasts as $podcast)
                        <tr wire:key="podcast-{{ $podcast->id }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $podcast->cover_image ? asset('storage/'.$podcast->cover_image) : asset('images/grid-blog-style-1.jpg') }}"
                                         class="rounded-2 flex-shrink-0" style="width:44px;height:44px;object-fit:cover;" alt="">
                                    <div>
                                        <div class="fw-semibold">{{ $podcast->title }}</div>
                                        <div class="text-muted small">{{ $podcast->description ? \Illuminate\Support\Str::limit($podcast->description, 50) : 'Sin descripción' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $podcast->episodes_count }}</td>
                            <td>
                                @php
                                    $badge = match ($podcast->status) {
                                        'published' => 'bg-success-subtle text-success-emphasis',
                                        'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                                        'archived' => 'bg-dark-subtle text-dark-emphasis',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $badge }} fw-normal px-3 py-2">{{ ucfirst($podcast->status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" wire:click="edit({{ $podcast->id }})" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar el podcast ' + @js($podcast->title) + ' y sus {{ $podcast->episodes_count }} episodio(s) asociados. Esta acción no se puede deshacer.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $podcast->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">No hay podcasts todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->podcasts->links() }}
    </div>

    <div x-data x-show="$wire.showModal" x-cloak class="modal-overlay-centered">
        <div @click.away="$wire.showModal = false"
             style="background:#fff; border-radius:12px; width:100%; max-width:560px; margin:16px;">
            <form wire:submit="save">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $editing ? 'Editar podcast' : 'Agregar podcast' }}</h5>
                    <button type="button" class="btn-close" @click="$wire.showModal = false" aria-label="Cerrar"></button>
                </div>

                <div class="p-3">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model.live="title">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" wire:model="slug" disabled>
                        <div class="form-text">Se genera automáticamente a partir del título.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  rows="3" wire:model="description"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" wire:model="status">
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                            <option value="archived">Archivado</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Portada</label>
                        <div class="position-relative border rounded-3 text-center p-3" style="border-style: dashed !important;">
                            <input type="file" wire:model="newCover" accept="image/*"
                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                            @if ($newCover)
                                <img src="{{ $newCover->temporaryUrl() }}" class="rounded" style="max-height:100px;">
                            @elseif ($editing?->cover_image)
                                <img src="{{ asset('storage/'.$editing->cover_image) }}" class="rounded" style="max-height:100px;">
                            @else
                                <div class="text-muted small">Arrastra una portada o haz clic</div>
                            @endif
                        </div>
                        <div wire:loading wire:target="newCover" class="text-muted small mt-2">Subiendo imagen...</div>
                        @error('newCover') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
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
