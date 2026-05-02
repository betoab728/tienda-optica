# 🛒 Shopping Cart Page Documentation

## Overview
Complete shopping cart (checkout preview) page implementation for viewing and managing cart items before proceeding to payment.

---

## 📋 Features Implemented

### ✅ Cart Page (`/carrito`)

**URL**: `http://localhost:8000/carrito`  
**Route Name**: `cart.show`  
**Controller**: `CartController@show`  
**View**: `resources/views/cart/index.blade.php`

---

## 🎨 Page Structure

### Layout (Desktop)

```
┌─────────────────────────────────────────────────────────────┐
│                        BREADCRUMB                           │
│                     Carrito de Compras                      │
├──────────────────────────────────┬──────────────────────────┤
│                                  │                          │
│  PRODUCT LIST (2/3 width)        │  ORDER SUMMARY (sticky)  │
│                                  │  (1/3 width)             │
│  ┌────────────────────────────┐  │  ┌────────────────────┐  │
│  │ [Image] Product Name       │  │  │ Resumen del Pedido │  │
│  │         Price              │  │  │                    │  │
│  │         Qty: [- 1 +]  S/99 │  │  │ Subtotal: S/ 299   │  │
│  │         [Remove]           │  │  │ Envío: GRATIS      │  │
│  └────────────────────────────┘  │  │ Total: S/ 299      │  │
│                                  │  │                    │  │
│  ┌────────────────────────────┐  │  │ [Proceder al Pago] │  │
│  │ [Image] Product Name       │  │  │                    │  │
│  │         Price              │  │  │ ✓ Compra segura    │  │
│  │         Qty: [- 2 +]  S/198│  │  │ ✓ Envío gratis     │  │
│  │         [Remove]           │  │  │ ✓ Devolución 30d   │  │
│  └────────────────────────────┘  │  └────────────────────┘  │
│                                  │                          │
│  [← Continuar comprando]         │                          │
└──────────────────────────────────┴──────────────────────────┘
```

### Mobile Layout

- **Stacks vertically**: Product list → Order summary
- **Full width cards**: Each product in its own card
- **Responsive images**: Smaller thumbnails on mobile

---

## 🔧 Components

### 1. Product Card

Each cart item displays:

```blade
┌─────────────────────────────────┐
│ [Thumbnail]  Product Name    [X]│
│              Unit: S/ 149.90    │
│                                 │
│ Qty: [- 2 +]      Subtotal:    │
│                   S/ 299.80     │
└─────────────────────────────────┘
```

**Features**:
- Product image (with fallback)
- Product name
- Unit price
- Quantity selector (+/- buttons)
- Editable quantity input
- Item subtotal (auto-calculated)
- Remove button

### 2. Quantity Selector

```html
[−] [ 2 ] [+]
```

**Features**:
- Decrease button (disabled at qty 1)
- Number input (min: 1, max: 99)
- Increase button (disabled at qty 99)
- Real-time AJAX updates
- Auto-recalculates totals

### 3. Order Summary (Sticky Sidebar)

```
┌─────────────────────────┐
│ Resumen del Pedido      │
├─────────────────────────┤
│ Subtotal (3 productos)  │
│                S/ 449.70│
│                         │
│ Envío          GRATIS   │
├─────────────────────────┤
│ Total          S/ 449.70│
├─────────────────────────┤
│ [Proceder al Pago]      │
├─────────────────────────┤
│ ✓ Compra 100% segura    │
│ ✓ Envío gratis          │
│ ✓ Devolución 30 días    │
└─────────────────────────┘
```

**Features**:
- Sticky positioning (stays visible on scroll)
- Real-time total updates
- Item count display
- Free shipping badge
- Trust badges
- Checkout button (placeholder)

### 4. Empty Cart State

```
┌─────────────────────────┐
│        🛒               │
│                         │
│ Tu carrito está vacío   │
│                         │
│ [🔍 Explorar Productos] │
└─────────────────────────┘
```

Displays when `$cart['item_count'] === 0`

---

## 💻 Alpine.js Integration

### Cart Manager Component

```javascript
x-data="cartManager({{ json_encode($cart) }})"
```

**State**:
- `items` - Array of cart items
- `totalItems` - Total quantity count
- `subtotal` - Total price

**Methods**:

#### `updateQuantity(productId, newQuantity)`
```javascript
// Updates item quantity via AJAX
updateQuantity(123, 5)
```

**Flow**:
1. Validates quantity (1-99)
2. Sends PUT request to `/cart/update/{id}`
3. Updates local state
4. Dispatches `cart-updated` event
5. Recalculates totals

#### `removeItem(productId)`
```javascript
// Removes item from cart
removeItem(123)
```

**Flow**:
1. Shows confirmation dialog
2. Sends DELETE request to `/cart/remove/{id}`
3. Updates local state
4. Reloads page if cart becomes empty
5. Dispatches `cart-updated` event

---

## 🔌 API Integration

### Update Quantity

```javascript
fetch('/cart/update/123', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ quantity: 5 })
})
```

**Response**:
```json
{
    "success": true,
    "message": "Cantidad actualizada",
    "cart": {
        "items": [...],
        "total_count": 5,
        "subtotal": 749.50,
        "item_count": 2
    }
}
```

### Remove Item

```javascript
fetch('/cart/remove/123', {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken }
})
```

**Response**:
```json
{
    "success": true,
    "message": "Producto eliminado del carrito",
    "cart": { ... }
}
```

---

## 📱 Responsive Design

### Breakpoints

| Screen | Layout | Image Size | Grid |
|--------|--------|------------|------|
| Mobile (<640px) | Stacked | 96×96px | 1 column |
| Tablet (640-1024px) | Stacked | 128×128px | 1 column |
| Desktop (>1024px) | Side-by-side | 128×128px | 2/3 + 1/3 |

### Mobile Optimizations

- Smaller product images
- Touch-friendly buttons (min 44×44px)
- Simplified quantity controls
- Full-width cards
- Sticky order summary at bottom

---

## 🎨 Design System

### Colors

```css
Background: #f9fafb (gray-50)
Cards: #ffffff (white)
Borders: #e5e7eb (gray-200)
Primary Text: #111827 (gray-900)
Secondary Text: #6b7280 (gray-600)
Success: #16a34a (green-600)
Danger: #dc2626 (red-600)
```

### Typography

```css
Page Title: 3xl (30px) / 4xl (36px) - Bold
Section Title: xl (20px) - Bold
Product Name: lg (18px) - Bold
Body: base (16px) - Regular
Small: sm (14px) / xs (12px)
```

### Spacing

```css
Card Padding: 1.5rem (24px)
Gap between items: 1rem (16px)
Section Margins: 2rem (32px)
```

---

## 🔄 State Management

### Cart Updates

When quantity changes or items removed:

1. **Local update** - Alpine.js updates component state
2. **API call** - AJAX request to Laravel backend
3. **Response handling** - Update cart from response
4. **Event dispatch** - Emit `cart-updated` event
5. **Global sync** - Header badge updates automatically

### Event Flow

```
User Action
    ↓
Alpine.js Handler
    ↓
AJAX Request (fetch)
    ↓
Laravel Controller
    ↓
CartService (session update)
    ↓
JSON Response
    ↓
Alpine.js Update
    ↓
Dispatch cart-updated Event
    ↓
Header Badge Updates
```

---

## 🧪 Testing Guide

### Manual Testing

**1. View Cart Page**
```bash
# Add item to cart first
# Then visit:
http://localhost:8000/carrito
```

**2. Test Quantity Update**
- Click `+` button → Quantity increases
- Click `-` button → Quantity decreases
- Type in input → Updates on blur/enter
- Check: Subtotal recalculates
- Check: Total updates in summary

**3. Test Remove Item**
- Click trash icon → Confirmation dialog
- Click "OK" → Item removed
- Check: Totals update
- Check: Page reloads if last item removed

**4. Test Empty Cart**
- Remove all items
- Check: Empty state displays
- Click "Explorar Productos" → Redirects to home

**5. Test Responsive**
- Resize browser window
- Check: Layout adapts
- Check: Touch targets on mobile
- Check: Sidebar stacks on mobile

---

## 📝 Controller Code

```php
// app/Http/Controllers/CartController.php

/**
 * Show cart page (HTML view)
 */
public function show()
{
    $cart = $this->cartService->getCartSummary();
    return view('cart.index', compact('cart'));
}
```

---

## 🛣️ Routes

```php
// routes/web.php

// Cart Page (HTML)
Route::get('/carrito', [CartController::class, 'show'])->name('cart.show');

// Cart API (JSON)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::put('/update/{productId}', [CartController::class, 'update']);
    Route::delete('/remove/{productId}', [CartController::class, 'remove']);
    // ... other API routes
});
```

---

## 🎯 User Flow

```
Homepage
    ↓
[Add to Cart]
    ↓
Cart Badge Updates (+1)
    ↓
Click Cart Icon
    ↓
/carrito (Cart Page)
    ↓
Review Items
    ↓
Update Quantities
    ↓
Remove Items (optional)
    ↓
[Proceder al Pago]
    ↓
(Checkout - not implemented yet)
```

---

## 🔐 Security

- ✅ **CSRF Protection** on all mutations
- ✅ **Session-based** cart (no client-side manipulation)
- ✅ **Input validation** (quantity 1-99)
- ✅ **XSS Protection** via Blade escaping
- ✅ **Server-side totals** (recalculated on every update)

---

## 🚀 Next Steps (Future Enhancements)

### Checkout Flow
1. Shipping address form
2. Payment method selection
3. Order review
4. Payment processing integration

### Cart Features
1. Save for later
2. Promo code input
3. Shipping calculator
4. Estimated delivery date
5. Product recommendations

### UX Improvements
1. Toast notifications instead of alerts
2. Undo remove action
3. Skeleton loaders
4. Optimistic UI updates
5. Quantity presets (2, 5, 10)

---

## 📚 Related Files

```
app/
├── Http/Controllers/
│   └── CartController.php (show method added)
├── Services/
│   └── CartService.php (existing)
resources/
├── views/
│   ├── cart/
│   │   └── index.blade.php (NEW - cart page)
│   └── components/
│       └── header.blade.php (updated cart link)
routes/
└── web.php (added cart.show route)
```

---

## 🐛 Troubleshooting

**Cart page shows empty but items in session?**
- Check `getCartSummary()` returns correct data
- Verify session driver is working
- Clear cache: `php artisan view:clear`

**Quantity update not working?**
- Check CSRF token in request headers
- Verify `/cart/update/{id}` route exists
- Check browser console for errors

**Layout broken on mobile?**
- Verify Tailwind CSS is compiled
- Check viewport meta tag in layout
- Test responsive breakpoints

**Total not updating?**
- Check Alpine.js is loaded
- Verify `x-data` initialization
- Check `subtotal` calculation in component

---

## ✅ Checklist

- [x] Cart page route (`/carrito`)
- [x] Controller method (`show()`)
- [x] Blade view with product list
- [x] Order summary sidebar (sticky)
- [x] Quantity selector with +/- buttons
- [x] Remove item functionality
- [x] Empty cart state
- [x] Responsive design (mobile/tablet/desktop)
- [x] Alpine.js integration
- [x] Real-time total updates
- [x] AJAX API integration
- [x] Header cart icon links to cart page
- [x] Loading states and error handling
- [x] Trust badges and security icons

---

**Implementation Date**: 2026-05-01  
**Version**: 1.0.0  
**Status**: ✅ Complete (Preview Only - No Payment)
