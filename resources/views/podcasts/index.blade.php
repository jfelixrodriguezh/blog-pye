@extends('layouts.public')

@section('title', 'Podcasts')

@section('content')
    <h1 class="fw-bold mb-4">Podcasts</h1>

    <livewire:podcasts.feed />
@endsection
