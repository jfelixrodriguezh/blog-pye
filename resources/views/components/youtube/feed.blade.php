<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Video;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Computed]
    public function videos()
    {
        return Video::query()
            ->activos()
            ->latest()
            ->paginate(9);
    }
};
?>

<div>
    <div class="row">
        @forelse ($this->videos as $video)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="video-card" data-bs-toggle="modal" data-bs-target="#videoModal{{ $video->id }}" role="button">
                    <div class="video-thumb-wrap">
                        <img src="{{ $video->thumbnail_url ?? asset('images/grid-blog-style-1.jpg') }}" class="video-thumb" alt="{{ $video->titulo }}">
                        <span class="video-play" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        </span>
                    </div>
                    <h5 class="video-title mb-0">{{ $video->titulo }}</h5>
                </div>
            </div>

            <div class="modal fade video-modal" id="videoModal{{ $video->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0">
                        <button type="button" class="btn-close btn-close-white align-self-end mb-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        <div class="ratio ratio-16x9">
                            <iframe class="video-modal-iframe" src="" data-src="{{ $video->embed_url }}"
                                    title="{{ $video->titulo }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center">Todavía no hay videos publicados.</p>
        @endforelse
    </div>

    @if ($this->videos->hasPages())
        <div class="mt-2">
            {{ $this->videos->links() }}
        </div>
    @endif
</div>
