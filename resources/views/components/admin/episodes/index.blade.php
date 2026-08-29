<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Episode;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function episodes()
    {
        return Episode::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->with(['podcast', 'autor'])
            ->latest()
            ->paginate(10);
    }

    // Recibe el ID como número simple (no como Episode $episode) a propósito:
    // el modelo tiene getRouteKeyName() = 'slug', y eso puede confundir a
    // Livewire cuando intenta resolver el parámetro automáticamente.
    public function delete($episodeId)
    {
        $episode = Episode::findOrFail($episodeId);
        $episode->delete();
        session()->flash('success', 'Episodio eliminado.');
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
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Episodio</th>
                        <th class="border-0">Podcast</th>
                        <th class="border-0">Autor</th>
                        <th class="border-0">T/E</th>
                        <th class="border-0">Estado</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->episodes as $episode)
                        <tr wire:key="episode-{{ $episode->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $episode->title }}</div>
                                <div class="text-muted small">{{ $episode->podcast->title }}</div>
                            </td>
                            <td class="text-muted">{{ $episode->podcast->title }}</td>
                            <td class="text-muted">{{ $episode->autor->name }}</td>
                            <td class="text-muted">
                                @if ($episode->season_number || $episode->episode_number)
                                    T{{ $episode->season_number ?? '-' }} E{{ $episode->episode_number ?? '-' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = match ($episode->status) {
                                        'published' => 'bg-success-subtle text-success-emphasis',
                                        'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                                        'archived' => 'bg-dark-subtle text-dark-emphasis',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $badge }} fw-normal px-3 py-2">{{ ucfirst($episode->status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.episodes.edit', $episode) }}" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>
                                    <button
                                        type="button"
                                        x-on:click="Swal.fire({
                                            title: '¿Estás seguro?',
                                            text: 'Vas a eliminar el episodio ' + @js($episode->title) + '.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar'
                                        }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $episode->id }}) } })"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No hay episodios todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->episodes->links() }}
    </div>
</div>
