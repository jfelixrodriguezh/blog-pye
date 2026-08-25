<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\Post;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        if (trim($this->search) === '') {
            return null;
        }

        return Post::query()
            ->where('status', 'published')
            ->where(function ($q2) {
                $q2->where('title', 'like', "%{$this->search}%")
                ->orWhere('summary', 'like', "%{$this->search}%");
            })
            ->with(['autor', 'category'])
            ->latest('published_at')
            ->paginate(6);
    }
};
?>

<div>
    @if (is_null($this->posts))
        <div class="text-center py-5">
            <h1 class="fw-bold mb-2">Buscar en el blog</h1>
            <p class="text-muted">Escribe un término en el buscador de arriba para ver resultados.</p>
        </div>
    @else
        <h1 class="fw-bold mb-1">
            Resultados de búsqueda por: "{{ $search }}"
        </h1>
        <p class="text-muted mb-4">{{ $this->posts->total() }} resultado(s) encontrado(s)</p>

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
                            <p class="card-text text-muted small flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($post->summary, 90) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                <span class="text-muted small">{{ $post->published_at?->format('d M, Y') }}</span>
                                <span class="text-primary small fw-semibold">Leer más</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">No encontramos resultados para tu búsqueda. Intenta con otras palabras.</p>
                </div>
            @endforelse
        </div>

        {{ $this->posts->links() }}
    @endif
</div>
