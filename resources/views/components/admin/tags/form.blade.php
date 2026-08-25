<?php

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Tag;

new class extends Component {
    public ?Tag $tag = null;

    public string $name = '';
    public string $slug = '';

    public function mount(?Tag $tag = null)
    {
        if ($tag?->exists) {
            $this->tag = $tag;
            $this->name = $tag->name;
            $this->slug = $tag->slug;
        }
    }

    public function updatedName($value)
    {
        if (! $this->tag) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'slug' => ['required', 'max:255', Rule::unique('tags', 'slug')->ignore($this->tag?->id)],
        ]);

        if ($this->tag) {
            $this->tag->update([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
        } else {
            Tag::create([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
        }

        session()->flash('success', 'Tag guardado correctamente.');

        return redirect()->route('admin.tags.index');
    }
};
?>

<div>
    <form wire:submit="save">
        <div class="card border-0 shadow-sm" style="max-width:600px;">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.live="name">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" wire:model="slug">
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $tag ? 'Guardar cambios' : 'Crear tag' }}
                </button>
                <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </form>
</div>
