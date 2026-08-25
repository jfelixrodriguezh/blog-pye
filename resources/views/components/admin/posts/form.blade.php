<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Autor;
use App\Models\Category;
use App\Models\Tag;

new class extends Component {
    use WithFileUploads;

    public ?Post $post = null;
    public $newImage = null;
    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $summary = '';
    public ?int $autorId = null;
    public ?int $categoryId = null;
    public array $tagIds = [];
    public string $status = 'draft';
    public bool $featured = false;

    public function mount(?Post $post = null)
    {
        if ($post?->exists) {
            $this->post = $post;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->content = $post->content;
            $this->summary = $post->summary ?? '';
            $this->autorId = $post->autor_id;
            $this->categoryId = $post->category_id;
            $this->tagIds = $post->tags->pluck('id')->toArray();
            $this->status = $post->status;
            $this->featured = $post->featured;
        }
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function toggleFeatured()
    {
        $this->featured = ! $this->featured;
    }

    #[Computed]
    public function currentFeatured()
    {
        return Post::where('featured', true)
            ->when($this->post, fn ($q) => $q->where('id', '!=', $this->post->id))
            ->first();
    }

    #[Computed]
    public function autores()
    {
        return Autor::orderBy('name')->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function tags()
    {
        return Tag::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:5|max:255',
            'slug' => ['required', 'max:255', Rule::unique('posts', 'slug')->ignore($this->post?->id)],
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('El contenido no puede estar vacío.');
                }
            }],
            'summary' => 'nullable|max:500',
            'autorId' => 'required|exists:autors,id',
            'categoryId' => 'required|exists:categories,id',
            'tagIds' => 'array',
            'tagIds.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published,archived',
            'newImage' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'category_id' => $this->categoryId,
            'slug' => $this->slug,
            'content' => $this->content,
            'summary' => $this->summary,
            'autor_id' => $this->autorId,
            'status' => $this->status,
            'published_at' => $this->status === 'published'
                ? ($this->post?->published_at ?? now())
                : $this->post?->published_at,
        ];

        if ($this->newImage) {
            if ($this->post?->post_image) {
                Storage::disk('public')->delete($this->post->post_image);
            }
            $data['post_image'] = $this->newImage->store('posts', 'public');
        }

        if ($this->post) {
            $this->post->update($data);
        } else {
            $this->post = Post::create($data);
        }

        if ($this->featured && ! $this->post->featured) {
            $this->post->markAsFeatured();
        } elseif (! $this->featured && $this->post->featured) {
            $this->post->update(['featured' => false]);
        }

        $this->post->tags()->sync($this->tagIds);

        session()->flash('success', 'Post guardado correctamente.');

        return redirect()->route('admin.posts.index');
    }
};
?>

<div>
    <form wire:submit="save">
        <div class="row">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   wire:model.live="title">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                   wire:model="slug">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Resumen</label>
                            <textarea class="form-control @error('summary') is-invalid @enderror"
                                      rows="3" wire:model="summary"></textarea>
                            @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Contenido</label>
                            <div wire:ignore x-data="{
                                init() {
                                    const quill = new Quill(this.$refs.editor, {
                                        theme: 'snow',
                                        modules: {
                                            toolbar: [
                                                ['bold', 'italic', 'underline', 'strike'],
                                                ['blockquote', 'code-block'],
                                                [{ header: 1 }, { header: 2 }],
                                                [{ list: 'ordered' }, { list: 'bullet' }],
                                                [{ script: 'sub' }, { script: 'super' }],
                                                [{ indent: '-1' }, { indent: '+1' }],
                                                [{ direction: 'rtl' }],
                                                [{ size: ['small', false, 'large', 'huge'] }],
                                                [{ header: [1, 2, 3, 4, 5, 6, false] }],
                                                [{ color: [] }, { background: [] }],
                                                [{ font: [] }],
                                                [{ align: [] }],
                                                ['clean'],
                                                ['link', 'image', 'video'],
                                            ],
                                        },
                                    });

                                    quill.root.innerHTML = @js($content);

                                    quill.on('text-change', () => {
                                        $wire.set('content', quill.root.innerHTML);
                                    });
                                }
                            }">
                            <div x-ref="editor" style="min-height:320px; background:#fff;"></div>
                            </div>
                            @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
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

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="featured"
                                {{ $featured ? 'checked' : '' }}
                                @if (! $featured && $this->currentFeatured)
                                    x-on:click.prevent="Swal.fire({
                                        title: '¿Estás seguro?',
                                        text: 'Ya existe un post destacado: ' + @js($this->currentFeatured->title) + '. ¿Quieres reemplazarlo por este?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Sí, reemplazar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => { if (result.isConfirmed) { $wire.toggleFeatured() } })"
                                @else
                                    wire:click="toggleFeatured"
                                @endif
                            >
                            <label class="form-check-label" for="featured">Destacar este post</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Categoría</label>
                        <select class="form-select @error('categoryId') is-invalid @enderror" wire:model="categoryId">
                            <option value="">Selecciona una categoría</option>
                            @foreach ($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('categoryId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body" wire:ignore x-data="{
                        init() {
                            new TomSelect(this.$refs.tagSelect, {
                                maxItems: null,
                                plugins: ['remove_button'],
                                onItemAdd: function () {
                                    this.setTextboxValue('');
                                    this.refreshOptions();
                                },
                                onChange: (value) => {
                                    $wire.set('tagIds', value.map((v) => parseInt(v)));
                                },
                            });
                        }
                    }">
                        <label class="form-label">Tags</label>
                        <select x-ref="tagSelect" multiple placeholder="Selecciona uno o más tags...">
                            @foreach ($this->tags as $tag)
                                <option value="{{ $tag->id }}" @selected(in_array($tag->id, $tagIds))>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Imagen destacada</label>

                        <div class="position-relative border rounded-3 text-center p-4" style="border-style: dashed !important;">
                            <input type="file" wire:model="newImage" accept="image/*"
                                class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">

                            @if ($newImage)
                                <img src="{{ $newImage->temporaryUrl() }}" class="img-fluid rounded mb-2" style="max-height:180px;">
                                <div class="small text-muted">Nueva imagen seleccionada</div>
                            @elseif ($post?->post_image)
                                <img src="{{ asset('storage/'.$post->post_image) }}" class="img-fluid rounded mb-2" style="max-height:180px;">
                                <div class="small text-muted">Imagen actual — arrastra otra para reemplazarla</div>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <div class="text-muted small">Arrastra una imagen aquí o haz clic para seleccionar</div>
                            @endif
                        </div>

                        <div wire:loading wire:target="newImage" class="text-muted small mt-2">Subiendo imagen...</div>
                        @error('newImage') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">
                    {{ $post ? 'Guardar cambios' : 'Crear post' }}
                </button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary w-100">Cancelar</a>
            </div>
        </div>
    </form>
</div>
