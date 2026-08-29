<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\Himno;
use App\Models\Himnario;
use App\Models\Autor;
use App\Models\Category;
use App\Models\Tono;

new class extends Component {
    use WithFileUploads;

    public ?Himno $himno = null;

    public ?int $himnarioId = null;
    public ?int $numero = null;
    public string $titulo = '';
    public ?int $autorId = null;
    public ?int $categoryId = null;
    public ?int $tonoId = null;
    public string $referencia = '';
    public string $status = 'draft';

    public bool $audioEsLocal = true;
    public string $audioUrl = '';
    public $newAudio = null;
    public string $youtubeUrl = '';
    public string $informacion = '';

    public bool $partituraEsLocal = true;
    public string $partituraUrl = '';
    public $newPartitura = null;

    public array $estrofas = [];

    public function mount(?Himno $himno = null)
    {
        if ($himno?->exists) {
            $this->himno = $himno;
            $this->himnarioId = $himno->himnario_id;
            $this->numero = $himno->numero;
            $this->titulo = $himno->titulo;
            $this->autorId = $himno->autor_id;
            $this->categoryId = $himno->category_id;
            $this->tonoId = $himno->tono_id;
            $this->referencia = $himno->referencia ?? '';
            $this->status = $himno->status;
            $this->youtubeUrl = $himno->youtube_url ?? '';
            $this->informacion = $himno->informacion ?? '';
            $this->audioEsLocal = $himno->es_local;
            $this->audioUrl = $himno->es_local ? '' : ($himno->audio_url ?? '');

            $this->partituraEsLocal = $himno->partitura_es_local;
            $this->partituraUrl = $himno->partitura_es_local ? '' : ($himno->partitura ?? '');

            $this->estrofas = $himno->estrofas()->orderBy('orden')->get()
                ->map(fn ($e) => ['tipo' => $e->tipo, 'texto' => $e->texto])
                ->toArray();
        } else {
            $this->estrofas = [['tipo' => 'estrofa', 'texto' => '']];
        }
    }

    #[Computed]
    public function himnarios()
    {
        return Himnario::orderBy('nombre')->get();
    }

    #[Computed]
    public function autores()
    {
        return Autor::orderBy('name')->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::paraHimnos()->orderBy('name')->get();
    }

    #[Computed]
    public function tonos()
    {
        return Tono::orderBy('id')->get();
    }

    public function addEstrofa()
    {
        $this->estrofas[] = ['tipo' => 'estrofa', 'texto' => ''];
    }

    public function addCoro()
    {
        $this->estrofas[] = ['tipo' => 'coro', 'texto' => ''];
    }

    public function removeEstrofa($index)
    {
        unset($this->estrofas[$index]);
        $this->estrofas = array_values($this->estrofas);
    }

    public function save()
    {
        $this->validate([
            'himnarioId' => 'required|exists:himnarios,id',
            'numero' => [
                'required', 'integer', 'min:1',
                Rule::unique('himnos')->where(fn ($q) => $q->where('himnario_id', $this->himnarioId))->ignore($this->himno?->id),
            ],
            'titulo' => 'required|min:2|max:255',
            'autorId' => 'nullable|exists:autors,id',
            'categoryId' => 'nullable|exists:categories,id',
            'tonoId' => 'nullable|exists:tonos,id',
            'referencia' => 'nullable|max:255',
            'status' => 'required|in:draft,published,archived',
            'newAudio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:51200',
            'newPartitura' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'estrofas.*.texto' => 'required|min:3',
        ], [
            'numero.unique' => 'Ya existe un himno con ese número en este himnario.',
        ]);

        $data = [
            'himnario_id' => $this->himnarioId,
            'numero' => $this->numero,
            'titulo' => $this->titulo,
            'autor_id' => $this->autorId,
            'category_id' => $this->categoryId,
            'tono_id' => $this->tonoId,
            'referencia' => $this->referencia ?: null,
            'informacion' => $this->informacion,
            'youtube_url' => $this->youtubeUrl ?: null,
            'status' => $this->status,
            'published_at' => $this->status === 'published'
                ? ($this->himno?->published_at ?? now())
                : $this->himno?->published_at,
        ];

        // Audio
        if ($this->audioEsLocal) {
            if ($this->newAudio) {
                if ($this->himno?->es_local && $this->himno?->audio_url) {
                    Storage::disk('public')->delete($this->himno->audio_url);
                }
                $data['audio_url'] = $this->newAudio->store('himnos/audio', 'public');
            } elseif ($this->himno?->es_local) {
                $data['audio_url'] = $this->himno->audio_url;
            } else {
                $data['audio_url'] = null;
            }
        } else {
            $data['audio_url'] = $this->audioUrl ?: null;
        }
        $data['es_local'] = $this->audioEsLocal;

        // Partitura
        if ($this->partituraEsLocal) {
            if ($this->newPartitura) {
                if ($this->himno?->partitura_es_local && $this->himno?->partitura) {
                    Storage::disk('public')->delete($this->himno->partitura);
                }
                $data['partitura'] = $this->newPartitura->store('himnos/partituras', 'public');
            } elseif ($this->himno?->partitura_es_local) {
                $data['partitura'] = $this->himno->partitura;
            } else {
                $data['partitura'] = null;
            }
        } else {
            $data['partitura'] = $this->partituraUrl ?: null;
        }
        $data['partitura_es_local'] = $this->partituraEsLocal;

        if ($this->himno) {
            $this->himno->update($data);
        } else {
            $this->himno = Himno::create($data);
        }

        // Estrofas: borramos las viejas y creamos las nuevas en el orden actual.
        $this->himno->estrofas()->delete();

        $numeroEstrofa = 0;
        foreach ($this->estrofas as $index => $estrofa) {
            if ($estrofa['tipo'] === 'estrofa') {
                $numeroEstrofa++;
            }

            $this->himno->estrofas()->create([
                'tipo' => $estrofa['tipo'],
                'numero' => $estrofa['tipo'] === 'estrofa' ? $numeroEstrofa : null,
                'texto' => $estrofa['texto'],
                'orden' => $index + 1,
            ]);
        }

        session()->flash('success', 'Himno guardado correctamente.');

        return redirect()->route('admin.himnos.index');
    }
};
?>

<div>
    <form wire:submit="save">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Número</label>
                        <input type="number" min="1" class="form-control @error('numero') is-invalid @enderror" wire:model="numero">
                        @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" wire:model="titulo">
                        @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Autor</label>
                        <select class="form-select" wire:model="autorId">
                            <option value="">Sin autor</option>
                            @foreach ($this->autores as $autor)
                                <option value="{{ $autor->id }}">{{ $autor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Himnario</label>
                        <select class="form-select @error('himnarioId') is-invalid @enderror" wire:model="himnarioId">
                            <option value="">Selecciona un himnario</option>
                            @foreach ($this->himnarios as $himnario)
                                <option value="{{ $himnario->id }}">{{ $himnario->nombre }}</option>
                            @endforeach
                        </select>
                        @error('himnarioId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" wire:model="categoryId">
                            <option value="">Sin categoría</option>
                            @foreach ($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tono</label>
                        <select class="form-select" wire:model="tonoId">
                            <option value="">Sin tono</option>
                            @foreach ($this->tonos as $tono)
                                <option value="{{ $tono->id }}">{{ $tono->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Información Adicional</h6>
                <div class="mb-3">
                    <label class="form-label">Referencia Bíblica (Opcional)</label>
                    <input type="text" class="form-control" wire:model="referencia" placeholder="Ej: Salmo 100">
                </div>
                <div class="mb-3">
                    <label class="form-label">URL de YouTube (Opcional)</label>
                    <input type="text" class="form-control" wire:model="youtubeUrl" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Audio del Himno</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check" wire:model.live="audioEsLocal" value="1" id="audioLocal">
                            <label class="btn btn-outline-secondary btn-sm" for="audioLocal">Archivo</label>
                            <input type="radio" class="btn-check" wire:model.live="audioEsLocal" value="0" id="audioLink">
                            <label class="btn btn-outline-secondary btn-sm" for="audioLink">Enlace</label>
                        </div>

                        @if ($audioEsLocal)
                            <div class="position-relative border rounded-3 text-center p-2" style="border-style: dashed !important;">
                                <input type="file" wire:model="newAudio" accept="audio/*"
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                                @if ($newAudio)
                                    <div class="text-muted small">{{ $newAudio->getClientOriginalName() }}</div>
                                @elseif ($himno?->es_local && $himno?->audio_url)
                                    <div class="text-muted small">Archivo actual: {{ basename($himno->audio_url) }}</div>
                                @else
                                    <div class="text-muted small">Arrastra un audio o haz clic</div>
                                @endif
                            </div>
                            <div wire:loading wire:target="newAudio" class="text-muted small mt-1">Subiendo...</div>
                        @else
                            <input type="text" class="form-control" wire:model="audioUrl" placeholder="https://youtube.com/...">
                        @endif
                        @error('newAudio') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Partitura (PDF o Imagen)</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check" wire:model.live="partituraEsLocal" value="1" id="partituraLocal">
                            <label class="btn btn-outline-secondary btn-sm" for="partituraLocal">Archivo</label>
                            <input type="radio" class="btn-check" wire:model.live="partituraEsLocal" value="0" id="partituraLink">
                            <label class="btn btn-outline-secondary btn-sm" for="partituraLink">Enlace</label>
                        </div>

                        @if ($partituraEsLocal)
                            <div class="position-relative border rounded-3 text-center p-2" style="border-style: dashed !important;">
                                <input type="file" wire:model="newPartitura" accept="image/*,application/pdf"
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                                @if ($newPartitura)
                                    <div class="text-muted small">{{ $newPartitura->getClientOriginalName() }}</div>
                                @elseif ($himno?->partitura_es_local && $himno?->partitura)
                                    <div class="text-muted small">Archivo actual: {{ basename($himno->partitura) }}</div>
                                @else
                                    <div class="text-muted small">Arrastra un PDF/imagen o haz clic</div>
                                @endif
                            </div>
                            <div wire:loading wire:target="newPartitura" class="text-muted small mt-1">Subiendo...</div>
                        @else
                            <input type="text" class="form-control" wire:model="partituraUrl" placeholder="https://...">
                        @endif
                        @error('newPartitura') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Letra del Himno</h6>

                @foreach ($estrofas as $index => $estrofa)
                    <div class="border rounded-3 p-3 mb-2" wire:key="estrofa-{{ $index }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge {{ $estrofa['tipo'] === 'coro' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-primary-subtle text-primary' }}">
                                {{ $estrofa['tipo'] === 'coro' ? 'Coro' : 'Estrofa' }}
                            </span>
                            <button type="button" wire:click="removeEstrofa({{ $index }})" class="btn btn-sm btn-link text-danger p-0" title="Quitar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <textarea class="form-control @error('estrofas.'.$index.'.texto') is-invalid @enderror"
                                  rows="4" wire:model="estrofas.{{ $index }}.texto" placeholder="Escribe la letra aquí..."></textarea>
                        @error('estrofas.'.$index.'.texto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endforeach

                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button type="button" wire:click="addEstrofa" class="btn btn-outline-primary btn-sm">+ Agregar Estrofa</button>
                    <button type="button" wire:click="addCoro" class="btn btn-outline-warning btn-sm">+ Agregar Coro</button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <label class="form-label">Estado</label>
                <select class="form-select" style="max-width:220px;" wire:model="status">
                    <option value="draft">Borrador</option>
                    <option value="published">Publicado</option>
                    <option value="archived">Archivado</option>
                </select>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Información (historia del himno)</h6>
                <div wire:ignore x-data="{
                    init() {
                        const quill = new Quill(this.$refs.editor, {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ header: [1, 2, 3, false] }],
                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                    ['link'],
                                    ['clean'],
                                ],
                            },
                        });

                        quill.root.innerHTML = @js($informacion);

                        quill.on('text-change', () => {
                            $wire.set('informacion', quill.root.innerHTML);
                        });
                    }
                }">
                    <div x-ref="editor" style="min-height:180px; background:#fff;"></div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                {{ $himno ? 'Guardar cambios' : 'Guardar himno' }}
            </button>
            <a href="{{ route('admin.himnos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
