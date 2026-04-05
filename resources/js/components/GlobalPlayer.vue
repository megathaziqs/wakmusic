<template>
  <section
    v-if="queue.length"
    class="fixed bottom-0 left-0 right-0 bg-gray-800 p-4 shadow-lg border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4"
  >
    <div class="flex-1 flex flex-col sm:flex-row items-center gap-4">
      <p class="font-semibold truncate">{{ currentTrackName }}</p>
      <audio ref="playerRef" :src="currentTrack" controls class="w-full sm:w-auto" preload="metadata"></audio>
    </div>
    <div class="flex gap-2">
      <button @click="prevTrack" class="px-3 py-2 bg-orange-500 rounded-lg hover:bg-orange-600 transition">Prev</button>
      <button @click="nextTrack" class="px-3 py-2 bg-orange-500 rounded-lg hover:bg-orange-600 transition">Next</button>
      <button @click="clearQueue" class="px-3 py-2 bg-red-600 rounded-lg hover:bg-red-700 transition">Clear</button>
    </div>
  </section>
</template>

<script setup>
import { queue, currentTrack, currentTrackName, queuePlayer, prevTrack, nextTrack, clearQueue } from '../stores/audioStore.js'

const playerRef = queuePlayer
</script>
