# AI Context Document — Tienda Óptica

> **Purpose:** This document gives AI coding agents a complete architectural, business, and implementation picture of the project so they can work effectively without rescanning the entire codebase each session.
>
> **Last updated:** 2026-06-11

---

## 1. Project Overview

### Business Purpose

Tienda Óptica is **not** a standalone e-commerce platform. It is a **customer-facing online sales channel** that extends an existing ERP system used by an optical store chain. The ERP is the operational backbone and manages customers, sales, electronic invoicing, inventory, optical prescriptions, medical records, appointments, and financials. The Laravel app is a modern web storefront that reads from and eventually writes back to that ERP.

### Main Objectives

1. Expose the ERP product catalog online with a clean, mobile-first shopping experience.
2. Provide an **AI-assisted optical prescription workflow** that partially replicates the in-store advisory experience: customers upload their prescription, Gemini Vision extracts the optical values, and the system recommends lenses.
3. Feed completed orders back into the ERP (checkout integration is in progress).

### Current Project Status

The project is in **active development**. The product catalog, product detail, session-based cart, and the full prescription AI analysis flow are implemented. Checkout/payment processing and ERP write-back are planned but not yet built.

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| **Backend framework** | Laravel 12 (PHP 8.2+) |
| **Frontend interactivity** | Alpine.js 3.x (CDN) |
| **CSS framework** | Tailwind CSS 4.0 (Vite plugin) |
| **Build tool** | Vite 7.0.7 |
| **Carousel** | Swiper 12.1.3 |
| **Searchable select** | Tom Select 2.6.1 |
| **HTTP client (JS)** | Axios 1.x (available, used minimally) |
| **AI integration** | Google Gemini API (`gemini-3.5-flash`, vision + JSON mode) |
| **Primary database** | SQL Server (production) |
| **Dev database** | SQLite (default for local) |
| **Session storage** | Laravel sessions (cart state) |

---

## 3. Architecture

### High-Level Layers

```
Routes (web.php)
  └── Controllers          thin — validate input, delegate, return response
        └── Services       business logic (CartService, OpticalPrescriptionService, GeminiService)
              └── Repositories  data access only (ProductoRepository, OcupacionRepository)
                    └── DB::  Laravel query builder against SQL Server
```

### Controllers

| Controller | Route prefix | Responsibility |
|---|---|---|
| `ProductoController` | `/`, `/producto/{id}` | Fetch products + occupations, render views |
| `CartController` | `/carrito`, `/cart/*` | Cart HTML page + JSON REST API (session-backed) |
| `PrescriptionController` | `/prescription/*` | Receive uploaded prescription, delegate to service, return JSON |

Controllers are kept intentionally thin: they validate the HTTP request, call a service or repository, and return the appropriate view or JSON response. No business logic lives in controllers.

### Services

| Service | Purpose |
|---|---|
| `CartService` | CRUD operations on the cart stored in Laravel session under key `shopping_cart` |
| `GeminiService` | HTTP client wrapper for the Gemini REST API (text and vision endpoints) |
| `OpticalPrescriptionService` | Orchestrates prescription analysis: builds prompt, calls Gemini, parses/validates/normalizes the JSON response |
| `CustomSqlServerConnector` | Extends Laravel's PDO SQL Server connector to remove `ATTR_STRINGIFY_FETCHES` (prevents string coercion of numeric values from SQL Server) |

### Repositories

| Repository | Table / SP | Purpose |
|---|---|---|
| `ProductoRepository` | `optica.producto` (+ joins) | Catalog list and single product lookup |
| `OcupacionRepository` | SP `ListarOcupaciones` | Occupation list for the prescription form dropdown |

### Blade Components

All reusable UI pieces live in `resources/views/components/`. Laravel auto-resolves `<x-component-name />` tags.

### Routing

All routes are defined in `routes/web.php`. There is no API prefix — the cart JSON endpoints sit alongside HTML routes. CSRF protection applies to all POST/PUT/DELETE routes.

### Database Access Patterns

- The repositories use Laravel's `DB::` query builder (not Eloquent ORM) for the SQL Server tables because the ERP schema uses legacy column names and stored procedures that don't map cleanly to Eloquent conventions.
- The `Producto` Eloquent model exists but is not currently used by the repositories.
- The `User` model is present (Laravel scaffold) but authentication is not yet implemented.

### Dependency Injection

Services and repositories are constructor-injected into controllers. `CustomSqlServerConnector` is bound in `AppServiceProvider::register()`. No manual `new` instantiation in controllers.

---

## 4. Database

### SQL Server Schema (ERP)

All ERP tables are under the `optica` schema. The application treats this as a **read-only** data source (for now).

| Table | Key Columns | Notes |
|---|---|---|
| `optica.producto` | `idproducto`, `codigo`, `color`, `p_venta`, `p_minimo`, `idfamilia`, `idmodelo`, `iddisenio`, `venta_online` | `venta_online = 1` filters catalog items |
| `optica.familia` | `idfamilia`, `nombre` | Product category (e.g., "Lentes de Sol") |
| `optica.modelo` | `idmodelo`, `nombre`, `idmarca` | Product model name |
| `optica.marca` | `idmarca`, `nombre` | Brand name |
| `optica.disenio` | `iddisenio`, `descripcion` | Lens/frame design description |

### Stored Procedures Used

| SP Name | Called From | Returns |
|---|---|---|
| `ListarOcupaciones` | `OcupacionRepository` | `idocupacion`, `descripcion`/`descripción`, `estado` |

The repository handles both accented (`descripción`) and unaccented (`descripcion`) column name variants from the SP result.

### Product Images

Images are **not** stored in the database. They are hosted externally and resolved at runtime:

- Thumbnail: `env('IMAGES_URL') . "/{idproducto}-thumb.jpg"`
- Detail images (5 total): `env('IMAGES_URL') . "/{idproducto}-01.jpg"` through `-05.jpg`

`IMAGES_URL` must be set in `.env` to point to the image CDN or server.

### Environment Variables Required

```
DB_CONNECTION=sqlsrv
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
GEMINI_API_KEY=
IMAGES_URL=https://cdn.example.com/images
```

---

## 5. Ecommerce Flow

```
1. Landing (/)
   ├── popup-asesoria modal appears
   │     ├── "Tengo una receta" → opens prescription flow
   │     └── "Comprar sin receta" → scrolls to catalog
   └── Slider + product catalog grid

2. Catalog (rendered inside home.blade.php via @include)
   - 20 products from SQL Server (venta_online = 1)
   - Hover reveals quick-add button
   - "Ver detalle" links to /producto/{id}

3. Product Detail (/producto/{id})
   - 5 image thumbnails, switchable via Alpine
   - Quantity selector (1–10)
   - "Agregar al carrito" → POST /cart/add (JSON API)
   - Trust badges (shipping, warranty, returns)

4. Cart (/carrito)
   - Items from Laravel session
   - Inline quantity update (PUT /cart/update/{id})
   - Item removal (DELETE /cart/remove/{id})
   - Sticky summary (desktop) / fixed bottom bar (mobile)
   - "Procesar al Pago" button → checkout (not yet implemented)
```

Cart state is managed in the browser with Alpine.js and persisted server-side in the Laravel session. The header badge updates via the `cart-updated` custom DOM event dispatched after every cart mutation.

---

## 6. Optical Prescription AI Flow

### Entry Points

The prescription workflow is triggered from `popup-asesoria.blade.php` when the user selects "Tengo una receta", which dispatches the `open-prescription-flow` DOM event caught by `prescription-flow.blade.php`.

### Step-by-Step Flow

```
Step 1 — Upload (prescription-flow.blade.php)
  ├── Drag-and-drop or file picker (JPG, PNG, WebP, PDF, max 10 MB)
  ├── Occupation selector (Tom Select, populated from OcupacionRepository → ListarOcupaciones SP)
  ├── Date of birth input
  └── "Analizar Receta" button → startAnalysis()

Step 2 — Loading
  └── Alpine shows animated spinner with progress indicators

Step 3a — Success → Result
  └── Renders optical-form.blade.php with AI-extracted data

Step 3b — Error
  └── Shows error message with retry button
```

### Backend Analysis Pipeline

```
PrescriptionController@analyze
  ├── Validate file (max:10240, mimes:jpeg,jpg,png,webp,pdf)
  ├── base64_encode(file contents)
  └── OpticalPrescriptionService@analyze(base64, mimeType)
        ├── buildPrompt()
        │     ├── Read: resources/prompts/analizar-receta.md
        │     └── Append: resources/json/receta-optica.json (schema)
        ├── GeminiService@analizarImagen(base64, mimeType, prompt)
        │     └── POST to Gemini REST API
        │           config: temperature=0.1, responseMimeType=application/json
        ├── extractTextFromResponse()
        ├── parseJson() — strips any markdown code fences
        ├── validatePrescription() — ensures vision_lejos and vision_cerca exist
        ├── normalize() — empty strings → null, boolean coercion, default complexity
        └── return structured prescription array
```

### Optical Form (`optical-form.blade.php`)

The form displays the AI-extracted prescription data in editable fields bound via `x-model`. Sections:

1. Patient info (name, age, date, doctor)
2. Distance vision — OD/OI: esfera, cilindro, eje, DIP, AV
3. Near vision — same structure
4. ADD and prisma values
5. Contact lens data (expandable section)
6. Indications (free text)
7. **AI Analysis Panel** (shown when `analisis_ia` is present):
   - Recommended lens type
   - Complexity badge (baja / estándar / media / alta, color-coded)
   - Flags: requires multifocal, high-index, diameter reduction, appointment
   - Observations text

The user reviews, corrects if needed, then clicks "Continuar al catálogo".

---

## 7. AI Components

### GeminiService (`app/Services/GeminiService.php`)

Thin HTTP client for the Gemini REST API.

- **Model used:** `gemini-3.5-flash`
- **Endpoint:** `https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent`
- **Auth:** API key as query parameter (`?key=...`)
- **Timeout:** 120 seconds
- **Vision request config:** `temperature: 0.1`, `responseMimeType: "application/json"`
- **Methods:** `analizarTexto()` (text only), `analizarImagen()` (multimodal), `extractTextFromResponse()` (path: `candidates[0].content.parts[0].text`)

### System Prompt (`resources/prompts/analizar-receta.md`)

Instructs Gemini to act as an optometrist specialist. Key rules embedded in the prompt:

- Return only valid JSON matching the schema — no markdown wrapping, no explanations
- Preserve numeric optical values exactly as they appear in the document
- Separate OD (right eye) and OI (left eye)
- Separate distance vision (`vision_lejos`) from near vision (`vision_cerca`)
- Use `null` for any field not present in the document
- Do not hallucinate values
- Populate `analisis_ia` with interpretation, recommended lens type, complexity level, and requirement flags

### JSON Schema (`resources/json/receta-optica.json`)

Defines the full structure the AI must return. Top-level keys:

| Key | Content |
|---|---|
| `paciente` | nombre, edad, fecha_nacimiento, fecha_receta, doctor, tipo_receta, ocupacion, uso_principal |
| `vision_lejos` | `od` and `oi` each with: esfera, cilindro, eje, dip, av |
| `vision_cerca` | same structure as vision_lejos |
| `add` | od, oi |
| `prisma` | od, oi |
| `lente_contacto` | od/oi each with: esfera, cilindro, eje, cb, diametro, av |
| `indicaciones` | free text string or null |
| `analisis_ia` | interpretacion_usuario, tratamientos_recomendados, tipo_lente_recomendado, requiere_multifocal (bool), requiere_alto_indice (bool), requiere_reduccion_diametro (bool), requiere_cita (bool), nivel_complejidad (enum: baja/estándar/media/alta), observaciones |

The schema is appended verbatim to the system prompt so Gemini produces conformant output.

---

## 8. Important Files

| File | Responsibility |
|---|---|
| `routes/web.php` | All application routes |
| `app/Http/Controllers/ProductoController.php` | Product catalog and detail page rendering |
| `app/Http/Controllers/CartController.php` | Cart HTML page + JSON REST API |
| `app/Http/Controllers/PrescriptionController.php` | Prescription upload endpoint |
| `app/Services/CartService.php` | Session-based cart CRUD and totals calculation |
| `app/Services/GeminiService.php` | Gemini API HTTP client |
| `app/Services/OpticalPrescriptionService.php` | Prescription analysis orchestration |
| `app/Services/CustomSqlServerConnector.php` | PDO SQL Server fix (no string coercion) |
| `app/Repositories/ProductoRepository.php` | SQL Server product queries |
| `app/Repositories/OcupacionRepository.php` | SQL Server `ListarOcupaciones` SP call |
| `app/Providers/AppServiceProvider.php` | Registers custom SQL Server connector; forces HTTPS in prod |
| `resources/views/home.blade.php` | Main landing page (includes catalog, components) |
| `resources/views/components/popup-asesoria.blade.php` | Welcome modal — entry point to prescription flow |
| `resources/views/components/prescription-flow.blade.php` | Multi-step prescription analysis UI (Alpine-driven) |
| `resources/views/components/optical-form.blade.php` | Editable prescription form with AI analysis panel |
| `resources/views/components/header.blade.php` | Header with cart badge and search |
| `resources/views/catalogo.blade.php` | Product grid (included into home) |
| `resources/views/producto/detalle.blade.php` | Product detail page |
| `resources/views/cart/index.blade.php` | Cart page |
| `resources/prompts/analizar-receta.md` | Gemini system prompt for prescription analysis |
| `resources/json/receta-optica.json` | JSON schema defining prescription output structure |
| `config/services.php` | Gemini API key config |
| `config/database.php` | Database connections including `sqlsrv` |
| `resources/css/app.css` | Tailwind + Tom Select styles + custom overrides |
| `resources/js/app.js` | Swiper + Tom Select initialization |
| `vite.config.js` | Vite build configuration |

---

## 9. UI/UX Conventions

### Tailwind CSS 4.0

- Utility-first. No custom Tailwind config file — uses the new Vite plugin approach with CSS-first configuration.
- Responsive breakpoints: `sm:` / `md:` / `lg:` / `xl:` — design is **mobile-first**.
- Color palette in use: `red-500`/`red-600` (brand primary), `gray-*` (neutrals), `green-*` (stock/success), `yellow-*` (warnings), `blue-*` (info).
- `[x-cloak]` utility: `display: none` prevents Alpine-uninitialized flicker.

### Alpine.js

- All reactive UI is declared inline with `x-data`, `x-bind`, `x-model`, `x-show`, `x-on`, `x-transition`.
- Complex components (prescription flow, cart manager) define named functions in `<script>` tags within the Blade view and pass them to `x-data`.
- Custom DOM events (`cart-updated`, `open-prescription-flow`) decouple components.
- No separate `.js` modules for Alpine components — logic stays collocated with the template.

### Responsive Design

- Mobile-first grid: products use `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4`.
- Cart layout collapses to single column on mobile; desktop shows 2/3 + 1/3 split.
- Sticky elements: cart summary is `sticky top-4` on desktop; on mobile it becomes a fixed bottom bar.
- Navigation: full navbar hidden on mobile, replaced by hamburger menu (slide-in sidebar).

### Enterprise UI Principles

- Loading states on every async action (spinners, disabled buttons).
- Error states displayed inline (not just console).
- Form inputs editable by default (no read-only lock on AI-extracted values — user always has control).
- Trust badges (shipping, warranty, returns) appear at cart and product detail level.
- No flashy animations — transitions are subtle (300ms ease).

---

## 10. Development Conventions

### Repository Pattern

Data access is isolated in `app/Repositories/`. Controllers and services never use `DB::` directly. Adding a new data source means adding a repository; existing controllers/services don't change.

### Service Layer

Business logic lives in `app/Services/`. Services are constructor-injected. Each service has a single clear responsibility:

- `CartService` — cart state only
- `GeminiService` — Gemini API transport only
- `OpticalPrescriptionService` — prescription analysis orchestration only

### Clean Controllers

Controllers do three things only: validate input, delegate to a service/repository, return a response. They contain no SQL, no business logic, no file I/O.

### Reusable Blade Components

All repeated or self-contained UI is a Blade component in `resources/views/components/`. Components accept `$props` via the `@props` directive and are used as `<x-component-name :prop="$value" />`.

### Naming Conventions

- PHP classes: PascalCase
- PHP methods: camelCase
- Blade view files: kebab-case
- Alpine.js data properties: camelCase
- Database columns: snake_case (following ERP convention)
- Routes: kebab-case URL slugs
- Spanish naming: Domain-specific entities use Spanish names matching the ERP schema (e.g., `idproducto`, `familia`, `marca`, `esfera`, `cilindro`)

### No Eloquent for ERP Tables

ERP tables use the `DB::` query builder directly. This is intentional — the ERP schema is not owned by this application and may change independently. Eloquent's assumptions (timestamps, `id` primary key) don't match the ERP schema.

---

## 11. Current Features Implemented

- [x] Product catalog page with SQL Server integration (20 products, `venta_online = 1` filter)
- [x] Product detail page with 5-image gallery and quantity selector
- [x] Session-based shopping cart (add, update quantity, remove, clear)
- [x] Cart count badge in header with real-time updates via custom DOM event
- [x] Cart page with full item management and order summary
- [x] Responsive navigation (desktop navbar + mobile hamburger sidebar)
- [x] Image slider (Swiper) on home page
- [x] Welcome modal (`popup-asesoria`) with dual CTA
- [x] Full prescription upload and AI analysis flow:
  - File upload (drag-and-drop + picker)
  - Occupation selector (Tom Select, populated from ERP SP)
  - Date of birth input
  - Gemini Vision analysis
  - JSON parsing, validation, normalization
  - Editable optical form with AI analysis panel
- [x] Custom SQL Server PDO connector (no string coercion)
- [x] HTTPS enforcement in production (`AppServiceProvider`)
- [x] Debug route `/test-gemini` for API connectivity verification

---

## 12. Pending Features

- [ ] **Checkout / payment processing** — "Procesar al Pago" button exists but leads nowhere; payment gateway integration and ERP order write-back needed
- [ ] **User authentication** — `User` model and Laravel auth scaffold exist but are not wired to any routes or views
- [ ] **Prescription-to-catalog recommendation** — after analysis, automatically filter/highlight compatible products based on `tipo_lente_recomendado` and `nivel_complejidad`
- [ ] **Product search** — search bar in header renders but has no backend route or logic
- [ ] **Promotions page** — nav link "Promociones" is present but has no route
- [ ] **Contact page** — nav link "Contáctanos" is present but has no route
- [ ] **Individual catalog pages** — nav links for Lentes de Sol, Lentes Oftálmicos, Lentes de Contacto have no dedicated filtered routes
- [ ] **Product catalog pagination** — currently hard-limited to 20 items
- [ ] **Checkout-ERP write-back** — completed orders must eventually be sent to the ERP as sales records
- [ ] **Order tracking** — "Seguimiento de orden" link exists in header but has no route

---

## 13. Recommendations for Future AI Agents

### Architecture Rules — Do Not Break

1. **Never put SQL in controllers.** All database access goes through a repository in `app/Repositories/`.
2. **Never put business logic in controllers.** It belongs in a service in `app/Services/`.
3. **Never use Eloquent for ERP (`optica.*`) tables.** Use `DB::` query builder. The ERP schema is external and does not follow Laravel conventions.
4. **The JSON schema (`receta-optica.json`) and prompt (`analizar-receta.md`) are tightly coupled.** If you change the schema, update the prompt too, and update `optical-form.blade.php` to render any new fields.
5. **Cart state lives exclusively in Laravel session** under the key `shopping_cart`. Do not introduce a database-backed cart without considering the session-based cart first.

### Frontend Rules

6. **Alpine.js is the only reactive layer.** Do not introduce Vue, React, or Livewire unless there is a strong reason and explicit user agreement.
7. **No build-step JS for Alpine components.** Alpine `x-data` logic stays inline in Blade files, collocated with the template.
8. **Tailwind 4.0 uses CSS-first config** — there is no `tailwind.config.js`. Add custom utilities in `resources/css/app.css` using `@theme` or `@layer`.
9. **Tom Select must be globally accessible** as `window.TomSelect` because Alpine.js calls it from inline `x-init` expressions. This is already set up in `resources/js/app.js`.

### AI Integration Rules

10. **The Gemini model in `GeminiService.php` is `gemini-3.5-flash`.** If upgrading to a newer model, update the URL constant and test that the JSON mode still works correctly.
11. **Temperature is deliberately set to 0.1** for prescription analysis — do not raise it. Higher temperatures produce inconsistent optical values.
12. **`OpticalPrescriptionService::normalize()`** is the single place responsible for data coercion after Gemini responds. If the schema gains new fields, add their normalization logic here.
13. **The prompt instructs Gemini to return raw JSON only** — no markdown fences. However, `parseJson()` defensively strips fences anyway. Maintain this defensive parsing.

### ERP Integration Context

14. This application is an **extension of the ERP, not a replacement**. Any new feature that involves customers, orders, invoices, or prescriptions should consider how that data flows to or from the ERP.
15. **`OcupacionRepository`** demonstrates the correct pattern for calling ERP stored procedures: `DB::select('EXEC StoredProcedureName')`. Follow this pattern for any new SP integrations.
16. The `CustomSqlServerConnector` in `AppServiceProvider` is a required workaround for numeric values returned from SQL Server. Do not remove it or bypass it — without it, prices and IDs will be returned as strings.

### General

17. **Do not use `npm run build` for dev testing** — use `npm run dev` for hot-reload during development.
18. The `/test-gemini` debug route is a quick way to verify the API key is working. Remove it before going to production.
19. When adding new routes, match the existing pattern: HTML page routes at the top level, JSON API routes under a logical prefix (`/cart/*`, `/prescription/*`).
20. Image URLs for products are constructed at the repository layer using `env('IMAGES_URL')`. Do not hardcode image paths anywhere.
