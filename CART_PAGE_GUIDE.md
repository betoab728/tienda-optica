# 🛒 Quick Start Guide - Shopping Cart Page

## 🚀 Accessing the Cart Page

### URL
```
http://localhost:8000/carrito
```

### From Code
```blade
<a href="{{ route('cart.show') }}">Ver Carrito</a>
```

### From Header
Click the cart icon (🛒) in the header - it now links to the cart page!

---

## 📸 What You'll See

### With Items in Cart

```
┌─────────────────────────────────────────────────────────┐
│  TIENDA ÓPTICA                                          │
│  🏠 Inicio > Carrito de Compras                         │
│                                                         │
│  🛒 Carrito de Compras                                  │
│  Tienes 2 productos en tu carrito                       │
├──────────────────────────────┬──────────────────────────┤
│                              │                          │
│  PRODUCTOS                   │  RESUMEN                 │
│                              │                          │
│  ┌────────────────────────┐  │  Subtotal: S/ 449.80   │
│  │ 👓 Ray-Ban Aviator     │  │  Envío: GRATIS         │
│  │    S/ 299.90          │  │  ────────────────       │
│  │    Cant: [−][2][+]    │  │  Total: S/ 449.80      │
│  │    Subtotal: S/599.80 │  │                        │
│  │                   [🗑] │  │  ┌──────────────────┐  │
│  └────────────────────────┘  │  │ Proceder al Pago │  │
│                              │  └──────────────────┘  │
│  ┌────────────────────────┐  │                        │
│  │ 👓 Oakley Sport        │  │  ✓ Compra segura       │
│  │    S/ 399.90          │  │  ✓ Envío gratis        │
│  │    Cant: [−][1][+]    │  │  ✓ Devolución 30 días  │
│  │    Subtotal: S/399.90 │  │                        │
│  │                   [🗑] │  │                        │
│  └────────────────────────┘  │                        │
│                              │                        │
│  ← Continuar comprando       │                        │
└──────────────────────────────┴──────────────────────────┘
```

### Empty Cart

```
┌─────────────────────────────────────┐
│  🏠 Inicio > Carrito de Compras     │
│                                     │
│  🛒 Carrito de Compras              │
│  Tu carrito está vacío              │
│                                     │
│  ┌─────────────────────────────┐   │
│  │                             │   │
│  │        🛒 (big icon)        │   │
│  │                             │   │
│  │  Tu carrito está vacío      │   │
│  │                             │   │
│  │  ¡Empieza a agregar        │   │
│  │   productos increíbles!     │   │
│  │                             │   │
│  │  [🔍 Explorar Productos]    │   │
│  │                             │   │
│  └─────────────────────────────┘   │
└─────────────────────────────────────┘
```

---

## 🎮 How to Use

### 1️⃣ View Cart Items
- Each product shows:
  - Product image
  - Name
  - Unit price
  - Current quantity
  - Subtotal (price × quantity)

### 2️⃣ Change Quantity

**Option A: Use Buttons**
```
Click [−] to decrease
Click [+] to increase
```

**Option B: Type Directly**
```
Click the number → Type new quantity → Press Enter
```

**Limits:**
- Minimum: 1
- Maximum: 99

### 3️⃣ Remove Items

```
Click [🗑] (trash icon) → Confirm → Item removed
```

**What happens:**
- Item disappears from list
- Totals recalculate automatically
- If last item: Redirects to empty cart state

### 4️⃣ Continue Shopping

```
Click "← Continuar comprando" to go back to homepage
```

### 5️⃣ Checkout

```
Click "Proceder al Pago" button
```
*(Not implemented yet - placeholder)*

---

## 💡 Key Features

### ⚡ Real-Time Updates
- Change quantity → Total updates instantly
- Remove item → Summary recalculates
- No page reload needed!

### 📱 Mobile Friendly
- Responsive design
- Touch-friendly buttons
- Scrolls smoothly

### 🔒 Secure
- CSRF protection
- Session-based storage
- Server-side validation

### 🎨 Modern Design
- Clean, minimal interface
- Smooth animations
- Professional styling

---

## 🧪 Testing Steps

### Test 1: Add Items to Cart
1. Go to homepage (`http://localhost:8000`)
2. Hover over a product
3. Click "Agregar al carrito"
4. Cart badge updates (+1)
5. Click cart icon in header
6. ✅ Should see product in cart page

### Test 2: Update Quantity
1. On cart page, click `[+]` button
2. ✅ Quantity increases
3. ✅ Subtotal updates
4. ✅ Total updates in summary
5. Click `[−]` button
6. ✅ Quantity decreases
7. ✅ Totals recalculate

### Test 3: Remove Item
1. Click trash icon [🗑]
2. ✅ Confirmation dialog appears
3. Click "OK"
4. ✅ Item disappears
5. ✅ Totals update

### Test 4: Empty Cart
1. Remove all items
2. ✅ Empty cart state displays
3. ✅ "Explorar Productos" button shows
4. Click button
5. ✅ Redirects to homepage

### Test 5: Mobile View
1. Open cart page
2. Resize browser to mobile width
3. ✅ Layout stacks vertically
4. ✅ Summary moves below products
5. ✅ All buttons remain clickable

---

## 📊 Cart Data Structure

### What's in the Cart?

```javascript
{
    items: [
        {
            id: 123,
            name: "Ray-Ban Aviator",
            price: 299.90,
            image: "https://cdn.example.com/123.jpg",
            quantity: 2
        },
        {
            id: 456,
            name: "Oakley Sport",
            price: 399.90,
            image: "https://cdn.example.com/456.jpg",
            quantity: 1
        }
    ],
    total_count: 3,        // 2 + 1
    subtotal: 999.70,      // (299.90×2) + (399.90×1)
    item_count: 2          // 2 products
}
```

---

## 🔗 Related Pages

### Homepage
```
http://localhost:8000
```
Browse products and add to cart

### Product Detail
```
http://localhost:8000/producto/123
```
View details and add with quantity

### Cart Page
```
http://localhost:8000/carrito
```
Review and manage cart

### (Future) Checkout
```
http://localhost:8000/checkout
```
*Not implemented yet*

---

## 🎯 Common Questions

### Q: Why does the page reload when I remove the last item?
**A:** To show the empty cart state properly. The page detects zero items and displays the "empty cart" message.

### Q: Can I add more than 99 items?
**A:** No, the limit is 99 per product for practical reasons.

### Q: Where is my cart data stored?
**A:** In the server session. It persists as long as your browser session is active.

### Q: What happens if I close the browser?
**A:** Your cart is saved in the session for 120 minutes (default Laravel session lifetime).

### Q: Can I save my cart for later?
**A:** Not yet - this feature could be added in the future with user accounts.

---

## 🚦 Status Indicators

### Loading States
- **Updating quantity**: Button shows spinner
- **Removing item**: Confirmation dialog
- **API call**: Background AJAX request

### Success States
- ✅ Quantity updated
- ✅ Item removed
- ✅ Totals recalculated

### Error States
- ❌ Network error: Alert message
- ❌ Invalid quantity: Reverts to previous value

---

## 🎨 Design Highlights

### Colors
- **Background**: Light gray (#f9fafb)
- **Cards**: Pure white (#ffffff)
- **Primary**: Dark gray (#111827)
- **Accent**: Red (#dc2626)
- **Success**: Green (#16a34a)

### Layout
- **Desktop**: Side-by-side (products left, summary right)
- **Mobile**: Stacked (products top, summary bottom)
- **Sticky**: Summary stays visible on scroll (desktop only)

### Typography
- **Title**: Large, bold, clear
- **Body**: Readable, comfortable spacing
- **Numbers**: Bold for emphasis

---

## 🛠️ Developer Notes

### Alpine.js Component
```javascript
x-data="cartManager({ items, total_count, subtotal })"
```

### Methods Available
```javascript
updateQuantity(productId, newQty)  // Update item quantity
removeItem(productId)              // Remove from cart
```

### Events Dispatched
```javascript
window.dispatchEvent(new CustomEvent('cart-updated', { 
    detail: cartData 
}))
```

---

## 📚 Documentation Files

1. **`CART_IMPLEMENTATION.md`** - Technical docs for cart system
2. **`CART_PAGE_DOCS.md`** - Complete cart page documentation
3. **`CART_EXAMPLES.html`** - Interactive code examples
4. **`CART_PAGE_GUIDE.md`** - This quick start guide

---

## ✅ Complete Feature Checklist

- [x] Cart page route (`/carrito`)
- [x] Product list with images
- [x] Quantity selector (+/- buttons)
- [x] Manual quantity input
- [x] Remove item button
- [x] Real-time subtotals
- [x] Order summary sidebar
- [x] Total calculation
- [x] Empty cart state
- [x] Breadcrumb navigation
- [x] Continue shopping link
- [x] Checkout button (placeholder)
- [x] Trust badges
- [x] Responsive design
- [x] Mobile optimization
- [x] Loading states
- [x] Error handling
- [x] CSRF protection

---

**Ready to test?** Visit http://localhost:8000/carrito 🚀

*Make sure you have items in your cart first!*
