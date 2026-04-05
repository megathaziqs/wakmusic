# 🎵 WakMusic - Music Streaming Platform

A modern music streaming platform built with **Vue 3** + **Laravel 12** + **Vite** + **Tailwind CSS**.

## 📋 Project Structure

```
wakmusic/
├── app/                          # Laravel backend
│   └── Http/
│   └── Models/
├── resources/
│   ├── js/
│   │   ├── App.vue              # Root Vue component (navigation & routing)
│   │   ├── app.js               # Vue app entry point
│   │   ├── bootstrap.js         # Bootstrap configuration
│   │   ├── components/          # Reusable Vue components
│   │   │   ├── Button.vue       # Button component (multi-variant)
│   │   │   ├── Card.vue         # Card component
│   │   │   └── Input.vue        # Input component
│   │   └── pages/               # Page components
│   │       ├── HomePage.vue     # Home/Dashboard
│   │       ├── MusicListPage.vue# Music library with CRUD
│   │       └── PlaylistPage.vue # Playlists management
│   ├── css/
│   │   └── app.css              # Global styles + Tailwind
│   └── views/
│       └── welcome.blade.php    # Laravel Blade template (Vue mount point)
├── routes/
│   ├── web.php                  # Web routes
│   └── api.php                  # API routes
├── vite.config.js               # Vite configuration
├── package.json                 # Node dependencies
└── composer.json                # PHP dependencies
```

## 🚀 Features Implemented

### ✅ Vue Components
- **Button** - Multi-variant (primary, secondary, danger, ghost, outline) with sizes (sm, md, lg)
- **Card** - Reusable card component with consistent styling
- **Input** - Styled input field with dark mode support

### ✅ Pages
1. **Home Page** - Hero section, features showcase, platform statistics
2. **Music List Page** - Display songs, add/search/delete functionality
3. **Playlist Page** - Create playlists, manage songs, view playlist details

### ✅ Features
- ✨ Dark mode support (using Tailwind CSS dark mode)
- 🎨 Consistent design system inspired by Sakai Vue
- 📱 Responsive grid layouts
- 🔍 Search and filter functionality
- ⚡ Fast HMR with Vite
- 🎯 Component-based architecture

## 🛠️ Tech Stack

- **Frontend**: Vue 3 (Composition API)
- **Build Tool**: Vite 7
- **Styling**: Tailwind CSS 4
- **Backend**: Laravel 12
- **Package Manager**: npm + composer

## 📦 Installation & Setup

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer
- npm

### Quick Start

1. **Install dependencies**
```bash
npm install
composer install
```

2. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Run database migrations**
```bash
php artisan migrate
```

4. **Start development server**
```bash
npm run dev
```

In another terminal, start Laravel:
```bash
php artisan serve
```

The app will be available at `http://localhost:8000`

### Or use the combined dev script:
```bash
composer run dev
```

This will start:
- Laravel development server (port 8000)
- Vite dev server with HMR
- PHP Queue listener
- Log viewer (Pail)

## 🎨 Design System

### Colors
- **Primary**: `#F53003` (Orange-Red)
- **Background Light**: `#FDFDFC`
- **Background Dark**: `#0a0a0a`
- **Text Light**: `#1b1b18`
- **Text Dark**: `#EDEDEC`
- **Border**: `#e3e3e0` / `#3E3E3A` (dark)

### Button Variants
- **primary** - Solid orange (main CTA)
- **secondary** - Gray background
- **danger** - Red background
- **ghost** - No background, text only
- **outline** - Border only

### Spacing & Sizing
Following Tailwind CSS 4 conventions with customized color palette.

## 📝 Usage Examples

### Using Button Component
```vue
<Button variant="primary" size="md" @click="handleClick">
  Click Me
</Button>
```

### Using Card Component
```vue
<Card>
  <h3 class="text-lg font-semibold">Card Title</h3>
  <p>Card content goes here</p>
</Card>
```

### Using Input Component
```vue
<Input 
  placeholder="Enter song name" 
  v-model="searchQuery"
  type="text"
/>
```

## 🔄 State Management

Currently using Vue's Composition API with `ref` and `computed` for local state. 

For larger applications, consider adding:
- Pinia (state management)
- axios (API calls)

## 📡 API Integration

Template route in `routes/api.php` for future API endpoints:

```php
// Example routes
Route::get('/songs', [SongController::class, 'index']);
Route::post('/songs', [SongController::class, 'store']);
Route::delete('/songs/{id}', [SongController::class, 'destroy']);
```

## 🎯 Next Steps

1. **Backend Development**
   - Create Laravel Models (Song, Playlist, User)
   - Setup Controllers for CRUD operations
   - Create API routes with proper authentication

2. **API Integration**
   - Replace mock data with API calls using axios
   - Add loading states and error handling
   - Implement proper error notifications

3. **Authentication**
   - Setup Laravel Sanctum for API authentication
   - Add login/register pages
   - Protected routes and API endpoints

4. **Database**
   - Design database schema
   - Create migrations
   - Setup relationships

5. **Advanced Features**
   - Real-time notifications
   - Music player with playback controls
   - User profiles and social features
   - Upload and streaming functionality

## 🧪 Build & Deploy

### Development
```bash
npm run dev
```

### Production Build
```bash
npm run build
```

### Production Serve
```bash
php artisan serve --env=production
```

## 📚 Resources

- [Vue 3 Documentation](https://vuejs.org/)
- [Laravel Documentation](https://laravel.com/docs)
- [Vite Documentation](https://vitejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)

## 📄 License

MIT License - feel free to use this project as a template!

---

**Happy Coding! 🎵🚀**
