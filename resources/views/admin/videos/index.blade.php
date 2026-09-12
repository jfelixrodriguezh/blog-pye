@extends('layouts.app')

@section('title', 'Videos')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Videos de YouTube</h1>
            <p class="text-muted mb-0">Videos que se muestran en la sección pública "Youtube".</p>
        </div>
    </div>

    <livewire:admin.videos.index />
@endsection
