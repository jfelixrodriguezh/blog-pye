<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Las credenciales no coinciden con nuestros registros.');
            return;
        }

        request()->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
};
?>

<form wire:submit="login">
    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" class="form-control" wire:model="password">
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
        <label class="form-check-label small" for="remember">Recordarme</label>
    </div>
    <button type="submit" class="btn btn-primary w-100">Entrar</button>
</form>
