@extends('layouts.app')

@section('content')

{{-- Product Detail Page - Shopify-style Layout --}}
<main class="bg-white">
    <div class="container mx-auto px-4 py-8 lg:py-12">
        
        {{-- Breadcrumb Navigation --}}
        <nav class="mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li>
                    <a href="/" class="hover:text-gray-900 transition-colors">Inicio</a>
                </li>
                <li>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li>
                    <span class="text-gray-400">Lentes</span>
                </li>
                <li>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li>
                    <span class="text-gray-400">{{ $producto->familia }}</span>
                </li>
                <li>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="text-gray-900 font-medium truncate max-w-[200px]">
                    {{ $producto->modelo }}
                </li>
            </ol>
        </nav>

        {{-- Main Product Layout: Images (60%) + Info (40%) --}}
        <div class="grid lg:grid-cols-5 gap-8 lg:gap-12" x-data="{ 
            qty: 1,
            selectedImage: '{{ $producto->imagenes[0] }}',
            activeIndex: 0
        }">
            
            {{-- LEFT SIDE: Image Gallery Grid (3 columns on desktop, 60% width) --}}
            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($producto->imagenes as $index => $img)
                        <div 
                            class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border-2 transition-all duration-300 cursor-pointer overflow-hidden"
                            :class="activeIndex === {{ $index }} ? 'border-gray-900 ring-2 ring-gray-900 ring-offset-2' : 'border-gray-200 hover:border-gray-400'"
                            @click="selectedImage = '{{ $img }}'; activeIndex = {{ $index }}"
                        >
                            <img 
                                src="{{ $img }}"
                                alt="{{ $producto->modelo }} - Vista {{ $index + 1 }}"
                                class="w-full h-full object-contain p-6 transition-transform duration-300 hover:scale-105"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                onerror="this.parentElement.style.display='none'"
                            >
                            
                            {{-- Selected Indicator --}}
                            <div 
                                class="absolute top-3 right-3 w-6 h-6 rounded-full bg-gray-900 flex items-center justify-center transition-opacity duration-200"
                                :class="activeIndex === {{ $index }} ? 'opacity-100' : 'opacity-0'"
                            >
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Mobile: Large Selected Image Preview --}}
                <div class="mt-6 lg:hidden">
                    <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-300 overflow-hidden">
                        <img 
                            :src="selectedImage"
                            alt="{{ $producto->modelo }}"
                            class="w-full h-full object-contain p-8"
                        >
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: Product Info (Sticky, 40% width) --}}
            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-8 space-y-6">
                    
                    {{-- Brand --}}
                    <div>
                        <p class="text-sm uppercase tracking-wider text-gray-500 font-bold">{{ $producto->marca }}</p>
                    </div>

                    {{-- Model Name --}}
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                        {{ $producto->modelo }}
                    </h1>

                    {{-- Color Badge --}}
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium">
                            <span class="w-3 h-3 rounded-full bg-gray-400 mr-2 border-2 border-gray-500"></span>
                            Color: {{ $producto->color }}
                        </span>
                    </div>

                    {{-- Price Display with Discount --}}
                    <div class="pt-4 pb-6 border-t border-b border-gray-200">
                        @if($producto->precioMinimo > $producto->precioVenta)
                            {{-- Show discount pricing --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl text-gray-400 line-through font-medium">
                                        S/ {{ number_format((float)$producto->precioMinimo, 2) }}
                                    </span>
                                    <span class="inline-flex items-center bg-red-100 text-red-700 text-sm font-bold px-3 py-1 rounded-full">
                                        -{{ round((($producto->precioMinimo - $producto->precioVenta) / $producto->precioMinimo) * 100) }}% OFF
                                    </span>
                                </div>
                                <p class="text-4xl md:text-5xl font-extrabold text-red-600">
                                    S/ {{ number_format((float)$producto->precioVenta, 2) }}
                                </p>
                                <p class="text-sm text-green-700 font-medium">
                                    ¡Ahorras S/ {{ number_format((float)($producto->precioMinimo - $producto->precioVenta), 2) }}!
                                </p>
                            </div>
                        @else
                            {{-- Regular pricing --}}
                            <p class="text-4xl md:text-5xl font-extrabold text-gray-900">
                                S/ {{ number_format((float)$producto->precioVenta, 2) }}
                            </p>
                        @endif
                    </div>

                    {{-- Stock Status --}}
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-sm font-medium text-green-700">En stock y listo para envío</span>
                    </div>

                    {{-- Quantity Selector --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">Cantidad</label>
                        <div class="flex items-center gap-3">
                            <button 
                                type="button"
                                @click="if(qty > 1) qty--"
                                class="w-12 h-12 flex items-center justify-center rounded-lg border-2 border-gray-300 hover:border-gray-900 text-gray-700 hover:text-gray-900 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="qty <= 1"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            
                            <input 
                                type="number" 
                                x-model="qty"
                                min="1"
                                max="10"
                                class="w-20 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 outline-none transition-all"
                            >
                            
                            <button 
                                type="button"
                                @click="if(qty < 10) qty++"
                                class="w-12 h-12 flex items-center justify-center rounded-lg border-2 border-gray-300 hover:border-gray-900 text-gray-700 hover:text-gray-900 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="qty >= 10"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Add to Cart Button --}}
                    <button 
                        type="button"
                        class="w-full bg-gray-900 text-white text-base font-bold py-4 px-8 rounded-lg hover:bg-gray-800 transition-all duration-200 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl active:scale-95"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Agregar al carrito
                    </button>

                    {{-- Additional Product Info --}}
                    <div class="pt-6 border-t border-gray-200 space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 font-medium mb-1">SKU</p>
                                <p class="text-gray-900 font-semibold">{{ $producto->codigo }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium mb-1">Categoría</p>
                                <p class="text-gray-900 font-semibold">{{ $producto->familia }}</p>
                            </div>
                        </div>
                        
                        @if($producto->descripcion)
                        <div>
                            <p class="text-gray-500 font-medium mb-1 text-sm">Diseño</p>
                            <p class="text-gray-900">{{ $producto->descripcion }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Trust Badges --}}
                    <div class="pt-6 border-t border-gray-200 grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center bg-gray-100 rounded-full">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-900">Envío gratis</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center bg-gray-100 rounded-full">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-900">Garantía 1 año</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center bg-gray-100 rounded-full">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-900">Devolución 30 días</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

@endsection
