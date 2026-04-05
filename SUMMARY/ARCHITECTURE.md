# 🏗️ WakMusic Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Browser / Client                        │
│  ┌───────────────────────────────────────────────────────┐  │
│  │           Vue 3 SPA (Single Page App)                │  │
│  │  ┌─────────────────────────────────────────────────┐ │  │
│  │  │  App.vue (Root Component)                       │ │  │
│  │  │  - Navigation Bar                              │ │  │
│  │  │  - Page Router                                 │ │  │
│  │  └─────────────────────────────────────────────────┘ │  │
│  │                                                       │  │
│  │  ┌──────────────┐ ┌──────────────┐ ┌─────────────┐  │  │
│  │  │  HomePage    │ │ MusicListPage│ │PlaylistPage│  │  │
│  │  └──────────────┘ └──────────────┘ └─────────────┘  │  │
│  │                                                       │  │
│  │  ┌────────────────────────────────────────────────┐  │  │
│  │  │         Reusable Components                    │  │  │
│  │  │  • Button.vue  • Card.vue  • Input.vue         │  │  │
│  │  └────────────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────────────┘  │
│                     ↓ (HTTP Requests)                       │
│  ┌───────────────────────────────────────────────────────┐  │
│  │        Frontend Services (Axios)                      │  │
│  │  - SongService.js                                    │  │
│  │  - PlaylistService.js                                │  │
│  │  - AuthService.js (future)                           │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                     ↓ / ↑
              (HTTPS API Calls)
                     ↓ / ↑
┌─────────────────────────────────────────────────────────────┐
│              Laravel Backend (API Server)                   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │          API Routes (routes/api.php)               │   │
│  │                                                     │   │
│  │  GET    /api/songs                                 │   │
│  │  POST   /api/songs                                 │   │
│  │  GET    /api/songs/{id}                            │   │
│  │  PUT    /api/songs/{id}                            │   │
│  │  DELETE /api/songs/{id}                            │   │
│  │                                                     │   │
│  │  GET    /api/playlists                             │   │
│  │  POST   /api/playlists                             │   │
│  │  GET    /api/playlists/{id}/songs                  │   │
│  │  POST   /api/playlists/{id}/songs/{songId}         │   │
│  │  DELETE /api/playlists/{id}                        │   │
│  │                                                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                     ↓                                       │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         Controllers (HTTP Handlers)                │   │
│  │  ┌─────────────────┐   ┌──────────────────────┐   │   │
│  │  │ SongController  │   │ PlaylistController   │   │   │
│  │  │ - index()       │   │ - index()            │   │   │
│  │  │ - store()       │   │ - store()            │   │   │
│  │  │ - show()        │   │ - getSongs()         │   │   │
│  │  │ - update()      │   │ - addSong()          │   │   │
│  │  │ - destroy()     │   │ - destroy()          │   │   │
│  │  └─────────────────┘   └──────────────────────┘   │   │
│  │                                                    │   │
│  └─────────────────────────────────────────────────────┘   │
│                     ↓                                       │
│  ┌─────────────────────────────────────────────────────┐   │
│  │    Models & Business Logic                         │   │
│  │  ┌────────────────┐   ┌───────────────────────┐   │   │
│  │  │ Song Model     │   │ Playlist Model        │   │   │
│  │  │ - id           │   │ - id                  │   │   │
│  │  │ - title        │   │ - user_id             │   │   │
│  │  │ - artist       │   │ - name                │   │   │
│  │  │ - album        │   │ - description         │   │   │
│  │  │ - duration     │   │ - songs (many-to-many)│   │   │
│  │  └────────────────┘   └───────────────────────┘   │   │
│  │                                                    │   │
│  └─────────────────────────────────────────────────────┘   │
│                     ↓                                       │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         Database Layer (Eloquent ORM)              │   │
│  │                                                     │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                     ↓ / ↑
              (SQL Queries)
                     ↓ / ↑
┌─────────────────────────────────────────────────────────────┐
│            SQLite Database                                  │
│                                                             │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │ songs table      │  │ playlists table  │                │
│  │ ┌──────────────┐ │  │ ┌──────────────┐ │                │
│  │ │ id (PK)      │ │  │ │ id (PK)      │ │                │
│  │ │ title        │ │  │ │ user_id (FK) │ │                │
│  │ │ artist       │ │  │ │ name         │ │                │
│  │ │ album        │ │  │ │ description  │ │                │
│  │ │ duration     │ │  │ │ created_at   │ │                │
│  │ │ created_at   │ │  │ │ updated_at   │ │                │
│  │ │ updated_at   │ │  │ └──────────────┘ │                │
│  │ └──────────────┘ │  │                  │                │
│  │                  │  │  playlist_song   │                │
│  │                  │  │  (Pivot Table)   │                │
│  │                  │  │  ┌────────────┐  │                │
│  │                  │  │  │ id (PK)    │  │                │
│  │                  │  │  │ playlist_id│  │                │
│  │                  │  │  │ song_id    │  │                │
│  │                  │  │  │ created_at │  │                │
│  │                  │  │  └────────────┘  │                │
│  └──────────────────┘  └──────────────────┘                │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow

### Example: Fetch Songs Flow
```
1. User clicks "Music List" in Navigation
   └→ MusicListPage.vue is displayed

2. Component mounts - calls:
   SongService.fetchAllSongs()
   └→ Makes HTTP GET /api/songs

3. Laravel receives request:
   routes/api.php → SongController@index()
   └→ Queries database for songs

4. Database returns results:
   Song::all() → JSON response

5. Frontend receives response:
   songService.fetchAllSongs()
   └→ Updates component data with ref()

6. Vue reactivity updates template:
   Songs rendered in table
   └→ User sees music list
```

### Example: Add Song Flow
```
1. User fills form and clicks "Save Song"
   └→ handleClick → addSong()

2. Frontend validates data:
   if (newSong.value.title && artist)

3. Creates HTTP request:
   SongService.createSong(songData)
   └→ Makes HTTP POST /api/songs

4. Laravel receives request:
   routes/api.php → SongController@store()
   └→ Validates input

5. Saves to database:
   Song::create($validated)
   └→ Returns created song as JSON

6. Frontend handles response:
   addSong() → songs.value.push(newSong)
   └→ Updates local state

7. Component updates:
   Template re-renders
   └→ New song appears in table + Alert shown
```

## Component Hierarchy

```
App.vue (Root)
├── Navigation (Top bar with page links)
├── MainContent
│   ├── HomePage
│   │   ├── Card (Feature Cards)
│   │   └── Button (CTA Buttons)
│   │
│   ├── MusicListPage
│   │   ├── Button (Add Song button)
│   │   ├── Card (Add form container)
│   │   ├── Input (Title, Artist, Album)
│   │   ├── Button (Save, Cancel)
│   │   ├── Input (Search)
│   │   ├── Button (Search)
│   │   └── Table (Songs list)
│   │       └── Button (Play, Delete)
│   │
│   └── PlaylistPage
│       ├── Button (Create Playlist)
│       ├── Card (Create form)
│       ├── Input (Name, Description)
│       ├── Button (Create, Cancel)
│       ├── Grid (Playlist cards)
│       │   └── Card
│       │       ├── Button (View, Delete)
│       │       └── (Playlist details)
│       └── Modal (Playlist Songs)
│           └── Button (Remove)
```

## State Management Pattern

### Current (Phase 1)
```
Component Level State
├── Each page manages its own state
├── Using Vue Composition API with ref()
└── No global state yet
```

### Future (Phase 3+)
```
Pinia Store Pattern
├── songStore
│   ├── state: { songs, currentSong, loading }
│   ├── getters: { getAllSongs(), getSongById() }
│   └── actions: { fetchSongs(), createSong(), deleteSong() }
│
├── playlistStore
│   ├── state: { playlists, currentPlaylist }
│   ├── getters: { getAllPlaylists() }
│   └── actions: { fetchPlaylists(), createPlaylist() }
│
└── userStore
    ├── state: { user, isAuthenticated }
    ├── getters: { getUser() }
    └── actions: { login(), register(), logout() }
```

## API Contract

### Song Endpoints
```
GET /api/songs
Response: { data: [...], total: 5 }

POST /api/songs
Body: { title, artist, album }
Response: { message, data: {...} }

GET /api/songs/{id}
Response: { id, title, artist, album, duration }

PUT /api/songs/{id}
Body: { title, artist, album }
Response: { message }

DELETE /api/songs/{id}
Response: { message }
```

### Playlist Endpoints
```
GET /api/playlists
Response: { data: [...] }

POST /api/playlists
Body: { name, description }
Response: { message, data: {...} }

GET /api/playlists/{id}/songs
Response: { id, name, songs: [...] }

POST /api/playlists/{id}/songs/{songId}
Response: { message }

DELETE /api/playlists/{id}
Response: { message }
```

## Key Design Patterns Used

1. **Component Composition** - Reusable Button, Card, Input
2. **Page-based Routing** - Computed property to switch components
3. **Service Layer** - Separation of API calls
4. **Reactive State** - Vue ref() for local state
5. **Event Handling** - @click, @submit for user actions
6. **Props & Events** - Component communication
7. **Tailwind Utilities** - Utility-first CSS

## Technology Stack Summary

- **Frontend Framework**: Vue 3 (Composition API)
- **Build Tool**: Vite 7
- **Styling**: Tailwind CSS 4
- **HTTP Client**: Axios
- **Backend Framework**: Laravel 12
- **ORM**: Eloquent
- **Database**: SQLite (development)
- **Package Manager**: npm + composer
- **Dev Server**: Vite HMR + Laravel Artisan

---

**This architecture provides a solid foundation for building modern web applications with Vue 3 and Laravel!** 🚀
