# 🎨 WakMusic - Visual Component Guide

## Button Component Showcase

### Variants

#### Primary Button
```vue
<Button variant="primary">Primary Action</Button>
```
- Use for: Main CTAs, Save, Submit
- Color: `#F53003` (Orange-Red)
- Hover: Darker shade
- Dark Mode: Special red shade

#### Secondary Button
```vue
<Button variant="secondary">Secondary Action</Button>
```
- Use for: Alternative actions
- Color: `#e3e3e0` (Light Gray)
- Hover: Darker gray

#### Danger Button
```vue
<Button variant="danger">Delete</Button>
```
- Use for: Destructive actions
- Color: Red
- Hover: Darker red

#### Ghost Button
```vue
<Button variant="ghost">Learn More</Button>
```
- Use for: Navigation, low priority
- No background
- Hover: Light background
- Most subtle

#### Outline Button
```vue
<Button variant="outline">Cancel</Button>
```
- Use for: Secondary actions, cancellations
- Border-only style
- Hover: Darker border

### Sizes

```vue
<Button size="sm">Small Button</Button>     <!-- px-3 py-1.5 text-sm -->
<Button size="md">Medium Button</Button>    <!-- px-4 py-2 text-base -->
<Button size="lg">Large Button</Button>     <!-- px-6 py-3 text-lg -->
```

### Complete Examples

```vue
<!-- Home Page CTA -->
<div class="flex gap-4">
  <Button variant="primary" size="lg">Get Started</Button>
  <Button variant="outline" size="lg">Learn More</Button>
</div>

<!-- Form Actions -->
<div class="flex gap-2">
  <Button variant="primary" @click="save">Save</Button>
  <Button variant="ghost" @click="cancel">Cancel</Button>
</div>

<!-- Table Actions -->
<div class="space-x-2">
  <Button variant="ghost" size="sm">▶️ Play</Button>
  <Button variant="danger" size="sm">🗑️ Delete</Button>
</div>

<!-- Disabled State -->
<Button :disabled="isLoading">{{ isLoading ? 'Saving...' : 'Save' }}</Button>
```

---

## Card Component Showcase

### Basic Card
```vue
<Card>
  <h3 class="text-lg font-semibold">Card Title</h3>
  <p class="text-gray-600 dark:text-gray-400">Card content goes here</p>
</Card>
```

### Feature Card
```vue
<Card>
  <div class="text-3xl mb-3">🎶</div>
  <h3 class="text-xl font-semibold">Millions of Songs</h3>
  <p class="text-gray-600 dark:text-gray-400">
    Access to millions of songs from artists around the world
  </p>
</Card>
```

### Info Card with Actions
```vue
<Card>
  <div class="flex justify-between items-start mb-4">
    <div>
      <h3 class="text-lg font-semibold">Workout Mix</h3>
      <p class="text-sm text-gray-500">High energy tracks</p>
    </div>
    <span class="text-2xl">📀</span>
  </div>
  <p class="text-sm mb-4">5 songs</p>
  <div class="flex gap-2">
    <Button variant="outline" size="sm">View</Button>
    <Button variant="danger" size="sm">Delete</Button>
  </div>
</Card>
```

### Card in Grid
```vue
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <Card v-for="item in items" :key="item.id">
    <!-- Content -->
  </Card>
</div>
```

---

## Input Component Showcase

### Basic Input
```vue
<Input 
  placeholder="Enter song name"
  v-model="searchQuery"
/>
```

### Different Types
```vue
<Input type="text" placeholder="Song Title" />
<Input type="email" placeholder="Enter email" />
<Input type="password" placeholder="Enter password" />
<Input type="number" placeholder="Enter quantity" />
<Input type="date" />
<Input type="search" placeholder="Search..." />
```

### With Container
```vue
<div class="space-y-4">
  <Input placeholder="Song Title" v-model="song.title" />
  <Input placeholder="Artist Name" v-model="song.artist" />
  <Input placeholder="Album" v-model="song.album" />
  <div class="flex gap-2">
    <Button variant="primary" @click="save">Save</Button>
    <Button variant="ghost" @click="cancel">Cancel</Button>
  </div>
</div>
```

### Search Bar
```vue
<div class="flex gap-4">
  <Input placeholder="Search songs..." v-model="searchQuery" class="flex-1" />
  <Button variant="outline">🔍 Search</Button>
</div>
```

---

## Page Layout Examples

### Hero Section
```vue
<div class="bg-gradient-to-r from-[#F53003] to-[#FF6B35] rounded-lg p-12 text-white">
  <h1 class="text-4xl font-bold mb-4">Welcome to WakMusic 🎵</h1>
  <p class="text-lg opacity-90 mb-6">Your favorite music streaming platform</p>
  <div class="flex gap-4">
    <Button variant="primary" @click="handleStart">Get Started</Button>
    <Button variant="outline" @click="handleLearnMore">Learn More</Button>
  </div>
</div>
```

### Statistics Section
```vue
<div class="bg-[#F53003] text-white rounded-lg p-8">
  <h2 class="text-2xl font-bold mb-6">Platform Statistics</h2>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div v-for="stat in statistics" :key="stat.id" class="text-center">
      <div class="text-4xl font-bold">{{ stat.value }}</div>
      <div class="text-sm opacity-90">{{ stat.label }}</div>
    </div>
  </div>
</div>
```

### Table with Actions
```vue
<div class="overflow-x-auto">
  <table class="w-full">
    <thead class="bg-gray-100 dark:bg-[#3E3E3A]">
      <tr>
        <th class="px-6 py-3 text-left">Title</th>
        <th class="px-6 py-3 text-left">Artist</th>
        <th class="px-6 py-3 text-center">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <tr v-for="song in songs" :key="song.id" class="hover:bg-gray-50">
        <td class="px-6 py-4">{{ song.title }}</td>
        <td class="px-6 py-4">{{ song.artist }}</td>
        <td class="px-6 py-4 text-center space-x-2">
          <Button variant="ghost" size="sm">Play</Button>
          <Button variant="danger" size="sm">Delete</Button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Grid Layout
```vue
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <Card v-for="playlist in playlists" :key="playlist.id">
    <h3 class="text-lg font-semibold">{{ playlist.name }}</h3>
    <p class="text-sm text-gray-500">{{ playlist.description }}</p>
    <p class="text-sm mb-4">{{ playlist.songs.length }} songs</p>
    <div class="flex gap-2">
      <Button variant="outline" size="sm">View</Button>
      <Button variant="danger" size="sm">Delete</Button>
    </div>
  </Card>
</div>
```

### Modal Overlay
```vue
<div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <Card class="w-full max-w-2xl mx-4">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Modal Title</h2>
      <button @click="showModal = false" class="text-2xl">✕</button>
    </div>
    <!-- Content -->
    <Button variant="primary" @click="showModal = false">Close</Button>
  </Card>
</div>
```

---

## Color Palette

### Light Mode
```
Primary (Orange-Red):    #F53003
Background:              #FDFDFC
Text:                    #1b1b18
Border:                  #e3e3e0
Border Light:            #19140035
```

### Dark Mode
```
Primary (Red):           #F61500
Background:              #0a0a0a
Text:                    #EDEDEC
Border:                  #3E3E3A
Text Muted:              #A1A09A
```

### Semantic Colors
```
Success:  green-600
Error:    red-600
Warning:  amber-600
Info:     blue-600
```

---

## Responsive Grid Examples

### Mobile-First
```vue
<!-- Stacks on mobile, 2 columns on tablet, 3 on desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <Card v-for="item in items" :key="item.id">
    <!-- Content -->
  </Card>
</div>

<!-- 2 columns on mobile, 4 on desktop -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
  <!-- Items -->
</div>
```

---

## Dark Mode Implementation

All components automatically support dark mode through Tailwind's `dark:` prefix:

```vue
<div class="bg-white dark:bg-[#1D0002]">
  <h1 class="text-[#1b1b18] dark:text-[#EDEDEC]">Dark mode text</h1>
  <p class="text-gray-600 dark:text-gray-400">Muted text</p>
</div>
```

Dark mode is triggered by:
1. System preference: `prefers-color-scheme: dark`
2. Manual toggle (can be added to settings)
3. Tailwind's dark class on `<html>` element

---

## Spacing & Sizing

### Padding
```
p-1   = 0.25rem
p-2   = 0.5rem
p-3   = 0.75rem
p-4   = 1rem
p-6   = 1.5rem
p-8   = 2rem
p-12  = 3rem
```

### Margins
```
m-1   = 0.25rem
m-2   = 0.5rem
m-4   = 1rem
m-6   = 1.5rem
mb-4  = margin-bottom: 1rem
```

### Gaps (flexbox)
```
gap-2  = 0.5rem
gap-4  = 1rem
gap-6  = 1.5rem
```

---

## Typography

### Font Sizes
```
text-xs   = 0.75rem  (12px)
text-sm   = 0.875rem (14px)
text-base = 1rem     (16px)
text-lg   = 1.125rem (18px)
text-xl   = 1.25rem  (20px)
text-2xl  = 1.5rem   (24px)
text-3xl  = 1.875rem (30px)
text-4xl  = 2.25rem  (36px)
```

### Font Weights
```
font-light      = 300
font-normal     = 400
font-medium     = 500
font-semibold   = 600
font-bold       = 700
font-extrabold  = 800
```

### Line Heights
```
leading-tight   = 1.25
leading-snug    = 1.375
leading-normal  = 1.5
leading-relaxed = 1.625
leading-loose   = 2
```

---

## Animation & Transitions

```vue
<!-- Fade transition -->
<div class="transition-opacity duration-200">Content</div>

<!-- Scale animation -->
<div class="transition-all duration-300 hover:scale-105">Hover me</div>

<!-- Smooth color change -->
<div class="transition-colors duration-200 hover:bg-blue-600">Color</div>

<!-- Custom animation -->
<div class="animate-spin">Loading...</div>
```

---

## Border & Radius

### Border Radius
```
rounded-none  = 0px
rounded-sm    = 0.25rem  (4px)
rounded       = 0.375rem (6px)
rounded-lg    = 0.5rem   (8px)
rounded-xl    = 0.75rem  (12px)
rounded-full  = 9999px   (circle)
```

### Borders
```
border       = 1px solid
border-2     = 2px solid
border-none  = no border
```

---

## Shadow Effects

```
shadow-none  = no shadow
shadow-sm    = small shadow
shadow-md    = medium shadow
shadow-lg    = large shadow
shadow-xl    = extra large
```

---

**This visual guide covers all the UI components and patterns used in WakMusic!** 🎨
