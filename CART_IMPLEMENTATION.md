# 🛒 Shopping Cart Implementation Guide

## Overview
Session-based shopping cart system for tienda-optica using Laravel backend and Alpine.js frontend.

---

## 📁 Files Created/Modified

### Backend Files Created:
1. **`app/Services/CartService.php`** - Cart business logic
2. **`app/Http/Controllers/CartController.php`** - API endpoints

### Frontend Files Modified:
1. **`resources/views/catalogo.blade.php`** - Added quick add button
2. **`resources/views/producto/detalle.blade.php`** - Added full add to cart
3. **`resources/views/components/header.blade.php`** - Added cart count badge
4. **`routes/web.php`** - Added cart routes

---

## 🔌 API Endpoints

### Base URL: `/cart`

| Method | Endpoint | Description | Request Body |
|--------|----------|-------------|--------------|
| GET | `/cart` | Get cart summary | - |
| POST | `/cart/add` | Add item to cart | `{ product_id, name, price, image, quantity }` |
| PUT | `/cart/update/{id}` | Update quantity | `{ quantity }` |
| DELETE | `/cart/remove/{id}` | Remove item | - |
| DELETE | `/cart/clear` | Clear cart | - |
| GET | `/cart/count` | Get total count | - |

---

## 📦 Cart Service Methods

### `CartService::class`

```php
// Add item (or update quantity if exists)
$cart = $cartService->addItem(
    productId: 1,
    name: 'Ray-Ban Aviator',
    price: 299.90,
    image: 'https://cdn.example.com/1.jpg',
    quantity: 2
);

// Update quantity
$cart = $cartService->updateQuantity(productId: 1, quantity: 3);

// Remove item
$cart = $cartService->removeItem(productId: 1);

// Get cart summary
$summary = $cartService->getCartSummary();
// Returns: ['items' => [...], 'total_count' => 5, 'subtotal' => 599.80, 'item_count' => 3]

// Get total count only
$count = $cartService->getTotalCount(); // Returns: 5

// Clear cart
$cartService->clearCart();
```

---

## 🎨 Frontend Integration (Alpine.js)

### 1. Add to Cart from Catalog (Quick Add)

The catalog page includes a hover overlay with "Quick Add" button:

```javascript
// On catalog hover, show overlay with:
fetch('/cart/add', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
        product_id: 123,
        name: 'Product Name',
        price: 149.90,
        image: 'https://cdn.example.com/123.jpg',
        quantity: 1
    })
})
.then(res => res.json())
.then(data => {
    // Dispatch event to update header count
    window.dispatchEvent(new CustomEvent('cart-updated', { 
        detail: data.cart 
    }));
    alert('✓ Producto agregado al carrito');
});
```

### 2. Add to Cart from Detail Page (with Quantity)

The product detail page includes quantity selector:

```javascript
// Alpine.js data:
x-data="{ 
    qty: 1,
    adding: false
}"

// On button click:
fetch('/cart/add', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
        product_id: 123,
        name: 'Product Name',
        price: 149.90,
        image: 'https://cdn.example.com/123.jpg',
        quantity: qty  // Uses selected quantity
    })
})
.then(res => res.json())
.then(data => {
    window.dispatchEvent(new CustomEvent('cart-updated', { 
        detail: data.cart 
    }));
    alert('✓ ' + qty + ' producto(s) agregado(s) al carrito');
    qty = 1; // Reset
});
```

### 3. Header Cart Count Badge

The header automatically updates when cart changes:

```javascript
// Header Alpine.js data:
x-data="{ 
    cartCount: 0,
    init() {
        // Load initial count
        this.updateCartCount();
        
        // Listen for updates
        window.addEventListener('cart-updated', (e) => {
            this.cartCount = e.detail.total_count;
        });
    },
    updateCartCount() {
        fetch('/cart/count')
            .then(res => res.json())
            .then(data => this.cartCount = data.count);
    }
}"

// Badge HTML:
<span 
    x-show="cartCount > 0"
    x-text="cartCount"
    class="badge"
></span>
```

---

## 🔄 Cart Event System

### Custom Event: `cart-updated`

Dispatched whenever cart changes (add/update/remove):

```javascript
// Dispatch event:
window.dispatchEvent(new CustomEvent('cart-updated', { 
    detail: {
        items: [...],
        total_count: 5,
        subtotal: 599.80,
        item_count: 3
    }
}));

// Listen for event:
window.addEventListener('cart-updated', (e) => {
    console.log('Cart updated:', e.detail);
    // Update UI components
});
```

---

## 📊 Response Format

### Success Response:

```json
{
    "success": true,
    "message": "Producto agregado al carrito",
    "cart": {
        "items": [
            {
                "id": 123,
                "name": "Ray-Ban Aviator",
                "price": 299.90,
                "image": "https://cdn.example.com/123.jpg",
                "quantity": 2
            }
        ],
        "total_count": 2,
        "subtotal": 599.80,
        "item_count": 1
    }
}
```

### Error Response:

```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "product_id": ["The product id field is required."]
    }
}
```

---

## 🧪 Testing

### Test Cart Service:

```bash
php artisan tinker
```

```php
$cart = app(\App\Services\CartService::class);

// Add item
$cart->addItem(1, 'Test Product', 99.99, 'test.jpg', 2);

// Get summary
$cart->getCartSummary();

// Get count
$cart->getTotalCount();

// Clear
$cart->clearCart();
```

### Test API Endpoints:

```bash
# Add to cart
curl -X POST http://localhost:8000/cart/add \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "product_id": 1,
    "name": "Test Product",
    "price": 99.99,
    "image": "test.jpg",
    "quantity": 2
  }'

# Get cart
curl http://localhost:8000/cart

# Get count
curl http://localhost:8000/cart/count
```

---

## 🎯 Features Implemented

✅ **Add to cart** with automatic quantity update if exists  
✅ **Update quantity** (set to 0 to remove)  
✅ **Remove item** from cart  
✅ **Clear entire cart**  
✅ **Get cart summary** (items, count, subtotal)  
✅ **Session-based storage** (no database required)  
✅ **Real-time cart count** in header with badge  
✅ **Alpine.js integration** with loading states  
✅ **Quick add from catalog** (hover overlay)  
✅ **Full add from detail** (with quantity selector)  
✅ **Custom events** for reactive UI updates  
✅ **JSON API responses** for AJAX  
✅ **CSRF protection** included  
✅ **Validation** on all inputs  

---

## 🚀 Next Steps (Optional Enhancements)

1. **Create cart page** (`/cart`) to view/edit items
2. **Add toast notifications** instead of alerts
3. **Implement checkout flow**
4. **Add product stock validation**
5. **Persist cart to database** for logged users
6. **Add cart mini-modal** (slide-in preview)
7. **Implement wishlist** similar pattern
8. **Add product variants** (size, color options)

---

## 📝 Notes

- Cart data stored in Laravel session (`shopping_cart` key)
- Session lifetime: 120 minutes (configurable in `config/session.php`)
- Cart persists across page reloads
- No authentication required (guest cart)
- Can be migrated to database later without frontend changes

---

## 🐛 Troubleshooting

**Cart count not updating?**
- Clear browser cache
- Check browser console for JavaScript errors
- Verify Alpine.js is loaded (CDN in layout)

**CSRF token error?**
- Check `{{ csrf_token() }}` is in fetch headers
- Clear session: `php artisan session:flush`

**Session not persisting?**
- Check `SESSION_DRIVER=database` in `.env`
- Run migrations: `php artisan migrate`
- Check `storage/framework/sessions` permissions

---

## 📚 Documentation

- Laravel Sessions: https://laravel.com/docs/session
- Alpine.js: https://alpinejs.dev/
- Fetch API: https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API

---

**Implementation Date**: 2026-05-01  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
