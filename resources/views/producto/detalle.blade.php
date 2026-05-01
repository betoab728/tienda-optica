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
            
            {{-- LEFT SIDE: Imagen principal + thumbnails --}}
            <div class="lg:col-span-3" 
                x-data="{ 
                    selectedImage: '{{ $producto->imagenes[0] }}',
                    activeIndex: 0
                }">

                {{-- Imagen principal --}}
                <div class="relative group bg-gradient-to-br from-gray-50 to-white rounded-2xl border-2 border-gray-200 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden mb-6">
                    {{-- Image Counter Badge --}}
                    <div class="absolute top-4 right-4 z-10 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full">
                        <span x-text="activeIndex + 1"></span> / {{ count($producto->imagenes) }}
                    </div>

                    {{-- Zoom Hint (appears on hover) --}}
                    <div class="absolute top-4 left-4 z-10 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                        Click para ampliar
                    </div>

                    {{-- Main Product Image --}}
                    <div class="p-8 md:p-12">
                        <img 
                            :src="selectedImage"
                            alt="{{ $producto->modelo }}"
                            class="w-full h-[400px] md:h-[500px] object-contain transition-transform duration-500 group-hover:scale-105 cursor-zoom-in"
                        >
                    </div>
                </div>

                {{-- Thumbnails Gallery --}}
                <div class="relative">
                    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        @foreach ($producto->imagenes as $index => $img)
                            <div 
                                @click="selectedImage = '{{ $img }}'; activeIndex = {{ $index }}"
                                class="relative flex-shrink-0 w-24 h-24 bg-white rounded-xl border-2 cursor-pointer transition-all duration-300 overflow-hidden group/thumb"
                                :class="activeIndex === {{ $index }} 
                                    ? 'border-gray-900 ring-4 ring-gray-900 ring-offset-2 shadow-xl scale-105' 
                                    : 'border-gray-200 hover:border-gray-400 hover:shadow-lg hover:scale-105'"
                            >
                                {{-- Thumbnail Image --}}
                                <img 
                                    src="{{ $img }}"
                                    alt="{{ $producto->modelo }} - Vista {{ $index + 1 }}"
                                    class="w-full h-full object-contain p-2 transition-transform duration-300 group-hover/thumb:scale-110"
                                    onerror="this.parentElement.style.display='none'"
                                >

                                {{-- Active Indicator Overlay --}}
                                <div 
                                    class="absolute inset-0 bg-gray-900/10 transition-opacity duration-200"
                                    :class="activeIndex === {{ $index }} ? 'opacity-0' : 'opacity-0 group-hover/thumb:opacity-100'"
                                ></div>

                                {{-- Checkmark for Active Thumbnail --}}
                                <div 
                                    class="absolute top-1.5 right-1.5 w-5 h-5 bg-gray-900 rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="activeIndex === {{ $index }} ? 'opacity-100 scale-100' : 'opacity-0 scale-0'"
                                >
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Gradient Fade Effect (for overflow indication) --}}
                    <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
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
