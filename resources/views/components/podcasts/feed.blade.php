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

    <div class="row">
        @forelse ($this->episodes as $episode)
            <div class="col-md-6 mb-4">
                <a href="{{ route('podcasts.show', $episode) }}" class="card style-3 text-decoration-none text-reset">
                    <div class="card-img-top position-relative"
                         style="background-image:url('{{ $episode->podcast->cover_image ? asset('storage/'.$episode->podcast->cover_image) : asset('images/grid-blog-style-1.jpg') }}'); background-size:cover; background-position:center; min-height:180px;">
                        @if ($episode->duration)
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark">
                                {{ sprintf('%d:%02d', intdiv($episode->duration, 60), $episode->duration % 60) }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body px-0 py-0 align-self-center">
                        <span class="badge bg-primary-subtle text-primary mb-2">{{ $episode->podcast->title }}</span>
                        <h5 class="card-title mb-3">{{ $episode->title }}</h5>
                        <p class="card-text text-muted small mb-0">{{ \Illuminate\Support\Str::limit($episode->summary, 80) }}</p>
                        <div class="media mt-4 mb-0 pt-1">
                            @if ($episode->autor->photo)
                                <img src="{{ asset('storage/'.$episode->autor->photo) }}" class="card-media-image me-3" alt="{{ $episode->autor->name }}">
                            @else
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold me-3 flex-shrink-0"
                                     style="width:45px;height:45px;font-size:.9rem;">
                                    {{ collect(explode(' ', $episode->autor->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                                </div>
                            @endif
                            <div class="media-body">
                                <h4 class="media-heading mb-1">{{ $episode->autor->name }}</h4>
                                <p class="media-text">{{ $episode->published_at?->format('d M') }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">No se encontraron episodios.</p>
            </div>
        @endforelse
    </div>

    @if ($this->episodes->total() > 0)
        <div class="text-muted small mb-2">
            Showing {{ $this->episodes->firstItem() }} to {{ $this->episodes->lastItem() }} of {{ $this->episodes->total() }} results
        </div>
    @endif

    {{ $this->episodes->links() }}
</div>
