<div
    x-data="{ open: true }"
    x-show="open"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto"
>

    <div
        @click.away="open = false"
        class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto"
    >

        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-600 to-red-500 text-white px-6 md:px-8 py-10 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Bienvenido a Óptica Prisma
            </h2>

            <p class="text-white/90 text-base md:text-lg leading-relaxed max-w-xl mx-auto">
                Elige la experiencia que mejor se adapte a tu compra.
            </p>

        </div>

        {{-- Opciones --}}
        <div class="p-6 md:p-8">

            <div class="grid md:grid-cols-2 gap-6">

                {{-- Tengo receta --}}
                <button
                    @click="open = false; window.dispatchEvent(new CustomEvent('open-prescription'))"
                    class="group border-2 border-red-100 hover:border-red-500 bg-red-50/40 rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 text-left w-full cursor-pointer"
                >

                    <div class="text-5xl mb-5">
                        📄
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        Tengo una receta
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        Recibe recomendaciones personalizadas con nuestro asesor virtual
                        y encuentra la mejor opción para tu visión.
                    </p>

                    <div class="mt-6 text-red-600 font-semibold flex items-center gap-2 group-hover:translate-x-1 transition-transform">
                        Iniciar asesoría
                        <span>→</span>
                    </div>

                </button>

                {{-- Compra libre --}}
                <a
                    href="#catalogo"
                    @click="open = false"
                    class="group border-2 border-gray-200 hover:border-gray-900 rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                >

                    <div class="text-5xl mb-5">
                        🛍️
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        Comprar sin receta
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        Explora libremente nuestras monturas,
                        lentes de sol y accesorios.
                    </p>

                    <div class="mt-6 text-gray-900 font-semibold flex items-center gap-2 group-hover:translate-x-1 transition-transform">
                        Ver catálogo
                        <span>→</span>
                    </div>

                </a>

            </div>

            {{-- Footer --}}
            <div class="mt-10 text-center">

                <button
                    @click="open = false"
                    class="text-sm text-gray-500 hover:text-gray-700 transition-colors"
                >
                    Continuar explorando
                </button>

            </div>

        </div>

    </div>

</div>
