<template>
  <div class="min-h-screen bg-[#0f172a] text-white overflow-x-hidden selection:bg-orange-500 selection:text-white relative">
    
    <!-- Global Background Image (Expanded to full page) -->
    <div v-if="heroBackground" class="fixed inset-0 z-0 pointer-events-none">
      <img :src="heroBackground" class="w-full h-full object-cover opacity-80" />
      <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-[#0f172a]/40 to-[#0f172a] transition-all duration-1000"></div>
    </div>

    <!-- Abstract Background Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute top-0 left-1/4 w-[50vw] h-[50vw] bg-orange-500/10 rounded-full blur-[120px] mix-blend-screen animate-pulse"></div>
      <div class="absolute bottom-0 right-1/4 w-[40vw] h-[40vw] bg-amber-500/5 rounded-full blur-[100px] mix-blend-screen"></div>
    </div>

    <!-- Hero Section / Header -->
    <div 
      class="relative min-h-[85vh] flex flex-col items-center justify-center py-20 px-4 sm:px-6 lg:px-8 overflow-hidden"
    >
      <!-- Hero background removed and moved to global above -->

      <div 
        class="relative z-10 w-full max-w-6xl mx-auto text-center flex flex-col items-center mt-[-4rem] transition-all duration-700"
        :class="isLoaded ? 'opacity-100' : 'opacity-0 scale-95'"
      >
        <!-- Hero Branding -->
        <div class="mb-4 flex flex-col items-center">
           <!-- Enlarged Floating Logo Icon -->
           <div class="w-48 h-48 md:w-56 md:h-56 lg:w-64 lg:h-64 flex items-center justify-center group hover:scale-105 transition-transform duration-500 z-20">
              <img 
                v-if="brandLogo" 
                :src="brandLogo" 
                class="w-full h-full object-contain filter drop-shadow-[0_30px_50px_rgba(0,0,0,0.9)]" 
              />
              <MusicalNoteIcon v-else class="w-40 h-40 text-orange-500 filter drop-shadow-[0_20px_40px_rgba(0,0,0,0.7)]" />
           </div>
           
           <!-- Symmetrical Wordmark Spacing -->
           <div class="flex flex-col items-center mt-[-6rem] sm:mt-[-10rem] md:mt-[-13rem] lg:mt-[-17rem] xl:mt-[-20rem] z-10">
              <img 
                v-if="brandText" 
                :src="brandText" 
                class="h-[200px] sm:h-[350px] md:h-[500px] lg:h-[600px] xl:h-[700px] w-auto object-contain select-none transition-all duration-700 filter drop-shadow-[0_20px_30px_rgba(0,0,0,0.8)] drop-shadow-[0_40px_60px_rgba(0,0,0,0.5)] drop-shadow-[0_0_100px_rgba(251,146,60,0.3)]" 
              />
              <h1 v-else class="text-8xl md:text-[14rem] font-black tracking-tighter text-white leading-none">
                {{ appName }}
              </h1>
           </div>
        </div>
        
        <p class="text-xl md:text-3xl text-gray-400 max-w-4xl mx-auto leading-relaxed mb-16 font-medium opacity-90 mt-[-4rem] sm:mt-[-8rem] md:mt-[-11rem] lg:mt-[-14rem] relative z-20">
          Transform YouTube videos into high-quality MP3s instantly. <br class="hidden sm:block"/> Your personal music library, elevated hahahha.
        </p>

        <!-- Convert Input Section -->
        <div class="w-full max-w-2xl lg:max-w-3xl mx-auto transform hover:scale-[1.01] transition-transform duration-300">
          <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-amber-600 rounded-2xl blur opacity-25 group-hover:opacity-60 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative flex flex-col sm:flex-row items-stretch sm:items-center bg-gray-900/80 backdrop-blur-xl rounded-2xl border border-white/10 p-2 shadow-2xl gap-2">
              <input
                v-model="youtubeUrl"
                type="text"
                placeholder="Paste YouTube URL here..."
                class="flex-1 bg-transparent border-none text-white placeholder-gray-500 px-4 py-3 focus:outline-none text-base md:text-lg w-full"
                @keyup.enter="convertVideo"
              />
              
              <div class="flex flex-row items-center gap-2 px-2 sm:px-0">
                <!-- Album Selection -->
                <div class="flex-1 sm:flex-none flex items-center gap-2 px-3 border-l border-white/10 sm:ml-2 h-10">
                  <select 
                    v-model="selectedAlbumId"
                    class="bg-transparent text-sm text-gray-300 focus:outline-none cursor-pointer hover:text-white transition-colors w-full sm:max-w-[150px] appearance-none"
                  >
                    <option value="" class="bg-gray-900 text-white">No Album</option>
                    <option v-for="album in albums" :key="album.id" :value="album.id" class="bg-gray-900 text-white">
                      {{ album.name }}
                    </option>
                  </select>
                </div>

                <button
                  @click="convertVideo"
                  :disabled="loading || fetchingPreview"
                  class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-orange-500/25 disabled:opacity-70 disabled:cursor-not-allowed whitespace-nowrap"
                >
                  <ArrowPathIcon v-if="loading || fetchingPreview" class="w-5 h-5 animate-spin" />
                  <ArrowDownTrayIcon v-else class="w-5 h-5" />
                  <span class="text-sm md:text-base">{{ (loading || fetchingPreview) ? 'Wait...' : 'Convert' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Video Preview Section -->
          <div v-if="fetchingPreview || videoPreview" class="mt-6 animate-fade-in">
            <div class="bg-gray-900/40 backdrop-blur-xl border border-white/10 rounded-2xl p-4 overflow-hidden relative group">
              <div v-if="fetchingPreview" class="flex items-center justify-center py-8">
                <div class="flex flex-col items-center gap-3">
                   <div class="w-10 h-10 border-4 border-orange-500/20 border-t-orange-500 rounded-full animate-spin"></div>
                   <p class="text-sm text-gray-400 font-medium tracking-wide">Fetching video details...</p>
                </div>
              </div>
              
              <div v-else-if="videoPreview" class="flex flex-col sm:flex-row gap-6">
                <!-- Video Thumbnail -->
                <div class="relative flex-shrink-0 w-full sm:w-48 h-32 rounded-xl overflow-hidden shadow-2xl">
                   <img :src="videoPreview.thumbnail" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" alt="Preview"/>
                   <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors"></div>
                   <div class="absolute bottom-2 right-2 bg-black/80 px-2 py-0.5 rounded text-[10px] font-bold text-white">
                      {{ Math.floor(videoPreview.duration / 60) }}:{{ (videoPreview.duration % 60).toString().padStart(2, '0') }}
                   </div>
                </div>
                
                <!-- Video Details -->
                <div class="text-left flex-1 min-w-0 flex flex-col justify-center">
                   <p class="text-orange-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Video Found</p>
                   <h3 class="text-xl font-bold text-white truncate leading-tight mb-2 pr-4 group-hover:text-orange-200 transition-colors">
                      {{ videoPreview.title }}
                   </h3>
                   <p class="text-gray-400 text-sm flex items-center gap-1.5">
                      <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                      {{ videoPreview.uploader }}
                   </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Indicator & Status Message -->
        <div v-if="loading || conversionMessage" class="mt-8 max-w-lg mx-auto">
          <div class="bg-gray-800/80 backdrop-blur-md rounded-xl p-4 border border-orange-500/20 shadow-lg">
            <div v-if="loading">
              <div class="flex items-center justify-between mb-2">
                <span class="text-orange-400 font-medium text-sm flex items-center gap-2">
                  <ArrowPathIcon class="w-4 h-4 animate-spin" /> Processing
                </span>
                <span class="text-xs text-gray-400">Please wait...</span>
              </div>
              <div class="w-full bg-gray-700/50 rounded-full h-2 mb-3">
                <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-2 rounded-full animate-pulse w-full origin-left duration-1000"></div>
              </div>
              <pre class="text-left text-xs text-gray-400 font-mono overflow-x-auto whitespace-pre-wrap max-h-20">{{ progress || 'Initializing...' }}</pre>
            </div>
            
            <div v-if="conversionMessage && !loading" class="flex items-center gap-3 py-2">
               <div :class="conversionSuccess ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'" class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                  <CheckIcon v-if="conversionSuccess" class="w-6 h-6" />
                  <XMarkIcon v-else class="w-6 h-6" />
               </div>
               <div class="text-left">
                  <p :class="conversionSuccess ? 'text-green-400' : 'text-red-400'" class="font-bold text-sm">
                     {{ conversionSuccess ? 'Success!' : 'Error Occurred' }}
                  </p>
                  <p class="text-gray-400 text-xs">{{ conversionMessage }}</p>
               </div>
               <button @click="conversionMessage = ''" class="ml-auto text-gray-500 hover:text-white transition-colors">
                  <XMarkIcon class="w-4 h-4" />
               </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 space-y-16 mt-12">
      <!-- Recent Tracks (Spotify Style Grid) -->

      <!-- Albums Section -->
      <section v-if="albums.length" class="animate-fade-in-up" style="animation-delay: 100ms;">
        <div class="flex items-end justify-between mb-6">
          <h2 class="text-2xl font-bold text-white hover:underline cursor-pointer">Your Albums</h2>
          <button class="text-xs font-bold text-gray-400 hover:text-white uppercase tracking-wider">Show all</button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
          <div 
            v-for="album in albums" 
            :key="album.id" 
            class="group bg-[#181818] hover:bg-[#282828] p-4 rounded-lg transition-all duration-300 relative cursor-pointer"
          >
            <div class="relative aspect-square mb-4 shadow-2xl overflow-hidden rounded-md">
              <img v-if="album.cover_image" :src="album.cover_image" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full bg-gradient-to-br from-orange-600 to-amber-700 flex items-center justify-center">
                 <MusicalNoteIcon class="w-1/2 h-1/2 text-white/50" />
              </div>
              <div class="absolute bottom-2 right-2 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                <button class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center shadow-xl hover:scale-105 active:scale-95">
                   <PlayIcon class="w-6 h-6 text-white ml-1" />
                </button>
              </div>
            </div>
            <h3 class="font-bold text-white truncate text-sm mb-1">{{ album.name }}</h3>
            <p class="text-xs text-gray-400 line-clamp-1">{{ album.artist || 'Various Artists' }}</p>
          </div>
        </div>
      </section>

      <!-- All Converted Songs Table -->
      <section v-if="convertedList.length" class="animate-fade-in-up" style="animation-delay: 200ms;">
        <div class="flex items-end justify-between mb-6">
          <h2 class="text-2xl font-bold text-white hover:underline cursor-pointer">All Converted Songs</h2>
          <button class="text-xs font-bold text-gray-400 hover:text-white uppercase tracking-wider">{{ convertedList.length }} Tracks</button>
        </div>

        <div class="bg-[#181818]/50 rounded-xl overflow-hidden backdrop-blur-sm border border-white/5">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="text-gray-400 text-xs uppercase tracking-widest border-b border-white/10">
                <th class="px-6 py-4 font-medium w-12">#</th>
                <th class="px-6 py-4 font-medium">Title</th>
                <th class="px-6 py-4 font-medium hidden md:table-cell">Album</th>
                <th class="px-6 py-4 font-medium hidden sm:table-cell">Date Added</th>
                <th class="px-6 py-4 font-medium w-32"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr 
                v-for="(song, index) in convertedList" 
                :key="song.id"
                class="group hover:bg-white/5 transition-colors cursor-pointer"
                @click="play(song.url)"
              >
                <td class="px-6 py-4 text-gray-400 text-sm">
                  <span class="group-hover:hidden">{{ index + 1 }}</span>
                  <PlayIcon class="w-4 h-4 text-white hidden group-hover:block" />
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-800 rounded flex-shrink-0 flex items-center justify-center border border-white/10">
                      <MusicalNoteIcon class="w-5 h-5 text-gray-400" />
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm font-semibold text-white truncate">{{ song.title }}</p>
                      <p class="text-xs text-gray-400 truncate md:hidden">{{ song.album }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-400 hidden md:table-cell">
                  {{ song.album }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-400 hidden sm:table-cell">
                  {{ song.created_at }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a :href="song.download_url" @click.stop class="p-2 text-gray-400 hover:text-white transition-colors">
                      <ArrowDownTrayIcon class="w-5 h-5" />
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- MP3 Ready (Just Converted Prompt) -->
      <section v-if="mp3List.length" class="animate-fade-in-up">
        <div class="bg-orange-600/10 border border-orange-500/20 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
               <CheckIcon class="w-7 h-7 text-white" />
            </div>
            <div>
              <h3 class="text-xl font-bold text-white">Conversion Ready!</h3>
              <p class="text-orange-200/70 text-sm">Your files have been successfully processed and added to the library.</p>
            </div>
          </div>
          <div class="flex gap-3">
             <button @click="mp3List = []" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl transition-all border border-white/10 text-sm">
                Dismiss
             </button>
             <button @click="loadConvertedList" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-xl shadow-lg transition-all text-sm">
                Refresh Library
             </button>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { PlayIcon, ArrowDownTrayIcon, ArrowPathIcon, ClockIcon, MusicalNoteIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import axios from 'axios'
import {
  queue,
  currentTrack,
  addToQueue,
  playTrack,
  setAudioElement,
} from '../stores/audioStore.js'
import { appName, brandLogo, brandText, heroBackground, isLoaded } from '../stores/settingsStore.js'

const youtubeUrl = ref('')
const selectedAlbumId = ref('')
const albums = ref([])
const mp3List = ref([])
const recentList = ref([])
const convertedList = ref([]) 
const loading = ref(false)
const progress = ref('')
const conversionMessage = ref('')
const conversionSuccess = ref(false)
const audioEl = ref(null)

// Fetch Albums
async function fetchAlbums() {
  try {
    const res = await axios.get('/api/albums')
    albums.value = res.data
  } catch (err) {
    console.error('Failed to load albums', err)
  }
}

// Preview state
const videoPreview = ref(null)
const fetchingPreview = ref(false)

// Watch URL input for preview
let debounceTimer = null
watch(youtubeUrl, (newUrl) => {
  clearTimeout(debounceTimer)
  if (!newUrl) {
    videoPreview.value = null
    return
  }
  
  // Basic validation for URL
  if (newUrl.includes('youtube.com/') || newUrl.includes('youtu.be/')) {
    debounceTimer = setTimeout(() => {
      fetchVideoInfo(newUrl)
    }, 500)
  }
})

async function fetchVideoInfo(url) {
  fetchingPreview.value = true
  try {
    const res = await axios.post('/api/video-info', { url })
    videoPreview.value = res.data
  } catch (err) {
    console.error('Info error:', err)
    videoPreview.value = null
  } finally {
    fetchingPreview.value = false
  }
}

// Ambil daftar lagu hasil convert dari backend
async function loadConvertedList() {
  try {
    const res = await axios.get('/api/music-list')
    convertedList.value = res.data
  } catch (err) {
    console.error('Failed to load converted list', err)
  }
}

const cleanYoutubeUrl = (url) => {
  try {
    const urlObj = new URL(url)
    if (urlObj.hostname.includes('youtube.com') && urlObj.pathname === '/watch') {
      const v = urlObj.searchParams.get('v')
      if (v) return `https://www.youtube.com/watch?v=${v}`
    } else if (urlObj.hostname === 'youtu.be') {
      const v = urlObj.pathname.substring(1)
      if (v) return `https://www.youtube.com/watch?v=${v}`
    }
  } catch (e) {}
  return url
}

// Convert Video
let progressInterval = null
async function convertVideo() {
  if (!youtubeUrl.value) return alert('Please paste a YouTube URL first')
  
  const originalUrl = youtubeUrl.value
  const cleanedUrl = cleanYoutubeUrl(originalUrl)
  
  loading.value = true
  mp3List.value = []
  progress.value = 'Initializing connection...'

  progressInterval = setInterval(async () => {
    try {
      const res = await axios.get('/api/convert-progress')
      if(res.data.progress) progress.value = res.data.progress
    } catch {}
  }, 1000)

  try {
    const res = await axios.post('/api/convert', { 
      url: cleanedUrl,
      album_id: selectedAlbumId.value || null
    })
    mp3List.value = res.data.paths
    
    conversionSuccess.value = true
    conversionMessage.value = 'File has been successfully converted and added to your library.'
    
    // Refresh lists
    await loadRecentTracks()
    await loadConvertedList()
    
    // Clear input and preview
    youtubeUrl.value = ''
    videoPreview.value = null
    
  } catch (error) {
    conversionSuccess.value = false
    conversionMessage.value = error.response?.data?.message || 'Conversion failed. Please check the URL and try again.'
    console.error(error)
  } finally {
    clearInterval(progressInterval)
    loading.value = false
    progress.value = ''
  }
}

// Play via global player
function play(mp3) {
  const idx = queue.value.indexOf(mp3)
  if (idx !== -1) {
    playTrack(idx)
  } else {
    addToQueue(mp3)
    playTrack(queue.value.length - 1)
  }
}

// Load recent converted MP3 from storage
async function loadRecentTracks() {
  try {
    const res = await axios.get('/api/recent-mp3')
    recentList.value = res.data
  } catch (err) {
    console.error('Failed to load recent tracks', err)
  }
}

onMounted(() => {
  loadRecentTracks()
  loadConvertedList()
  fetchAlbums()
})
</script>

<style scoped>
/* Custom Scrollbar for this page */
::-webkit-scrollbar {
  width: 12px;
}
::-webkit-scrollbar-track {
  background: #0f172a; 
}
::-webkit-scrollbar-thumb {
  background: #282828;
  border: 3px solid #0f172a;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #383838; 
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.5s ease-out forwards;
}

tr:hover .group-hover\:block {
  display: block !important;
}
tr:hover .group-hover\:hidden {
  display: none !important;
}
</style>
