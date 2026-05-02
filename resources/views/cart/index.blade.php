@extends('layouts.app')

@section('content')

{{-- Shopping Cart Page --}}
<main class="bg-gray-50 min-h-screen py-4 md:py-8">
    <div class="container mx-auto px-4 pb-24 lg:pb-8">
        
        {{-- Breadcrumb --}}
        <nav class="mb-4 md:mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-xs md:text-sm text-gray-600">
                <li>
                    <a href="/" class="hover:text-gray-900 transition-colors">Inicio</a>
                </li>
                <li>
                    <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="text-gray-900 font-medium">
                    orden
                </li>
            </ol>
        </nav>

        {{-- Page Title --}}
        <div class="mb-4 md:mb-8">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900">Su Orden de compra</h1>
            <p class="mt-1 md:mt-2 text-sm md:text-base text-gray-600">
                @if($cart['item_count'] > 0)
                    Tienes {{ $cart['item_count'] }} {{ $cart['item_count'] == 1 ? 'producto' : 'productos' }} en tu carrito
                @else
                    Tu carrito está vacío
                @endif
            </p>
        </div>

        @if($cart['item_count'] > 0)
            {{-- Cart Content --}}
            <div class="grid lg:grid-cols-3 gap-4 lg:gap-8" 
                x-data="cartManager({{ json_encode($cart) }})"
                x-init="init()">
                
                {{-- LEFT: Product List (2/3 width) --}}
                <div class="lg:col-span-2 space-y-3 md:space-y-4">
                    
                    {{-- Cart Items --}}
                    <template x-for="item in items" :key="item.id">
                        <div class="bg-white rounded-lg md:rounded-xl border border-gray-200 p-3 md:p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 md:gap-6">
                                
                                {{-- Product Image --}}
                                <div class="flex-shrink-0">
                                    <div class="w-full h-40 sm:w-20 sm:h-20 md:w-32 md:h-32 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                        <img 
                                            :src="item.image" 
                                            :alt="item.name"
                                            class="w-full h-full object-contain p-2"
                                            onerror="this.src='https://placehold.co/200x200/f9fafb/9ca3af?text=Sin+imagen'"
                                        >
                                    </div>
                                </div>

                                {{-- Product Details --}}
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start mb-2 md:mb-3">
                                        <div class="flex-grow pr-2">
                                            <h3 class="text-base md:text-lg font-bold text-gray-900 line-clamp-2" x-text="item.name"></h3>
                                            <p class="text-xs md:text-sm text-gray-500 mt-1">
                                                Precio unitario: <span class="font-semibold">S/ <span x-text="item.price.toFixed(2)"></span></span>
                                            </p>
                                        </div>
                                        
                                        {{-- Remove Button --}}
                                        <button 
                                            @click="removeItem(item.id)"
                                            class="text-red-600 hover:text-red-700 p-1.5 md:p-2 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0"
                                            title="Eliminar producto"
                                        >
                                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Quantity Selector & Subtotal --}}
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mt-3 md:mt-4">
                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto">
                                            <span class="text-xs md:text-sm text-gray-600 font-medium">Cantidad:</span>
                                            <div class="flex items-center gap-0 border border-gray-300 rounded-lg">
                                                <button 
                                                    @click="updateQuantity(item.id, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1"
                                                    class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center text-gray-700 hover:bg-gray-100 rounded-l-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                
                                                <input 
                                                    type="number" 
                                                    :value="item.quantity"
                                                    @change="updateQuantity(item.id, parseInt($event.target.value) || 1)"
                                                    min="1"
                                                    max="99"
                                                    class="w-10 md:w-12 text-center text-xs md:text-sm font-bold border-0 focus:ring-0 outline-none"
                                                >
                                                
                                                <button 
                                                    @click="updateQuantity(item.id, item.quantity + 1)"
                                                    :disabled="item.quantity >= 99"
                                                    class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center text-gray-700 hover:bg-gray-100 rounded-r-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Subtotal --}}
                                        <div class="text-left sm:text-right w-full sm:w-auto">
                                            <p class="text-xs md:text-sm text-gray-500">Subtotal</p>
                                            <p class="text-lg md:text-xl font-bold text-gray-900">
                                                S/ <span x-text="(item.price * item.quantity).toFixed(2)"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Continue Shopping Button --}}
                    <div class="pt-3 md:pt-4 mb-4 lg:mb-0">
                        <a 
                            href="/"
                            class="inline-flex items-center text-sm md:text-base text-gray-700 hover:text-gray-900 font-semibold transition-colors group"
                        >
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Continuar comprando
                        </a>
                    </div>
                </div>

                {{-- RIGHT: Order Summary (1/3 width, sticky on desktop, fixed bottom on mobile) --}}
                <div class="lg:col-span-1">
                    {{-- Desktop Summary (hidden on mobile) --}}
                    <div class="hidden lg:block bg-white rounded-xl border border-gray-200 p-6 shadow-sm sticky top-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Resumen del Pedido</h2>

                        {{-- Summary Details --}}
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal (<span x-text="totalItems"></span> productos)</span>
                                <span class="font-semibold text-gray-900">S/ <span x-text="subtotal.toFixed(2)"></span></span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Envío</span>
                                <span class="font-semibold text-green-600">GRATIS</span>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-2xl font-extrabold text-gray-900">S/ <span x-text="subtotal.toFixed(2)"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Checkout Button --}}
                        <button 
                            type="button"
                            class="w-full bg-gray-900 text-white text-base font-bold py-4 px-6 rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Proceder al Pago
                        </button>

                        {{-- Trust Badges --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span>Compra 100% segura</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                <span>Envío gratis en todos los pedidos</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Devolución gratis 30 días</span>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Summary (fixed bottom bar) --}}
                    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 p-4 shadow-2xl z-40">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-grow">
                                <p class="text-xs text-gray-500">Total (<span x-text="totalItems"></span> productos)</p>
                                <p class="text-xl font-extrabold text-gray-900">S/ <span x-text="subtotal.toFixed(2)"></span></p>
                            </div>
                            <button 
                                type="button"
                                class="bg-gray-900 text-white text-sm font-bold py-3 px-6 rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-lg active:scale-95 flex items-center gap-2 flex-shrink-0"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Proceder al Pago
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- Empty Cart State --}}
            <div class="max-w-md mx-auto text-center py-8 md:py-16">
                <div class="bg-white rounded-xl md:rounded-2xl border-2 border-dashed border-gray-300 p-8 md:p-12">
                    {{-- Empty Cart Icon --}}
                    <div class="mb-4 md:mb-6">
                        <svg class="w-16 h-16 md:w-24 md:h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>

                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 md:mb-3">Tu carrito está vacío</h2>
                    <p class="text-sm md:text-base text-gray-600 mb-6 md:mb-8">¡Empieza a agregar productos increíbles!</p>

                    <a 
                        href="/"
                        class="inline-flex items-center bg-gray-900 text-white text-sm md:text-base font-bold px-6 md:px-8 py-2.5 md:py-3 rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95"
                    >
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Explorar Productos
                    </a>
                </div>
            </div>
        @endif

    </div>
</main>

{{-- Alpine.js Cart Manager --}}
<script>
function cartManager(initialCart) {
    return {
        items: initialCart.items || [],
        totalItems: initialCart.total_count || 0,
        subtotal: initialCart.subtotal || 0,
        
        init() {
            // Listen for cart updates from other pages
            window.addEventListener('cart-updated', (e) => {
                this.updateFromEvent(e.detail);
            });
        },

        updateQuantity(productId, newQuantity) {
            if (newQuantity < 1 || newQuantity > 99) return;

            fetch(`/cart/update/${productId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.updateFromCart(data.cart);
                    this.dispatchUpdate(data.cart);
                }
            })
            .catch(err => {
                console.error('Error updating quantity:', err);
                alert('Error al actualizar la cantidad');
            });
        },

        removeItem(productId) {
            if (!confirm('¿Estás seguro de eliminar este producto?')) return;

            fetch(`/cart/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.updateFromCart(data.cart);
                    this.dispatchUpdate(data.cart);
                    
                    // Redirect if cart is empty
                    if (data.cart.item_count === 0) {
                        window.location.reload();
                    }
                }
            })
            .catch(err => {
                console.error('Error removing item:', err);
                alert('Error al eliminar el producto');
            });
        },

        updateFromCart(cart) {
            this.items = cart.items || [];
            this.totalItems = cart.total_count || 0;
            this.subtotal = cart.subtotal || 0;
        },

        updateFromEvent(cart) {
            this.updateFromCart(cart);
        },

        dispatchUpdate(cart) {
            window.dispatchEvent(new CustomEvent('cart-updated', { 
                detail: cart 
            }));
        }
    }
}
</script>

@endsection
