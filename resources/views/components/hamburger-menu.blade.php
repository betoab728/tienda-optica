<div x-data="{ open: false }" class="relative z-50">

    <!-- Botón -->
    <button 
        @click="open = true" 
        class="text-red-500 text-2xl lg:hidden"
    >
        ☰
    </button>

    <!-- Overlay -->
    <div 
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed inset-0 bg-black/50"
    ></div>

    <!-- Menú -->
    <div 
        x-show="open"
        x-transition:enter="transition transform duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 w-80 h-full bg-gray-100 p-4"
    >

        <button 
            @click="open = false" 
            class="text-red-600 text-2xl mb-4"
        >
            ×
        </button>

        <ul class="space-y-3">
            <li><a href="#" class="text-red-600">Lentes de Sol</a></li>
            <li><a href="#" class="text-red-600">Lentes Oftálmicos</a></li>
            <li><a href="#" class="text-red-600">Lentes de Contacto</a></li>
            <li><a href="#" class="text-red-600">Promociones</a></li>
        </ul>

    </div>

</div>