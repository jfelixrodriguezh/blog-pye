<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Autor;

new class extends Component {
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public $newPhoto = null;
    // Estado del modal
    public bool $showModal = false;
    public ?Autor $editing = null;

    // Cambia cada vez que se abre el modal (crear o editar) para forzar
    // que Alpine vuelva a inicializar el editor Quill con el contenido
    // correcto (el div del editor usa wire:ignore, así que Livewire no
    // lo actualiza solo con un cambio de $description).
    public int $formToken = 0;

    public string $name = '';
    public string $description = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function autores()
    {
        return Autor::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(10);
    }

    public function create()
    {
        $this->reset(['editing', 'name', 'description', 'newPhoto']);
        $this->resetValidation();
        $this->formToken++;
        $this->showModal = true;
    }

    public function edit(Autor $autor)
    {
        $this->editing = $autor;
        $this->name = $autor->name;
        $this->description = $autor->description ?? '';
        $this->resetValidation();
        $this->formToken++;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|max:5000',
            'newPhoto' => 'nullable|image|max:2048',
        ]);

        if ($this->newPhoto) {
            if ($this->editing?->photo) {
                Storage::disk('public')->delete($this->editing->photo);
            }
        }

        if ($this->editing) {
            $this->editing->update([
                'name' => $this->name,
                'description' => $this->description,
                'photo' => $this->newPhoto ? $this->newPhoto->store('autores', 'public') : $this->editing?->photo,
            ]);
        } else {
            Autor::create([
                'name' => $this->name,
                'description' => $this->description,
                'photo' => $this->newPhoto ? $this->newPhoto->store('autores', 'public') : $this->editing?->photo,
            ]);
        }

        $this->showModal = false;
        session()->flash('success', 'Autor guardado correctamente.');
    }

    public function delete(Autor $autor)
    {
        if ($autor->posts()->exists()) {
            session()->flash('error', "No puedes eliminar a \"{$autor->name}\" porque tiene posts asociados. Reasigna o elimina esos posts primero.");
            return;
        }

        $autor->delete();
        session()->flash('success', 'Autor eliminado.');
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
                   class="form-control" placeholder="Buscar por nombre...">
        </div>
        <div class="col-md-6 text-end">
            <button type="button" wire:click="create" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo autor
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Autor</th>
                        <th class="border-0">Posts</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->autores as $autor)
                        <tr wire:key="autor-{{ $autor->id }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($autor->photo)
                                        <img src="{{ asset('storage/'.$autor->photo) }}" class="rounded-circle flex-shrink-0" style="width:40px;height:40px;object-fit:cover;" alt="{{ $autor->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:40px;height:40px;">
                                            {{ collect(explode(' ', $autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $autor->name }}</div>
                                        <div class="text-muted small">{{ $autor->description ? \Illuminate\Support\Str::limit(strip_tags($autor->description), 60) : 'Sin descripción' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $autor->posts_count }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('autor.show', $autor) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light text-muted" title="Ver perfil público">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <button type="button" wire:click="edit({{ $autor->id }})" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar al autor ' + @js($autor->name) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $autor->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                    {{-- <button
                                        wire:click="delete({{ $autor->id }})"
                                        wire:confirm="¿Seguro que quieres eliminar a '{{ $autor->name }}'?"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">No hay autores todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->autores->links() }}
    </div>

    {{-- Modal de crear/editar --}}
    <div x-data
         x-show="$wire.showModal"
         x-cloak
         class="modal-overlay-centered">

        <div @click.away="$wire.showModal = false"
             style="background:#fff; border-radius:12px; width:100%; max-width:760px; margin:16px;">

            <form wire:submit="save">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $editing ? 'Editar autor' : 'Agregar autor' }}</h5>
                    <button type="button" class="btn-close" @click="$wire.showModal = false" aria-label="Cerrar"></button>
                </div>

                <div class="mb-3 px-3 pt-3">
                    <label class="form-label">Foto</label>
                    <div class="position-relative border rounded-3 text-center p-3" style="border-style: dashed !important;">
                        <input type="file" wire:model="newPhoto" accept="image/*"
                            class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                        @if ($newPhoto)
                            <img src="{{ $newPhoto->temporaryUrl() }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                        @elseif ($editing?->photo)
                            <img src="{{ asset('storage/'.$editing->photo) }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                        @else
                            <div class="text-muted small">Arrastra una foto o haz clic</div>
                        @endif
                    </div>
                </div>

                <div class="p-3">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Descripción</label>
                        <div wire:ignore wire:key="autor-editor-{{ $formToken }}" x-data="{
                            init() {
                                const quill = new Quill(this.$refs.editor, {
                                    theme: 'snow',
                                    modules: {
                                        toolbar: [
                                            ['bold', 'italic', 'underline', 'strike'],
                                            ['blockquote'],
                                            [{ header: [1, 2, 3, false] }],
                                            [{ list: 'ordered' }, { list: 'bullet' }],
                                            [{ align: [] }],
                                            ['link'],
                                            ['clean'],
                                        ],
                                    },
                                });

                                quill.root.innerHTML = @js($description);

                                quill.on('text-change', () => {
                                    $wire.set('description', quill.root.innerHTML);
                                });
                            }
                        }">
                            <div x-ref="editor" style="min-height:200px; background:#fff;"></div>
                        </div>
                        @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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
