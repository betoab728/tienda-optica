<header class="flex items-center justify-between gap-4 px-4 py-3 shadow-md bg-white" 
    x-data="{ 
        cartCount: 0,
        init() {
            // Load initial cart count
            this.updateCartCount();
            // Listen for cart updates
            window.addEventListener('cart-updated', (e) => {
                this.cartCount = e.detail.total_count;
            });
        },
        updateCartCount() {
            fetch('{{ route('cart.count') }}')
                .then(res => res.json())
                .then(data => this.cartCount = data.count)
                .catch(err => console.error('Error loading cart count:', err));
        }
    }">

    <!-- Hamburger -->
    <x-hamburger-menu />

    <!-- Logo -->
    <div class="px-2 shrink-0">
        <img src="/img/logo.png" class="w-36 md:w-44 h-auto" alt="Optica Prisma">
    </div>

    <!-- Buscador -->
    <div class="hidden md:flex flex-grow mx-4">
        <div class="relative w-full">
            <input
                type="text"
                placeholder="Buscar productos"
                class="w-full p-2 pl-10 border border-gray-300 rounded-md focus:outline-none"
            />

            <!-- Icono SVG -->
            <svg xmlns="http://www.w3.org/2000/svg"
                class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex items-center space-x-4">

        <div class="hidden lg:flex flex-col items-center text-red-600">
            <img src="/img/calendar.svg" class="w-7 h-7" alt="Agenda tu cita">
            <span class="text-xs">Agenda tu cita</span>
        </div>

        <div class="hidden lg:flex flex-col items-center text-red-600">
            <img src="/img/user.svg" class="w-7 h-7" alt="Iniciar sesion">
            <span class="text-xs">Iniciar Sesión</span>
        </div>

        <div class="hidden lg:flex flex-col items-center text-red-600">
            <img src="/img/tracking.svg" class="w-7 h-7" alt="Seguimiento">
            <span class="text-xs">Sigue tu compra</span>
        </div>

        <a href="{{ route('cart.show') }}" class="relative flex flex-col items-center text-red-600 hover:text-red-700 transition-colors">
            <div class="relative">
                <img src="/img/cart.svg" class="w-7 h-7" alt="Carrito">
                {{-- Cart Count Badge --}}
                <span 
                    x-show="cartCount > 0"
                    x-text="cartCount"
                    class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-sm"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-75"
                    x-transition:enter-end="opacity-100 scale-100"
                ></span>
            </div>
            <span class="text-xs">Carrito</span>
        </a>

    </div>

</header>