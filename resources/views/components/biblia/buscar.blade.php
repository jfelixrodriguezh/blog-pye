<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\Versiculo;
use App\Models\Testamento;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $termino = '';

    #[Url(as: 'libros')]
    public array $libroIds = [];

    public function updatedTermino()
    {
        $this->resetPage();
    }

    public function updatedLibroIds()
    {
        $this->resetPage();
    }

    #[Computed]
    public function testamentos()
    {
        return Testamento::with('libros')->orderBy('orden')->get();
    }

    #[Computed]
    public function resultados()
    {
        if (trim($this->termino) === '') {
            return null;
        }

        return Versiculo::query()
            ->where('texto', 'like', '%'.$this->termino.'%')
            ->when($this->libroIds, fn ($q) => $q->whereIn('libro_id', $this->libroIds))
            ->with('libro')
            ->orderBy('libro_id')
            ->orderBy('capitulo')
            ->orderBy('numero')
            ->paginate(15);
    }

    public function resaltar(string $texto): string
    {
        if (trim($this->termino) === '') {
            return e($texto);
        }

        return preg_replace(
            '/('.preg_quote($this->termino, '/').')/ui',
            '<mark>$1</mark>',
            e($texto)
        );
    }
};
?>

<div>
    <h1 class="fw-bold mb-4">Buscar en la Biblia</h1>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Palabra o frase</label>
                    <input type="text" wire:model.live.debounce.500ms="termino"
                           class="form-control" placeholder="Ej: trigo, amor, esperanza...">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Libros (opcional, vacío = toda la Biblia)</label>
                    <div wire:ignore x-data="{
                        init() {
                            new TomSelect(this.$refs.libroSelect, {
                                maxItems: null,
                                plugins: ['remove_button'],
                                onItemAdd: function () {
                                    this.setTextboxValue('');
                                    this.refreshOptions();
                                },
                                onChange: (value) => {
                                    $wire.set('libroIds', value.map((v) => parseInt(v)));
                                },
                            });
                        }
                    }">
                        <select x-ref="libroSelect" multiple placeholder="Todos los libros...">
                            @foreach ($this->testamentos as $testamento)
                                <optgroup label="{{ $testamento->nombre }}">
                                    @foreach ($testamento->libros as $libro)
                                        <option value="{{ $libro->id }}" @selected(in_array($libro->id, $libroIds))>
                                            {{ $libro->nombre }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (is_null($this->resultados))
        <p class="text-muted text-center py-5">Escribe una palabra arriba para empezar a buscar.</p>
    @else
        <p class="text-muted mb-3">{{ $this->resultados->total() }} versículo(s) encontrado(s)</p>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @forelse ($this->resultados as $versiculo)
                    <a href="{{ route('biblia.leer', ['libro' => $versiculo->libro, 'capitulo' => $versiculo->capitulo]) }}#v{{ $versiculo->numero }}"
                       class="d-block text-decoration-none text-reset border-bottom py-3">
                        <div class="fw-semibold text-primary small mb-1">
                            {{ $versiculo->libro->nombre }} {{ $versiculo->capitulo }}:{{ $versiculo->numero }}
                        </div>
                        <div class="text-dark">{!! $this->resaltar($versiculo->texto) !!}</div>
                    </a>
                @empty
                    <p class="text-muted mb-0">No se encontraron versículos con ese término.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-3">
            {{ $this->resultados->links() }}
        </div>
    @endif
</div>
