@extends('layouts.app')

@section('title', 'Crear post')

@section('content')

    <div class="page-meta">
        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}">Posts</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear</li>
            </ol>
        </nav>
    </div>

    <livewire:admin.posts.form />

@endsection
