<template>
  <div 
    class="group bg-gray-800/60 backdrop-blur-md shadow-lg rounded-2xl p-5 border border-gray-700/50 hover:border-orange-500/40 transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-orange-500/10 flex flex-col gap-5 animate-fade-in-up"
    :style="{ animationDelay: `${index * 50}ms` }"
  >
    <!-- Background Gradient Glow (Subtle) -->
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-orange-500/10 rounded-full blur-3xl group-hover:bg-orange-500/20 transition-all duration-500"></div>

    <!-- Header: Song Title with Premium Icon -->
    <div class="flex items-center gap-4 relative z-10">
      <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl shadow-lg shadow-orange-500/20 flex items-center justify-center transform group-hover:scale-105 transition-transform duration-300">
        <MusicalNoteIcon class="w-7 h-7 text-white" />
      </div>
      <div class="flex-1 min-w-0">
        <h3 class="font-bold text-white truncate text-lg group-hover:text-orange-400 transition-colors">{{ displayName }}</h3>
        <div class="flex items-center gap-2 mt-0.5">
          <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-gray-700 text-gray-300 border border-gray-600">{{ fileType }}</span>
        </div>
      </div>
    </div>

    <!-- Audio Player (Styled Wrapper) -->
    <div class="w-full relative z-10 p-1 bg-gray-900/50 rounded-xl border border-gray-700/50">
      <audio :src="music.url" controls class="w-full h-8 rounded-lg opacity-80 hover:opacity-100 transition-opacity" style="filter: sepia(20%) hue-rotate(320deg) saturate(140%);"></audio>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-2 gap-3 relative z-10">
      <button 
        @click="$emit('play', music)"
        class="col-span-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-700 hover:bg-green-600 text-white rounded-xl transition-all duration-300 group/btn border border-gray-600 hover:border-green-500/50 font-medium"
      >
        <PlayIcon class="w-5 h-5 text-green-400 group-hover/btn:text-white transition-colors" />
        <span>Play</span>
      </button>
      
      <a 
        :href="music.download_url" 
        class="col-span-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-700 hover:bg-blue-600 text-white rounded-xl transition-all duration-300 group/btn border border-gray-600 hover:border-blue-500/50 font-medium"
      >
        <ArrowDownTrayIcon class="w-5 h-5 text-blue-400 group-hover/btn:text-white transition-colors" />
        <span>Save</span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  MusicalNoteIcon,
  PlayIcon,
  ArrowDownTrayIcon,
} from '@heroicons/vue/24/solid'

const props = defineProps({ 
  music: Object,
  index: {
    type: Number,
    default: 0
  }
})
const emits = defineEmits(['play'])

// Display name without extension
const displayName = computed(() => {
  return props.music.name.replace(/\.[^/.]+$/i, '')
})

const fileType = computed(() => {
  return (props.music.name.split('.').pop() || 'mp3').toUpperCase()
})
</script>
