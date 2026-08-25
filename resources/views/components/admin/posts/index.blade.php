<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Post;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->with(['autor', 'category'])
            ->latest()
            ->paginate(10);
    }

    public function delete(Post $post)
    {
        $post->delete();

        session()->flash('success', 'Post eliminado.');
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
        <div class="col-md-6">
            <select wire:model.live="status" class="form-select" style="max-width:220px;">
                <option value="">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="published">Publicado</option>
                <option value="archived">Archivado</option>
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Post</th>
                        <th class="border-0">Author</th>
                        <th class="border-0">Category</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->posts as $post)
                        <tr wire:key="post-{{ $post->id }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $post->post_image ? asset('storage/'.$post->post_image) : asset('images/grid-blog-style-1.jpg') }}"
                                         class="rounded-2 flex-shrink-0" style="width:40px;height:40px;object-fit:cover;" alt="">
                                    <div>
                                        <div class="fw-semibold">{{ $post->title }}</div>
                                        <div class="text-muted small">{{ $post->published_at?->format('d M, Y') ?? 'Sin publicar' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $post->autor->name }}</td>
                            <td class="text-muted">{{ $post->category?->name ?? '—' }}</td>
                            <td>
                                @php
                                    $badge = match ($post->status) {
                                        'published' => 'bg-success-subtle text-success-emphasis',
                                        'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                                        'archived' => 'bg-dark-subtle text-dark-emphasis',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $badge }} fw-normal px-3 py-2">{{ ucfirst($post->status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="#" class="btn btn-sm btn-light text-muted" title="Vista previa (próximamente)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar el post ' + @js($post->title) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $post->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                    {{-- <button
                                        wire:click="delete({{ $post->id }})"
                                        wire:confirm="¿Seguro que quieres eliminar '{{ $post->title }}'? Podrás recuperarlo desde la base de datos si te equivocas."
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No hay posts todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->posts->links() }}
    </div>
</div>
