@extends('layouts.app')

@section('content')

<x-popup-asesoria />
<x-prescription-flow :ocupaciones="$ocupaciones" />

<x-slider />

<main class="container mx-auto px-4 pb-10">
    {{-- catálogo reutilizado --}}
    @include('catalogo')
</main>

@endsection