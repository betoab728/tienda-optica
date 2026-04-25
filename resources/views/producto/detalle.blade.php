@extends('layouts.app')

@section('content')

<main class="container mx-auto px-4 py-10">
    <div class="grid md:grid-cols-2 gap-10">

        {{-- IMÁGENES --}}
        <div x-data="{ imagenActiva: '{{ $producto->imagenes[0] }}' }">

            {{-- imagen principal --}}
            <div class="bg-white rounded-xl border p-6">
                <img :src="imagenActiva" class="w-full h-96 object-contain">
            </div>

            {{-- miniaturas --}}
            <div class="flex gap-3 mt-4">
                @foreach ($producto->imagenes as $img)
                    <img 
                        src="{{ $img }}"
                        @click="imagenActiva = '{{ $img }}'"
                        class="w-20 h-20 object-contain border rounded cursor-pointer hover:border-rose-500"
                        onerror="this.style.display='none'"
                    >
                @endforeach
            </div>

        </div>

        {{-- INFO --}}
        <div class="space-y-4">
            <p class="text-sm text-gray-500">{{ $producto->marca }}</p>

            <h1 class="text-3xl font-bold">
                {{ $producto->modelo }}
            </h1>

            <p class="text-gray-600">
                Color: {{ $producto->color }}
            </p>

            <p class="text-3xl font-extrabold text-emerald-600">
                S/ {{ number_format((float) $producto->precioVenta, 2) }}
            </p>

            <button class="bg-rose-500 text-white px-6 py-3 rounded-lg hover:bg-rose-600">
                Agregar al carrito
            </button>
        </div>

    </div>
</main>

@endsection