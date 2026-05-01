{{-- Modern Product Catalog Grid - Refactored for premium ecommerce UX --}}
<section class="mt-12 mb-16">
    {{-- Section Header --}}
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Productos destacados</h2>
            <p class="mt-2 text-sm text-gray-600">Descubre nuestra selección de lentes exclusivos</p>
        </div>
        <a href="#" class="hidden sm:inline-flex items-center text-sm font-semibold text-gray-900 hover:text-gray-700 transition-colors group">
            Ver todo
            <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
        @foreach ($productos as $p)
            <article class="group bg-white rounded-lg overflow-hidden border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-xl">
                <a href="{{ route('producto.detalle', $p->idproducto) }}" class="block">
                    
                    {{-- Image Container with Overlay --}}
                    <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
                        
                        {{-- Product Image --}}
                        <img
                            src="{{ $p->imagen }}"
                            alt="{{ $p->marca }} {{ $p->modelo }}"
                            class="w-full h-full object-contain p-6 transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                            onerror="this.src='https://placehold.co/600x600/f9fafb/9ca3af?text=Sin+imagen';"
                        >
                        
                        {{-- Familia Badge (Top Left) --}}
                        <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm text-gray-700 text-[10px] uppercase font-bold tracking-wider px-3 py-1.5 shadow-sm">
                            {{ $p->familia }}
                        </span>

                        {{-- Discount Badge (Top Right) - Only if discount exists --}}
                        @if($p->precioMinimo > $p->precioVenta)
                            <span class="absolute top-3 right-3 inline-flex items-center rounded-full bg-red-600 text-white text-xs font-bold px-2.5 py-1 shadow-md">
                                -{{ round((($p->precioMinimo - $p->precioVenta) / $p->precioMinimo) * 100) }}%
                            </span>
                        @endif
                        
                        {{-- Hover Overlay with "Ver producto" --}}
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="text-center transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                <span class="inline-flex items-center text-white text-base font-semibold px-6 py-2.5 border-2 border-white rounded-lg hover:bg-white hover:text-gray-900 transition-colors">
                                    Ver producto
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Product Info --}}
                    <div class="p-5 space-y-3">
                        
                        {{-- Brand Name --}}
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">{{ $p->marca }}</p>
                        
                        {{-- Model Name --}}
                        <h3 class="text-base font-bold text-gray-900 leading-tight line-clamp-2 group-hover:text-gray-700 transition-colors">
                            {{ $p->modelo }}
                        </h3>
                        
                        {{-- Color --}}
                        <p class="text-sm text-gray-600 flex items-center">
                            <span class="w-3 h-3 rounded-full bg-gray-300 mr-2 border border-gray-400"></span>
                            {{ $p->color }}
                        </p>
                        
                        {{-- Price Display with Discount Logic --}}
                        <div class="pt-2 border-t border-gray-100">
                            @if($p->precioMinimo > $p->precioVenta)
                                {{-- Show discount pricing --}}
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 line-through text-sm font-medium">
                                            S/ {{ number_format((float)$p->precioMinimo, 2) }}
                                        </span>
                                        <span class="text-xs bg-red-50 text-red-700 font-bold px-2 py-0.5 rounded">
                                            Ahorra S/ {{ number_format((float)($p->precioMinimo - $p->precioVenta), 2) }}
                                        </span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-red-600">
                                        S/ {{ number_format((float)$p->precioVenta, 2) }}
                                    </p>
                                </div>
                            @else
                                {{-- Regular pricing --}}
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Precio online</p>
                                    <p class="text-2xl font-extrabold text-gray-900">
                                        S/ {{ number_format((float)$p->precioVenta, 2) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </article>
        @endforeach
    </div>

    {{-- View All Link (Mobile) --}}
    <div class="mt-8 text-center sm:hidden">
        <a href="#" class="inline-flex items-center text-sm font-semibold text-gray-900 hover:text-gray-700 transition-colors">
            Ver todos los productos
            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>
