<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Video;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;
    public ?Video $editing = null;

    public string $titulo = '';
    public string $url = '';
    public string $status = 'activo';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function videos()
    {
        return Video::query()
            ->when($this->search, fn ($q) => $q->where('titulo', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
    }

    public function create()
    {
        $this->reset(['editing', 'titulo', 'url']);
        $this->status = 'activo';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(Video $video)
    {
        $this->editing = $video;
        $this->titulo = $video->titulo;
        $this->url = $video->url;
        $this->status = $video->status;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'titulo' => 'required|min:2|max:255',
            'url' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (! preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', $value)) {
                        $fail('Ingresa una URL válida de YouTube (watch, youtu.be, embed o shorts).');
                    }
                },
            ],
            'status' => 'required|in:activo,inactivo',
        ]);

        if ($this->editing) {
            $this->editing->update([
                'titulo' => $this->titulo,
                'url' => $this->url,
                'status' => $this->status,
            ]);
        } else {
            Video::create([
                'titulo' => $this->titulo,
                'url' => $this->url,
                'status' => $this->status,
            ]);
        }

        $this->showModal = false;
        session()->flash('success', 'Video guardado correctamente.');
    }

    public function toggleStatus(Video $video)
    {
        $video->update(['status' => $video->status === 'activo' ? 'inactivo' : 'activo']);
    }

    public function delete(Video $video)
    {
        $video->delete();
        session()->flash('success', 'Video eliminado.');
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
                Nuevo video
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Miniatura</th>
                        <th class="border-0">Título</th>
                        <th class="border-0">URL</th>
                        <th class="border-0">Estado</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->videos as $video)
                        <tr wire:key="video-{{ $video->id }}">
                            <td class="ps-4" style="width:96px;">
                                @if ($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="" class="rounded-2" style="width:72px;height:44px;object-fit:cover;">
                                @else
                                    <div class="rounded-2 bg-light d-flex align-items-center justify-content-center text-muted" style="width:72px;height:44px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $video->titulo }}</td>
                            <td class="text-muted small">
                                <a href="{{ $video->url }}" target="_blank" rel="noopener">{{ Str::limit($video->url, 40) }}</a>
                            </td>
                            <td>
                                <button type="button" wire:click="toggleStatus({{ $video->id }})"
                                        class="badge border-0 {{ $video->status === 'activo' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $video->status === 'activo' ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" wire:click="edit({{ $video->id }})" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar el video ' + @js($video->titulo) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $video->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No hay videos todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->videos->links() }}
    </div>

    <div x-data x-show="$wire.showModal" x-cloak class="modal-overlay-centered">
        <div @click.away="$wire.showModal = false"
             style="background:#fff; border-radius:12px; width:100%; max-width:520px; margin:16px;">
            <form wire:submit="save">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $editing ? 'Editar video' : 'Agregar video' }}</h5>
                    <button type="button" class="btn-close" @click="$wire.showModal = false" aria-label="Cerrar"></button>
                </div>

                <div class="p-3">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" wire:model="titulo">
                        @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL de YouTube</label>
                        <input type="text" class="form-control @error('url') is-invalid @enderror" wire:model="url" placeholder="https://www.youtube.com/watch?v=...">
                        @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" wire:model="status">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
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
