@extends('layouts.app')
@section('title', 'Editar tag')
@section('content')
    <h1 class="h2 fw-bold mb-4">Editar tag</h1>
    <livewire:admin.tags.form :tag="$tag" />
@endsection
