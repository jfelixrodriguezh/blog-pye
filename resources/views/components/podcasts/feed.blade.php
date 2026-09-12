<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $podcastFilter = '';
    public string $categoryFilter = '';
    public string $sort = 'recent';

    public function buscar()
    {
        $this->resetPage();
    }

    public function updatedPodcastFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    #[Computed]
    public function podcasts()
    {
        return Podcast::orderBy('title')->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::paraPodcasts()
            ->withCount(['episodes' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function episodes()
    {
        return Episode::query()
            ->where('status', 'published')
            ->when($this->search, fn ($q) => $q->where(function ($q2) {
                $q2->where('title', 'like', "%{$this->search}%")
                   ->orWhere('summary', 'like', "%{$this->search}%")
                   ->orWhere('show_notes', 'like', "%{$this->search}%");
            }))
            ->when($this->podcastFilter, fn ($q) => $q->where('podcast_id', $this->podcastFilter))
            ->when($this->categoryFilter, fn ($q) => $q->whereHas(
                'categories',
                fn ($q2) => $q2->where('categories.id', $this->categoryFilter)
            ))
            ->with(['podcast', 'autor'])
            ->when(
                $this->sort === 'oldest',
                fn ($q) => $q->oldest('published_at'),
                fn ($q) => $q->latest('published_at')
            )
            ->paginate(6);
    }
};
?>

<div>
    <div class="row mb-4 g-2">
        <div class="col-lg-4">
            <form wire:submit="buscar" class="input-group">
                <input type="text" wire:model="search"
                       class="form-control" placeholder="Buscar por episodios">
                <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
        </div>
        <div class="col-lg-3 col-6">
            <select class="form-select" wire:model.live="podcastFilter">
                <option value="">Todos los Podcast</option>
                @foreach ($this->podcasts as $podcast)
                    <option value="{{ $podcast->id }}">{{ $podcast->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-6">
            <select class="form-select" wire:model.live="categoryFilter">
                <option value="">Todas las categorías</option>
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <select class="form-select" wire:model.live="sort">
                <option value="recent">Más recientes</option>
                <option value="oldest">Más antiguos</option>
            </select>
        </div>
    </div>

    <div class="podcast-list">
        @forelse ($this->episodes as $episode)
            <div class="podcast-item position-relative">
                <img src="{{ $episode->podcast->cover_image ? asset('storage/'.$episode->podcast->cover_image) : asset('images/grid-blog-style-1.jpg') }}"
                     class="podcast-thumb" alt="{{ $episode->title }}">
                <div class="podcast-body">
                    <div class="podcast-meta">
                        <span class="podcast-meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $episode->published_at?->translatedFormat('d M, Y') }}
                        </span>
                        @if ($episode->duration)
                            <span class="podcast-meta-sep">&bull;</span>
                            <span class="podcast-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ (int) ceil($episode->duration / 60) }} mins
                            </span>
                        @endif
                    </div>
                    <h5 class="podcast-title">
                        <a href="{{ route('podcasts.show', $episode) }}" class="stretched-link text-reset text-decoration-none">{{ $episode->title }}</a>
                    </h5>
                    <p class="podcast-desc">{{ \Illuminate\Support\Str::limit($episode->summary, 110) }}</p>
                </div>
                <span class="podcast-play" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                </span>
            </div>
        @empty
            <p class="text-muted">No se encontraron episodios.</p>
        @endforelse
    </div>

    @if ($this->episodes->total() > 0)
        <div class="text-muted small mb-2">
            Mostrando {{ $this->episodes->firstItem() }} a {{ $this->episodes->lastItem() }} de {{ $this->episodes->total() }} resultadoss
        </div>
    @endif

    {{ $this->episodes->links() }}
</div>
