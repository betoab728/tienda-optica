<section class="mt-10">
    <div class="flex items-end justify-between mb-6">
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Productos destacados</h2>
        <a href="#" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Ver todo</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($productos as $p)
            <article class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="relative bg-gradient-to-b from-white to-gray-50">
                    <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-rose-100 text-rose-600 text-[11px] font-semibold px-2.5 py-1">
                        {{ $p->familia }}
                    </span>

                    <img
                        src="{{ $p->imagen }}"
                        alt="{{ $p->modelo }}"
                        class="h-64 w-full object-contain p-4"
                        loading="lazy"
                        onerror="this.src='https://placehold.co/600x600/f3f4f6/9ca3af?text=Sin+imagen';"
                    >
                </div>

                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">{{ $p->marca }}</p>
                        <h3 class="text-base font-bold text-gray-900 leading-snug">{{ $p->modelo }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Color: {{ $p->color }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Precio online</p>
                            <p class="text-2xl font-extrabold text-emerald-600">S/ {{ number_format((float) $p->precioVenta, 2) }}</p>
                        </div>
                        <button type="button" class="rounded-lg bg-rose-500 text-white text-sm font-semibold px-4 py-2 hover:bg-rose-600 transition-colors">
                            Agregar
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>