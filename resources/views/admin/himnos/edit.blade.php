@extends('layouts.app')

@section('title', 'Editar himno')

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.himnos.index') }}">Himnos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>

    <livewire:admin.himnos.form :himno="$himno" />
@endsection
