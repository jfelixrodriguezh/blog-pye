<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;
    public string $categoryFilter = '';
    public string $sort = 'recent';
    protected $paginationTheme = 'bootstrap';

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    #[Computed]
    public function featuredPost()
    {
        if ((int) request()->query('page', 1) > 1) {
            return null;
        }

        if ($this->categoryFilter) {
            return null;
        }

        return Post::query()
            ->where('status', 'published')
            ->where('featured', true)
            ->with('autor')
            ->latest('published_at')
            ->first();
    }

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->where('status', 'published')
            ->when($this->featuredPost, fn ($q) => $q->where('id', '!=', $this->featuredPost->id))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->with(['autor', 'category'])
            ->when(
                $this->sort === 'oldest',
                fn ($q) => $q->oldest('published_at'),
                fn ($q) => $q->latest('published_at')
            )
            ->paginate(6);
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();
    }

    public function initials(string $name): string
    {
        return collect(explode(' ', $name))
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->join('');
    }
};
?>

<div>
    <div class="d-flex justify-content-end gap-2 mb-4">
        <select class="form-select pe-5" style="width:auto;" wire:model.live="categoryFilter">
            <option value="">Todas las categorías</option>
            @foreach ($this->categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <select class="form-select pe-5" style="width:auto;" wire:model.live="sort">
            <option value="recent">Más recientes</option>
            <option value="oldest">Más antiguos</option>
        </select>
    </div>

    @if ($this->featuredPost)
        <a href="{{ route('post.show', $this->featuredPost) }}" class="card mb-4 border-0 shadow-sm overflow-hidden text-decoration-none text-reset">
            <div class="row g-0">
                <div class="col-md-6">
                    <img src="{{ $this->featuredPost->post_image ? asset('storage/'.$this->featuredPost->post_image) : asset('images/grid-blog-style-2.jpeg') }}"
                         class="w-100 h-100 object-fit-cover" style="min-height:320px;" alt="{{ $this->featuredPost->title }}">
                </div>
                <div class="col-md-6">
                    <div class="card-body p-4 p-lg-5 d-flex flex-column h-100">
                        @if ($this->featuredPost->category)
                            <span class="badge bg-dark text-uppercase small mb-2 align-self-start">{{ $this->featuredPost->category->name }}</span>
                        @endif
                        <h2 class="fw-bold mb-3">{{ $this->featuredPost->title }}</h2>
                        <p class="text-muted">{{ $this->featuredPost->summary }}</p>
                        <div class="mt-auto d-flex align-items-center gap-2 pt-3">
                            @if ($this->featuredPost->autor->photo)
                                <img src="{{ asset('storage/'.$this->featuredPost->autor->photo) }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" alt="{{ $this->featuredPost->autor->name }}">
                            @else
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;">
                                    {{ $this->initials($this->featuredPost->autor->name) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $this->featuredPost->autor->name }}</div>
                                <div class="text-muted small">{{ $this->featuredPost->published_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @endif

    <div class="row">
        <div class="col-lg-9">
            <div class="row">
                @forelse ($this->posts as $post)
                    <div class="col-md-4 mb-4">
                        <a href="{{ route('post.show', $post) }}" class="card h-100 border-0 shadow-sm text-decoration-none text-reset">
                            <div class="position-relative">
                                <img src="{{ $post->post_image ? asset('storage/'.$post->post_image) : asset('images/grid-blog-style-1.jpg') }}"
                                     class="card-img-top" style="height:180px;object-fit:cover;" alt="{{ $post->title }}">
                                @if ($post->category)
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-dark text-uppercase small">
                                        {{ $post->category->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text text-muted small flex-grow-1">{{ Str::limit($post->summary, 90) }}</p>
                                <div class="d-flex align-items-center gap-2 pt-2 border-top mt-2">
                                    @if ($post->autor->photo)
                                        <img src="{{ asset('storage/'.$post->autor->photo) }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="{{ $post->autor->name }}">
                                    @else
                                        <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:.75rem;">
                                            {{ $this->initials($post->autor->name) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold small">{{ $post->autor->name }}</div>
                                        <div class="text-muted small">{{ $post->published_at?->format('d M') }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-muted">No hay posts publicados todavía.</p>
                @endforelse
            </div>

            {{ $this->posts->links() }}
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Categorías</h5>
                    <ul class="list-unstyled mb-0">
                        @foreach ($this->categories as $category)
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">{{ $category->name }}</span>
                                <span class="badge bg-light text-dark">{{ $category->posts_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- <div class="card border-0 bg-dark text-white">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Únete a la comunidad</h5>
                    <p class="small text-white-50">Recibe contenido cada semana directo a tu correo. (Próximamente)</p>
                    <input type="email" class="form-control mb-2" placeholder="Tu correo electrónico" disabled>
                    <button class="btn btn-primary w-100" disabled>Suscribirme</button>
                </div>
            </div> --}}
        </div>
    </div>
</div>
