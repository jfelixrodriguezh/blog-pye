<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

new class extends Component {
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;
    public ?User $editing = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';
    public $newPhoto = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, fn ($q) => $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->search}%")
                   ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->with('roles')
            ->orderBy('name')
            ->paginate(10);
    }

    public function create()
    {
        $this->reset(['editing', 'name', 'email', 'password', 'password_confirmation', 'role', 'newPhoto']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        $this->editing = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = $user->roles->first()?->name ?? '';
        $this->newPhoto = null;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editing?->id)],
            'password' => $this->editing ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'newPhoto' => 'nullable|image|max:2048',
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->newPhoto) {
            if ($this->editing?->photo) {
                Storage::disk('public')->delete($this->editing->photo);
            }
            $data['photo'] = $this->newPhoto->store('users', 'public');
        }

        if ($this->editing) {
            $this->editing->update($data);
            $user = $this->editing;
        } else {
            $user = User::create($data);
        }

        $user->syncRoles([$this->role]);

        $this->showModal = false;
        session()->flash('success', 'Usuario guardado correctamente.');
    }

    public function delete($userId)
    {
        if ($userId == auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');
            return;
        }

        $user = User::findOrFail($userId);

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();
        session()->flash('success', 'Usuario eliminado.');
    }
};
?>

<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live.debounce.400ms="search"
                   class="form-control" placeholder="Buscar por nombre o correo...">
        </div>
        <div class="col-md-6 text-end">
            <button type="button" wire:click="create" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo usuario
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase text-muted small">
                        <th class="border-0 ps-4">Usuario</th>
                        <th class="border-0">Rol</th>
                        <th class="border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($user->photo)
                                        <img src="{{ asset('storage/'.$user->photo) }}" class="rounded-circle flex-shrink-0" style="width:40px;height:40px;object-fit:cover;" alt="{{ $user->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:40px;height:40px;">
                                            {{ collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-normal px-3 py-2">
                                    {{ $user->roles->first()?->name ?? 'Sin rol' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" wire:click="edit({{ $user->id }})" class="btn btn-sm btn-light text-muted" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <button
                                            type="button"
                                            x-on:click="Swal.fire({
                                                title: '¿Estás seguro?',
                                                text: 'Vas a eliminar a ' + @js($user->name) + '.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'Sí, eliminar',
                                                cancelButtonText: 'Cancelar'
                                            }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $user->id }}) } })"
                                            class="btn btn-sm btn-light text-danger" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">No hay usuarios todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $this->users->links() }}
    </div>

    <div x-data x-show="$wire.showModal" x-cloak class="modal-overlay-centered">
        <div @click.away="$wire.showModal = false"
             style="background:#fff; border-radius:12px; width:100%; max-width:520px; margin:16px;">
            <form wire:submit="save">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $editing ? 'Editar usuario' : 'Nuevo usuario' }}</h5>
                    <button type="button" class="btn-close" @click="$wire.showModal = false" aria-label="Cerrar"></button>
                </div>

                <div class="p-3">
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <div class="position-relative border rounded-3 text-center p-3" style="border-style: dashed !important;">
                            <input type="file" wire:model="newPhoto" accept="image/*"
                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;">
                            @if ($newPhoto)
                                <img src="{{ $newPhoto->temporaryUrl() }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                            @elseif ($editing?->photo)
                                <img src="{{ asset('storage/'.$editing->photo) }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                            @else
                                <div class="text-muted small">Arrastra una foto o haz clic</div>
                            @endif
                        </div>
                        @error('newPhoto') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select @error('role') is-invalid @enderror" wire:model="role">
                            <option value="">Selecciona un rol</option>
                            @foreach ($this->roles as $roleOption)
                                <option value="{{ $roleOption->name }}">{{ $roleOption->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">
                                Contraseña
                                @if ($editing) <span class="text-muted fw-normal small">(opcional)</span> @endif
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Confirmar</label>
                            <input type="password" class="form-control" wire:model="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 p-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" @click="$wire.showModal = false">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
