# ByCrousty Client Application

Professional Vue 3 + Capacitor client application for ByCrousty bakery.

## Features

- 🛍️ Browse products and categories
- 🛒 Shopping cart (localStorage for guests, database for authenticated users)
- 👤 User authentication (login/register)
- 📱 Mobile-first responsive design
- ⭐ Points system integration
- 🎨 Professional UI with Bootstrap 5
- 🔄 Cart synchronization after login

## Tech Stack

- Vue 3 (Composition API with `<script setup>`)
- Vue Router
- Axios for API calls
- Bootstrap 5 (via CDN)
- Bootstrap Icons (via CDN)
- Capacitor (for mobile deployment)
- LocalStorage for guest cart management

## Project Structure

```
src/
├── assets/
│   └── styles.css          # Global styles matching admin app
├── components/
│   ├── TopNavbar.vue       # Fixed top navigation
│   ├── BottomNavbar.vue    # Fixed bottom navigation (when logged in)
│   ├── SidebarDrawer.vue   # Side menu
│   ├── CarouselSlider.vue  # Bootstrap carousel
│   ├── ProductCard.vue     # Product display card
│   └── CategoryCard.vue    # Category display card
├── layouts/
│   └── MainLayout.vue      # Main app layout
├── pages/
│   ├── WelcomePage.vue     # First-time welcome screen
│   ├── HomePage.vue        # Main landing page
│   ├── CategoriesPage.vue  # Categories list
│   ├── ProductsPage.vue    # Products list
│   ├── ProductDetailsPage.vue # Single product view
│   ├── LoginPage.vue       # User login
│   ├── RegisterPage.vue    # User registration
│   ├── CartPage.vue        # Shopping cart
│   └── ProfilePage.vue     # User profile
├── router/
│   └── index.js            # Vue Router configuration
├── services/
│   ├── api.js              # API service
│   └── cart.js             # Cart management service
├── store/
│   ├── auth.js             # Authentication store
│   ├── cart.js             # Cart store
│   └── ui.js               # UI state store
├── App.vue                 # Root component
└── main.js                 # Application entry point
```

## Setup Instructions

### 1. Install Dependencies

```bash
cd frontend_clients_application
npm install
```

### 2. Configure Environment

Create a `.env` file:

```bash
cp .env.example .env
```

Update the API URL in `.env`:

```
VITE_API_URL=http://localhost:8000/api/v1
```

### 3. Run Development Server

```bash
npm run dev
```

The app will be available at `http://localhost:5173`

### 4. Build for Production

```bash
npm run build
```

## API Integration

The app connects to the Laravel backend API. Make sure the backend is running and accessible.

### API Endpoints Used:

- `POST /client/register` - User registration
- `POST /client/login` - User login
- `GET /client/me` - Get user profile
- `GET /categories` - List categories
- `GET /products` - List products
- `GET /products/{id}` - Get product details
- `GET /categories/{id}/products` - Get products by category
- `GET /client/cart` - Get cart (authenticated)
- `POST /client/cart/add` - Add to cart (authenticated)
- `DELETE /client/cart/remove/{id}` - Remove from cart (authenticated)
- `POST /client/cart/clear` - Clear cart (authenticated)
- `POST /client/cart/sync` - Sync localStorage cart (authenticated)
- `GET /client/static/about` - About us content
- `GET /client/static/contact` - Contact information
- `GET /client/static/find-us` - Location information

## Features Details

### Welcome Screen
- Shows only on first app launch
- Stored in localStorage: `has_seen_welcome`
- Can be reset by clearing browser data

### Guest Shopping
- Browse products without login
- Add items to cart (stored in localStorage)
- Cart persists across sessions

### Authenticated Shopping
- All guest features
- Cart stored in database
- Automatic cart sync after login
- User profile with points
- Order history (coming soon)

### Cart Synchronization
When a guest user logs in:
1. LocalStorage cart is synced to database
2. Existing database items are merged
3. LocalStorage cart is cleared
4. User sees unified cart

### Mobile Optimization
- Touch-friendly UI
- Safe area support for notched devices
- Optimized for iOS and Android
- Ready for Capacitor deployment

## Color Palette

Matching the admin application:

- Primary: `#FFD700` (Gold)
- Secondary: `#646cff`
- Background: `#242424` / `#0f0f0f`
- Text: `rgba(255, 255, 255, 0.87)`
- Border: `#333333`

## Navigation Guards

Routes are protected based on authentication:

- **Public**: Home, Categories, Products, Cart (guest mode)
- **Guest Only**: Login, Register (redirect to home if authenticated)
- **Protected**: Profile (requires authentication)

## Development Notes

- Use Composition API with `<script setup>` syntax
- All API calls go through the centralized `api.js` service
- Cart logic is handled by `cart.js` service
- State management uses Composition API stores (no Vuex/Pinia needed)
- Components are fully mobile-responsive

## Capacitor Deployment

To deploy as a mobile app:

```bash
# Build the Vue app
npm run build

# Add Capacitor platform (if not already added)
npx cap add ios
npx cap add android

# Sync web assets to native projects
npx cap sync

# Open in Xcode (iOS)
npx cap open ios

# Open in Android Studio
npx cap open android
```

## License

Proprietary - ByCrousty 2025
