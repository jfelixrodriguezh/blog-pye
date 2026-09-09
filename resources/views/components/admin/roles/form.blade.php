<?php

use Livewire\Component;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

new class extends Component {
    public ?Role $role = null;
    public string $nombre = '';
    public array $permisos = [];

    public function modulos(): array
    {
        return [
            'posts' => 'Posts',
            'autores' => 'Autores',
            'categorias' => 'Categorías',
            'tags' => 'Tags',
            'podcasts' => 'Podcasts',
            'episodios' => 'Episodios',
            'himnarios' => 'Himnarios',
            'tonos' => 'Tonos',
            'himnos' => 'Himnos',
        ];
    }

    public function acciones(): array
    {
        return ['ver' => 'Ver', 'crear' => 'Crear', 'editar' => 'Editar', 'eliminar' => 'Eliminar'];
    }

    public function mount(?Role $role = null)
    {
        if ($role?->exists) {
            $this->role = $role;
            $this->nombre = $role->name;
        }

        $permisosDelRol = $this->role?->permissions->pluck('name')->toArray() ?? [];

        foreach ($this->modulos() as $modulo => $etiqueta) {
            foreach (array_keys($this->acciones()) as $accion) {
                $key = "{$modulo}_{$accion}";
                $this->permisos[$key] = in_array("{$accion} {$modulo}", $permisosDelRol);
            }
        }
    }

    public function save()
    {
        $this->validate([
            'nombre' => ['required', 'min:2', 'max:255', Rule::unique('roles', 'name')->ignore($this->role?->id)],
        ]);

        if ($this->role) {
            $this->role->update(['name' => $this->nombre]);
        } else {
            $this->role = Role::create(['name' => $this->nombre]);
        }

        $permisosActivos = [];
        foreach ($this->modulos() as $modulo => $etiqueta) {
            foreach (array_keys($this->acciones()) as $accion) {
                if ($this->permisos["{$modulo}_{$accion}"] ?? false) {
                    $permisosActivos[] = "{$accion} {$modulo}";
                }
            }
        }

        $this->role->syncPermissions($permisosActivos);

        session()->flash('success', 'Rol guardado correctamente.');

        return redirect()->route('admin.roles.index');
    }
};
?>

<div>
    <form wire:submit="save">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <label class="form-label">Nombre del rol</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" wire:model="nombre" style="max-width:400px;">
                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Permisos por módulo</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-uppercase text-muted small">
                                <th>Módulo</th>
                                <th class="text-center text-success">Ver</th>
                                <th class="text-center text-primary">Crear</th>
                                <th class="text-center text-warning">Editar</th>
                                <th class="text-center text-danger">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->modulos() as $modulo => $etiqueta)
                                <tr>
                                    <td class="fw-semibold">{{ $etiqueta }}</td>
                                    <td class="text-center">
                                        <div class="form-check form-check-success d-inline-block">
                                            <input type="checkbox" class="form-check-input" wire:model="permisos.{{ $modulo }}_ver">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-check-primary d-inline-block">
                                            <input type="checkbox" class="form-check-input" wire:model="permisos.{{ $modulo }}_crear">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-check-warning d-inline-block">
                                            <input type="checkbox" class="form-check-input" wire:model="permisos.{{ $modulo }}_editar">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-check-danger d-inline-block">
                                            <input type="checkbox" class="form-check-input" wire:model="permisos.{{ $modulo }}_eliminar">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $role ? 'Guardar cambios' : 'Crear rol' }}</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
