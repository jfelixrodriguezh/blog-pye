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

    public function buscar()
    {
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->reset(['termino', 'libroIds']);
        $this->resetPage();
    }

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

    <form wire:submit="buscar"
        x-data="{ tomSelectInstance: null }"
        x-init="
            tomSelectInstance = new TomSelect($refs.libroSelect, {
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
        ">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label small text-muted">Palabra o frase</label>
                        <div class="input-group">
                            <input type="text" wire:model="termino"
                                class="form-control" placeholder="Ej: trigo, amor, esperanza...">
                            <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Libros (opcional, vacío = toda la Biblia)</label>
                        <div wire:ignore>
                            <select x-ref="libroSelect" multiple placeholder="Todos los libros..." class="form-select">
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
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100"
                                style="padding: 0.75rem 1.25rem; font-size: 15px;"
                                wire:click="limpiar"
                                @click="tomSelectInstance.clear()">
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
