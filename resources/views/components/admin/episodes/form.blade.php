<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\Autor;
use App\Models\Category;
use App\Models\Tag;

new class extends Component {
    use WithFileUploads;

    public ?Episode $episode = null;

    public ?int $podcastId = null;
    public string $title = '';
    public string $slug = '';
    public string $summary = '';
    public string $showNotes = '';
    public ?int $autorId = null;
    public bool $esLocal = true;
    public string $audioUrl = '';
    public $newAudio = null;
    public string $durationInput = '';
    public ?int $seasonNumber = null;
    public ?int $episodeNumber = null;
    public string $status = 'draft';
    public array $categoryIds = [];
    public array $tagIds = [];

    public function mount(?Episode $episode = null)
    {
        if ($episode?->exists) {
            $this->episode = $episode;
            $this->podcastId = $episode->podcast_id;
            $this->title = $episode->title;
            $this->slug = $episode->slug;
            $this->summary = $episode->summary ?? '';
            $this->showNotes = $episode->show_notes ?? '';
            $this->autorId = $episode->autor_id;
            $this->esLocal = $episode->es_local;
            $this->audioUrl = $episode->es_local ? '' : $episode->audio_url;
            $this->durationInput = $episode->duration
                ? sprintf('%d:%02d', intdiv($episode->duration, 60), $episode->duration % 60)
                : '';
            $this->seasonNumber = $episode->season_number;
            $this->episodeNumber = $episode->episode_number;
            $this->status = $episode->status;
            $this->categoryIds = $episode->categories->pluck('id')->toArray();
            $this->tagIds = $episode->tags->pluck('id')->toArray();
        }
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    #[Computed]
    public function podcasts()
    {
        return Podcast::orderBy('title')->get();
    }

    #[Computed]
    public function autores()
    {
        return Autor::orderBy('name')->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::paraPodcasts()->orderBy('name')->get();
    }

    #[Computed]
    public function tags()
    {
        return Tag::paraPodcasts()->orderBy('name')->get();
    }

    private function parseDuration(string $input): ?int
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        if (preg_match('/^(\d+):([0-5]\d)$/', $input, $m)) {
            return ((int) $m[1] * 60) + (int) $m[2];
        }

        return null;
    }

    public function save()
    {
        $this->validate([
            'podcastId' => 'required|exists:podcasts,id',
            'title' => 'required|min:3|max:255',
            'slug' => ['required', 'max:255', Rule::unique('episodes', 'slug')->ignore($this->episode?->id)],
            'summary' => 'nullable|max:500',
            'autorId' => 'required|exists:autors,id',
            'audioUrl' => $this->esLocal ? 'nullable' : 'required|url',
            'newAudio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:51200',
            'seasonNumber' => 'nullable|integer|min:1',
            'episodeNumber' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published,archived',
        ]);

        $data = [
            'podcast_id' => $this->podcastId,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'show_notes' => $this->showNotes,
            'autor_id' => $this->autorId,
            'es_local' => $this->esLocal,
            'duration' => $this->parseDuration($this->durationInput),
            'season_number' => $this->seasonNumber,
            'episode_number' => $this->episodeNumber,
            'status' => $this->status,
            'published_at' => $this->status === 'published'
                ? ($this->episode?->published_at ?? now())
                : $this->episode?->published_at,
        ];

        if ($this->esLocal) {
            if ($this->newAudio) {
                if ($this->episode?->es_local && $this->episode?->audio_url) {
                    Storage::disk('public')->delete($this->episode->audio_url);
                }
                $data['audio_url'] = $this->newAudio->store('episodes', 'public');
            } elseif ($this->episode?->es_local) {
                $data['audio_url'] = $this->episode->audio_url;
            } else {
                $this->addError('newAudio', 'Debes subir un archivo de audio.');
                return;
            }
        } else {
            $data['audio_url'] = $this->audioUrl;
        }

        if ($this->episode) {
            $this->episode->update($data);
        } else {
            $this->episode = Episode::create($data);
        }

        $this->episode->categories()->sync($this->categoryIds);
        $this->episode->tags()->sync($this->tagIds);

        session()->flash('success', 'Episodio guardado correctamente.');

        return redirect()->route('admin.episodes.index');
    }
};
?>

<div>
    <form wire:submit="save">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Podcast</label>
                            <select class="form-select @error('podcastId') is-invalid @enderror" wire:model="podcastId">
                                <option value="">Selecciona un podcast</option>
                                @foreach ($this->podcasts as $podcast)
                                    <option value="{{ $podcast->id }}">{{ $podcast->title }}</option>
                                @endforeach
                            </select>
                            @error('podcastId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

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

                        <div class="mb-0">
                            <label class="form-label">Resumen</label>
                            <textarea class="form-control @error('summary') is-invalid @enderror"
                                      rows="3" wire:model="summary"></textarea>
                            @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Show notes</label>
                        <div wire:ignore x-data="{
                            init() {
                                const quill = new Quill(this.$refs.editor, {
                                    theme: 'snow',
                                    modules: {
                                        toolbar: [
                                            ['bold', 'italic', 'underline', 'strike'],
                                            ['blockquote', 'code-block'],
                                            [{ header: [1, 2, 3, false] }],
                                            [{ list: 'ordered' }, { list: 'bullet' }],
                                            ['link'],
                                            ['clean'],
                                        ],
                                    },
                                });

                                quill.root.innerHTML = @js($showNotes);

                                quill.on('text-change', () => {
                                    $wire.set('showNotes', quill.root.innerHTML);
                                });
                            }
                        }">
                            <div x-ref="editor" style="min-height:200px; background:#fff;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Autor</label>
                            <select class="form-select @error('autorId') is-invalid @enderror" wire:model="autorId">
                                <option value="">Selecciona un autor</option>
                                @foreach ($this->autores as $autor)
                                    <option value="{{ $autor->id }}">{{ $autor->name }}</option>
                                @endforeach
                            </select>
                            @error('autorId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model="status">
                                <option value="draft">Borrador</option>
                                <option value="published">Publicado</option>
                                <option value="archived">Archivado</option>
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Temporada</label>
                                <input type="number" min="1" class="form-control" wire:model="seasonNumber">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Episodio #</label>
                                <input type="number" min="1" class="form-control" wire:model="episodeNumber">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        @if ($episode && $episode->audio_url)
                            <div class="mb-3">
                                <label class="form-label">Audio actual</label>

                                @if ($episode->is_spotify_embed)
                                    <iframe src="{{ $episode->audio_url }}" width="100%" height="152"
                                            frameborder="0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                            loading="lazy"></iframe>
                                @else
                                    <audio controls class="w-100" style="height:40px;">
                                        <source src="{{ $episode->es_local ? route('audio.episode', $episode) : $episode->audio_url }}">
                                        Tu navegador no soporta el elemento de audio.
                                    </audio>
                                @endif

                                @if (! $episode->es_local && ! $episode->is_spotify_embed)
                                    <div class="form-text">Si es un enlace a una plataforma, el reproductor de aquí podría no funcionar — solo funciona con enlaces directos a un archivo de audio, o con embeds de Spotify.</div>
                                @endif
                            </div>
                        @endif
                        <label class="form-label">Duración</label>
                        <input type="text" class="form-control" wire:model="durationInput" placeholder="45:30">
                        <div class="form-text">Formato minutos:segundos.</div>

                        <label class="form-label mt-3">Origen del audio</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="esLocal" value="1" id="esLocalSi">
                            <label class="form-check-label" for="esLocalSi">Archivo local</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" wire:model.live="esLocal" value="0" id="esLocalNo">
                            <label class="form-check-label" for="esLocalNo">Enlace externo</label>
                        </div>

                        @if ($esLocal)
                            <div class="position-relative border rounded-3 text-center p-3" style="border-style: dashed !important;">
                                <input type="file" wire:model="newAudio" accept="audio/*"
                                    x-on:change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            const audio = new Audio(URL.createObjectURL(file));
                                            audio.addEventListener('loadedmetadata', () => {
                                                const mins = Math.floor(audio.duration / 60);
                                                const secs = Math.floor(audio.duration % 60).toString().padStart(2, '0');
                                                $wire.set('durationInput', mins + ':' + secs);
                                            });
                                        }
                                    "
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                                @if ($newAudio)
                                    <div class="text-muted small">{{ $newAudio->getClientOriginalName() }}</div>
                                @elseif ($episode?->es_local && $episode?->audio_url)
                                    <div class="text-muted small">Archivo actual: {{ basename($episode->audio_url) }}</div>
                                @else
                                    <div class="text-muted small">Arrastra un archivo de audio o haz clic</div>
                                @endif
                            </div>
                            <div wire:loading wire:target="newAudio" class="text-muted small mt-2">Subiendo audio...</div>
                            @error('newAudio') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        @else
                            <input type="text" class="form-control @error('audioUrl') is-invalid @enderror"
                                wire:model="audioUrl" placeholder="https://..."
                                x-on:blur="
                                    if ($el.value) {
                                        const audio = new Audio($el.value);
                                        audio.addEventListener('loadedmetadata', () => {
                                            const mins = Math.floor(audio.duration / 60);
                                            const secs = Math.floor(audio.duration % 60).toString().padStart(2, '0');
                                            $wire.set('durationInput', mins + ':' + secs);
                                        });
                                    }
                                ">
                            @error('audioUrl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body" wire:ignore x-data="{
                        init() {
                            new TomSelect(this.$refs.categorySelect, {
                                maxItems: null,
                                plugins: ['remove_button'],
                                onItemAdd: function () { this.setTextboxValue(''); this.refreshOptions(); },
                                onChange: (value) => { $wire.set('categoryIds', value.map((v) => parseInt(v))); },
                            });
                        }
                    }">
                        <label class="form-label">Categorías</label>
                        <select x-ref="categorySelect" multiple placeholder="Selecciona una o más...">
                            @foreach ($this->categories as $category)
                                <option value="{{ $category->id }}" @selected(in_array($category->id, $categoryIds))>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body" wire:ignore x-data="{
                        init() {
                            new TomSelect(this.$refs.tagSelect, {
                                maxItems: null,
                                plugins: ['remove_button'],
                                onItemAdd: function () { this.setTextboxValue(''); this.refreshOptions(); },
                                onChange: (value) => { $wire.set('tagIds', value.map((v) => parseInt(v))); },
                            });
                        }
                    }">
                        <label class="form-label">Tags</label>
                        <select x-ref="tagSelect" multiple placeholder="Selecciona uno o más...">
                            @foreach ($this->tags as $tag)
                                <option value="{{ $tag->id }}" @selected(in_array($tag->id, $tagIds))>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">
                    {{ $episode ? 'Guardar cambios' : 'Crear episodio' }}
                </button>
                <a href="{{ route('admin.episodes.index') }}" class="btn btn-outline-secondary w-100">Cancelar</a>
            </div>
        </div>
    </form>
</div>
