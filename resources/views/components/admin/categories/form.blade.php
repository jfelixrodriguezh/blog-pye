<?php

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Category;

new class extends Component {
    public ?Category $category = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public function mount(?Category $category = null)
    {
        if ($category?->exists) {
            $this->category = $category;
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = $category->description ?? '';
        }
    }

    public function updatedName($value)
    {
        //if (! $this->category) {
            $this->slug = Str::slug($value);
        //}
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'slug' => ['required', 'max:255', Rule::unique('categories', 'slug')->ignore($this->category?->id)],
            'description' => 'nullable|max:500',
        ]);

        if ($this->category) {
            $this->category->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
        } else {
            Category::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
        }

        session()->flash('success', 'Categoría guardada correctamente.');

        return redirect()->route('admin.categories.index');
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

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              rows="3" wire:model="description"></textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $category ? 'Guardar cambios' : 'Crear categoría' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </form>
</div>
