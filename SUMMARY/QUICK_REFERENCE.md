# 🚀 WakMusic Quick Reference Card

## 📁 Project Structure at a Glance

```
wakmusic/
├── 📄 Documentation Files (READ THESE FIRST!)
│   ├── SUMMARY.md                 ← START HERE! Overview & quick start
│   ├── GETTING_STARTED.md         ← Setup & development guide
│   ├── PROJECT_OVERVIEW.md        ← Complete project documentation
│   ├── ARCHITECTURE.md            ← System design & data flow
│   ├── DEVELOPMENT_CHECKLIST.md   ← Phase-by-phase tasks
│   ├── COMPONENT_GUIDE.md         ← UI components showcase
│   └── This file                  ← Quick reference
│
├── 🎨 Frontend (resources/js/)
│   ├── App.vue                    ← Root component with navigation
│   ├── app.js                     ← Vue app entry point
│   ├── components/
│   │   ├── Button.vue             ← 5 variants, 3 sizes
│   │   ├── Card.vue               ← Reusable container
│   │   └── Input.vue              ← Text input field
│   └── pages/
│       ├── HomePage.vue           ← Hero + Features + Stats
│       ├── MusicListPage.vue      ← Songs CRUD + Search
│       └── PlaylistPage.vue       ← Playlists management
│
├── 🔧 Backend (app/)
│   ├── Http/Controllers/
│   │   ├── SongController.php      ← Song API template
│   │   └── PlaylistController.php  ← Playlist API template
│   └── Models/
│       └── User.php                ← Existing user model
│
├── 🛣️ Routes (routes/)
│   ├── web.php                     ← Web routes
│   └── api.php                     ← API routes (to implement)
│
├── 📚 Config Files
│   ├── vite.config.js              ← Vite build config
│   ├── tailwind.config.js          ← Tailwind CSS config
│   ├── package.json                ← NPM dependencies
│   └── composer.json               ← PHP dependencies
│
└── 🗄️ Database
    └── database/
        ├── migrations/             ← Database schemas
        └── seeders/                ← Seed data
```

---

## ⚡ Quick Start (5 Minutes)

```powershell
# 1. Install dependencies
npm install
composer install

# 2. Setup environment
php artisan key:generate
php artisan migrate

# 3. Start development (Terminal 1)
php artisan serve

# 4. Start frontend (Terminal 2)
npm run dev

# 5. Visit: http://localhost:8000
```

**Or use combined command:**
```powershell
composer run dev   # Starts everything
```

---

## 🎯 Key Tasks

### Add a New Button
```vue
<Button variant="primary" size="md" @click="action">Click Me</Button>
```

**Variants:** primary, secondary, danger, ghost, outline  
**Sizes:** sm, md, lg

### Add a New Page
1. Create `resources/js/pages/MyPage.vue`
2. Import in `App.vue`
3. Add to `pageComponents` object
4. Add to `navigation` array

### Add a New Component
1. Create in `resources/js/components/`
2. Import where needed
3. Use with `<ComponentName />`

### Connect to API (Phase 2)
```javascript
const response = await axios.get('/api/songs');
songs.value = response.data.data;
```

---

## 🎨 Component Reference

### Button
```vue
<Button 
  variant="primary|secondary|danger|ghost|outline"
  size="sm|md|lg"
  :disabled="boolean"
  @click="handler"
>
  Text
</Button>
```

### Card
```vue
<Card>
  <!-- Any content -->
</Card>
```

### Input
```vue
<Input 
  type="text|email|password|number|date"
  placeholder="..."
  v-model="value"
/>
```

---

## 🎨 Tailwind CSS Cheatsheet

### Layout
```
flex                 # Flexbox container
flex-col             # Column direction
gap-4                # Space between items
grid                 # Grid layout
grid-cols-1          # 1 column
md:grid-cols-2       # 2 columns on tablets
lg:grid-cols-3       # 3 columns on desktop
```

### Text
```
text-sm              # Small text (14px)
text-lg              # Large text (18px)
text-xl              # Extra large (20px)
font-bold            # Bold text
font-semibold        # Semi-bold text
text-[#F53003]       # Custom color
dark:text-white      # Dark mode color
```

### Background
```
bg-white             # White background
dark:bg-[#0a0a0a]    # Dark mode background
bg-gradient-to-r     # Gradient background
hover:bg-gray-100    # Hover effect
```

### Spacing
```
p-4                  # Padding all sides (1rem)
px-4                 # Padding left & right
py-2                 # Padding top & bottom
m-4                  # Margin all sides
mb-4                 # Margin bottom
```

### Size
```
w-full               # Width 100%
h-screen             # Height 100vh
min-h-screen         # Minimum height 100vh
max-w-4xl            # Max width
```

### Borders & Shadows
```
border               # 1px border
rounded-lg           # Rounded corners
shadow-md            # Medium shadow
hover:shadow-lg      # Hover shadow
```

---

## 🔄 Vue 3 Composition API

### Reactive State
```javascript
import { ref } from 'vue';

const count = ref(0);
count.value++           // Update value
```

### Computed Properties
```javascript
import { computed } from 'vue';

const doubled = computed(() => count.value * 2);
```

### Lifecycle Hooks
```javascript
import { onMounted } from 'vue';

onMounted(() => {
  console.log('Component mounted');
});
```

### Event Handling
```vue
@click="handler"
@submit="handler"
v-model="data"          # Two-way binding
```

### Conditional Rendering
```vue
<div v-if="condition">Show</div>
<div v-else>Hide</div>
```

### List Rendering
```vue
<div v-for="item in items" :key="item.id">
  {{ item.name }}
</div>
```

---

## 📡 API Integration Pattern

### Service Layer
Create `resources/js/services/api.js`:
```javascript
import axios from 'axios';

export const SongService = {
  async fetchAll() {
    const response = await axios.get('/api/songs');
    return response.data;
  },
  async create(song) {
    return axios.post('/api/songs', song);
  },
  async delete(id) {
    return axios.delete(`/api/songs/${id}`);
  }
};
```

### Usage in Components
```javascript
import { SongService } from '../services/api';

const songs = ref([]);

onMounted(async () => {
  songs.value = await SongService.fetchAll();
});
```

---

## 🌓 Dark Mode

Tailwind automatically handles dark mode. Add dark mode classes:
```vue
<div class="bg-white dark:bg-[#0a0a0a]">
  <p class="text-black dark:text-white">Text</p>
</div>
```

Dark mode triggered by:
- System preference
- Browser extension
- Manual toggle (add later)

---

## 🔗 Important URLs

- **Application**: http://localhost:8000
- **Laravel Docs**: https://laravel.com/docs
- **Vue 3 Docs**: https://vuejs.org
- **Tailwind Docs**: https://tailwindcss.com
- **Vite Docs**: https://vitejs.dev

---

## 📋 Development Workflow

### Daily Development
```
1. Start servers (php artisan serve + npm run dev)
2. Open browser (http://localhost:8000)
3. Make code changes
4. See HMR reload (no refresh needed!)
5. Check browser console (F12) for errors
6. Commit changes (git commit)
```

### Building for Production
```bash
npm run build           # Create optimized build
php artisan serve       # Serve with optimized assets
```

---

## 🐛 Common Errors & Fixes

| Error | Cause | Fix |
|-------|-------|-----|
| Blank page | Vue not mounting | Check `#app` div in blade template |
| Styles missing | Tailwind not compiled | Run `npm run dev` |
| Buttons not working | Missing @click handler | Add `@click="handler"` |
| Components not found | Import missing | Add `import Component from '...'` |
| API errors | Backend not running | Run `php artisan serve` |

---

## 📝 Configuration Files

### tailwind.config.js
```javascript
export default {
  content: [
    "./resources/**/*.{vue,js}",
  ],
  theme: {
    // Custom colors defined here
  }
}
```

### vite.config.js
```javascript
export default defineConfig({
  plugins: [
    laravel(),
    vue(),
    tailwindcss(),
  ],
});
```

### package.json Scripts
```json
{
  "scripts": {
    "dev": "vite",           // Start dev server
    "build": "vite build"    // Build for production
  }
}
```

---

## 🎯 Current Status

✅ **Completed**
- Vue 3 setup with Vite
- 3 Reusable components (Button, Card, Input)
- 3 Full pages (Home, Music List, Playlist)
- Tailwind CSS dark mode
- Navigation system
- Responsive design

📋 **Next Phase** (Backend Integration)
- Create Song model & migration
- Create Playlist model & migration
- Implement API endpoints
- Connect frontend to API

---

## 💾 Save Often!

Use Git for version control:
```bash
git status              # Check changes
git add .              # Stage all files
git commit -m "message" # Commit changes
git log                # View history
```

---

## 🆘 Getting Help

1. **Check documentation files** in project root
2. **Read component comments** in Vue files
3. **Use browser DevTools** (F12) - Console tab for errors
4. **Check terminal output** for backend errors
5. **Read official docs** - Vue, Laravel, Tailwind

---

## 🚀 Ready to Build?

You have everything you need! All files are organized and documented.

**Start with:**
1. SUMMARY.md - Overview
2. GETTING_STARTED.md - Setup guide
3. Then build your features!

---

**Last Updated**: December 2025  
**Project Status**: Phase 1 Complete ✅  
**Next Phase**: Backend Integration 🔄

🎵 **Happy Coding!** 🚀
