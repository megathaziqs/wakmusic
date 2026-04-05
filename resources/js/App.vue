<template>
  <div class="min-h-screen bg-[#050505] text-[#EDEDEC] font-sans selection:bg-orange-500 selection:text-white">

    <!-- NAVBAR -->
    <nav v-if="activePage !== 'dashboard'" class="sticky top-0 z-40 backdrop-blur-md bg-black/60 border-b border-white/5 transition-all duration-300">
      <div class="px-6 flex justify-between items-center h-16">
        <!-- Logo -->
          <div 
            class="flex items-center gap-3 group cursor-pointer transition-opacity duration-300" 
            :class="isLoaded ? 'opacity-100' : 'opacity-0'"
            @click="activePage = 'home'"
          >
            <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform duration-300 overflow-hidden">
               <img v-if="brandLogo" :src="brandLogo" class="w-full h-full object-contain" />
               <MusicalNoteIcon v-else class="w-6 h-6 text-white" />
            </div>
            <h1 class="text-2xl font-black tracking-tighter">
              <img v-if="brandText" :src="brandText" class="h-50 object-contain" />
              <span v-else class="text-white">{{ appName }}</span>
            </h1>
          </div>

          <!-- Navigation -->
          <div class="flex gap-1 bg-white/5 p-1 rounded-full">
            <button
              v-for="link in navigation"
              :key="link.id"
              @click="navigate(link.page)"
              class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-200"
              :class="activePage === link.page
                ? 'bg-white text-black shadow-sm'
                : 'text-gray-400 hover:text-white hover:bg-white/5'"
            >
              {{ link.label }}
            </button>
          </div>

          <!-- User Menu -->
          <div class="flex items-center gap-3 ml-4 pl-4 border-l border-white/10">
             <div v-if="isAuthenticated" class="flex items-center gap-3">
                 <button 
                   v-if="!['dashboard', 'addAlbum'].includes(activePage)"
                   @click="navigate('dashboard')"
                   class="p-2 text-gray-500 hover:text-orange-500 transition-colors rounded-lg hover:bg-orange-500/10"
                   title="Go to Dashboard"
                 >
                    <Squares2X2Icon class="w-5 h-5" />
                 </button>
                 <div class="flex items-center gap-2">
                    <UserCircleIcon class="w-6 h-6 text-gray-400" />
                    <span class="text-sm font-medium hidden sm:block">{{ currentUser?.name }}</span>
                 </div>
                 <button @click="handleLogout" class="p-2 text-gray-500 hover:text-red-500 transition-colors rounded-lg hover:bg-red-500/10" title="Logout">
                     <ArrowRightOnRectangleIcon class="w-5 h-5" />
                 </button>
             </div>
             <button 
               v-else 
               @click="activePage = 'login'" 
               class="px-5 py-2 bg-white hover:bg-gray-200 text-black text-sm font-bold rounded-full transition-colors shadow-lg flex items-center gap-2"
             >
                 <span>Login</span>
             </button>
          </div>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="w-full pb-32">
      <transition name="fade-slide" mode="out-in">
        <component 
          :is="currentPageComponent" 
          @switch="(page) => activePage = page"
          @success="handleAuthSuccess"
        />
      </transition>
    </main>

    <!-- GLOBAL PLAYER COMPONENT (SPOTIFY STYLE) -->
    <transition name="player-slide">
      <section
        v-if="queue.length"
        class="fixed bottom-0 left-0 right-0 z-[100] bg-[#000000] border-t border-white/10 h-24"
      >
        <div class="h-full px-4 flex items-center justify-between gap-4">
          
          <!-- Left: Track Info -->
          <div class="flex items-center gap-4 w-[30%] min-w-0">
             <div class="w-14 h-14 bg-gray-800 rounded-md flex-shrink-0 flex items-center justify-center border border-white/5 shadow-2xl relative overflow-hidden group">
               <div v-if="currentTrack" class="absolute inset-0 bg-gradient-to-br from-orange-500/20 to-transparent"></div>
               <MusicalNoteIcon class="w-8 h-8 text-white/20 group-hover:scale-110 transition-transform" />
             </div>
             <div class="min-w-0 flex-1">
                <h3 class="font-bold text-white text-sm hover:underline cursor-pointer truncate">
                  {{ currentTrackName }}
                </h3>
                <p class="text-[11px] text-gray-400 font-medium hover:text-white hover:underline cursor-pointer truncate">
                  Now Playing on WakMusic
                </p>
             </div>
             <button class="p-2 text-gray-400 hover:text-orange-500 transition-colors">
               <HeartIcon class="w-5 h-5" />
             </button>
          </div>

          <!-- Center: Controls & Progress -->
          <div class="flex flex-col items-center justify-center flex-1 max-w-[40%] gap-2">
            <!-- Playback Controls -->
            <div class="flex items-center gap-5">
              <button class="text-gray-500 hover:text-white transition-colors">
                <ArrowsRightLeftIcon class="w-4 h-4" />
              </button>
              <button @click="prevTrack" class="text-gray-300 hover:text-white transition-colors">
                <BackwardIcon class="w-7 h-7" />
              </button>
              
              <button 
                @click="togglePlay" 
                class="w-10 h-10 bg-white text-black rounded-full flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition-all"
              >
                <component :is="isPlaying ? PauseIcon : PlayIcon" class="w-5 h-5 fill-current" :class="{ 'ml-0.5': !isPlaying }" />
              </button>
              
              <button @click="nextTrack" class="text-gray-300 hover:text-white transition-colors">
                <ForwardIcon class="w-7 h-7" />
              </button>
              <button class="text-gray-500 hover:text-white transition-colors">
                <ArrowPathIcon class="w-4 h-4" />
              </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="flex items-center gap-3 w-full group">
              <span class="text-[10px] text-gray-400 font-medium min-w-[32px] text-right">{{ formatTime(currentTime) }}</span>
              <div 
                class="flex-1 h-1 bg-white/10 rounded-full cursor-pointer relative group/progress"
                @click="seekTo"
              >
                <div 
                  class="h-full bg-white group-hover:bg-orange-500 rounded-full relative transition-all duration-100"
                  :style="{ width: `${progressPercent}%` }"
                >
                  <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full shadow-lg opacity-0 group-hover/progress:opacity-100 transition-opacity"></div>
                </div>
              </div>
              <span class="text-[10px] text-gray-400 font-medium min-w-[32px]">{{ formatTime(duration) }}</span>
            </div>
          </div>

          <!-- Right: Volume & Queue -->
          <div class="flex items-center justify-end gap-3 w-[30%]">
             <button class="p-2 text-gray-400 hover:text-white transition-colors">
               <MicrophoneIcon class="w-4 h-4" />
             </button>
             <button 
               @click="showQueue = !showQueue" 
               class="p-2 text-gray-400 hover:text-white transition-colors relative"
               :class="{ 'text-orange-500': showQueue }"
             >
                <QueueListIcon class="w-4 h-4" />
                <span v-if="queue.length" class="absolute top-1 right-1 flex h-3 w-3 items-center justify-center rounded-full bg-orange-500 text-[8px] font-bold text-white">
                  {{ queue.length }}
                </span>
             </button>
             <div class="flex items-center gap-2 group w-24">
                <SpeakerWaveIcon class="w-4 h-4 text-gray-400 group-hover:text-white" />
                <div 
                  class="flex-1 h-1 bg-white/10 rounded-full cursor-pointer group-hover:bg-white/20 relative"
                  @click="handleVolumeClick"
                >
                  <div 
                    class="h-full bg-white group-hover:bg-orange-500 rounded-full relative transition-all"
                    :style="{ width: `${volume * 100}%` }"
                  >
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                  </div>
                </div>
             </div>
             <button @click="clearQueue" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Close Player">
                <XMarkIcon class="w-4 h-4" />
             </button>
          </div>
        </div>

        <!-- Queue Panel (Spotify Style) -->
        <transition name="queue-fade">
          <div v-if="showQueue" class="absolute bottom-28 right-4 w-96 max-h-[70vh] bg-[#121212] rounded-xl shadow-2xl border border-white/10 overflow-hidden flex flex-col">
            <div class="p-4 border-b border-white/5 flex items-center justify-between">
              <h4 class="font-bold text-sm">Queue</h4>
              <button @click="showQueue = false" class="text-gray-400 hover:text-white"><XMarkIcon class="w-5 h-5" /></button>
            </div>
            <div class="flex-1 overflow-y-auto p-2 custom-scrollbar">
              <div 
                v-for="(track, index) in queue" 
                :key="index"
                @click="playTrack(index)"
                class="flex items-center gap-3 p-2 rounded-md cursor-pointer hover:bg-white/5"
                :class="{ 'bg-white/10': currentIndex === index }"
              >
                <div class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center flex-shrink-0">
                  <MusicalNoteIcon class="w-5 h-5 text-gray-500" />
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium truncate" :class="{ 'text-orange-500': currentIndex === index }">
                    {{ decodeURIComponent(track.split('/').pop()) }}
                  </p>
                  <p class="text-[11px] text-gray-500">WakMusic Library</p>
                </div>
              </div>
            </div>
          </div>
        </transition>

        <!-- Audio Element -->
        <audio
          ref="audioEl"
          :src="currentTrack"
          @timeupdate="updateTime"
          @loadedmetadata="setDuration"
          @ended="nextTrack"
          class="hidden"
          autoplay
        />
      </section>
    </transition>

    <footer class="text-center text-xs text-gray-500 py-8 border-t border-white/5 bg-[#050505]">
      <p>&copy; {{ new Date().getFullYear() }} WakMusic. Crafted for vibes.</p>
    </footer>

  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { 
  PlayIcon, 
  PauseIcon, 
  XMarkIcon, 
  BackwardIcon, 
  ForwardIcon,
  UserCircleIcon,
  ArrowRightOnRectangleIcon,
  Squares2X2Icon,
  QueueListIcon,
  MusicalNoteIcon,
  ArrowsRightLeftIcon,
  ArrowPathIcon,
  MicrophoneIcon,
  SpeakerWaveIcon,
  HeartIcon
} from '@heroicons/vue/24/solid'

import HomePage from './pages/HomePage.vue'
import LibraryPage from './pages/LibraryPage.vue'
import DashboardPage from './pages/DashboardPage.vue'
import AddAlbumPage from './pages/AddAlbumPage.vue'
import LoginPage from './pages/LoginPage.vue'
import RegisterPage from './pages/RegisterPage.vue'

import { fetchSettings, appName, brandLogo, brandText, isLoaded } from './stores/settingsStore.js'

import {
  queue,
  currentIndex,
  isPlaying,
  currentTrack,
  currentTrackName,
  playTrack,
  nextTrack,
  prevTrack,
  togglePlay,
  clearQueue,
  updateTime,
  setDuration,
  setAudioElement,
  currentTime,
  duration,
  formatTime,
  volume,
  setVolume
} from './stores/audioStore.js'

import { isAuthenticated, currentUser, logout } from './stores/authStore.js'

const activePage = ref('home')
const showQueue = ref(false)

const navigation = computed(() => {
  const dashboardPages = ['dashboard', 'addAlbum']
  if (dashboardPages.includes(activePage.value)) {
    return [{ id: 1, label: 'Dashboard', page: 'dashboard' }]
  }
  return [
    { id: 1, label: 'Home', page: 'home' },
    { id: 2, label: 'Library', page: 'library' },
  ]
})

const pageComponents = {
  home: HomePage,
  library: LibraryPage,
  dashboard: DashboardPage,
  addAlbum: AddAlbumPage,
  login: LoginPage,
  register: RegisterPage
}
const currentPageComponent = computed(() => pageComponents[activePage.value])

const audioEl = ref(null)

const navigate = (page) => {
  if ((page === 'dashboard' || page === 'addAlbum') && !isAuthenticated.value) {
     activePage.value = 'login'
     return
  }
  activePage.value = page
}

const handleAuthSuccess = () => { activePage.value = 'dashboard' }
const handleLogout = () => { logout(); activePage.value = 'home' }

const progressPercent = computed(() => {
  if (!duration.value) return 0
  return (currentTime.value / duration.value) * 100
})

const seekTo = (event) => {
  if (!duration.value || !audioEl.value) return
  const { left, width } = event.currentTarget.getBoundingClientRect()
  const clickX = event.clientX - left
  const percentage = clickX / width
  audioEl.value.currentTime = percentage * duration.value
}

const handleVolumeClick = (event) => {
  const { left, width } = event.currentTarget.getBoundingClientRect()
  const clickX = event.clientX - left
  const percentage = clickX / width
  setVolume(percentage)
}

onMounted(() => {
  fetchSettings()
})

watch(audioEl, (el) => {
  if (el) {
    setAudioElement(el)
    el.volume = volume.value // Sync initial volume
  }
})
</script>

<style scoped>
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-slide-enter-from { opacity: 0; transform: translateY(10px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-10px); }

.player-slide-enter-active, .player-slide-leave-active {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.player-slide-enter-from, .player-slide-leave-to { transform: translateY(100%); }

.queue-fade-enter-active, .queue-fade-leave-active { transition: all 0.3s ease; }
.queue-fade-enter-from, .queue-fade-leave-to { opacity: 0; transform: translateY(10px); }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
</style>
