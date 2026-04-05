<template>
  <div class="min-h-screen bg-[#0f172a] text-white">
    <!-- Header/Hero -->
    <div class="relative bg-gradient-to-br from-orange-600 via-orange-500 to-amber-600 shadow-2xl overflow-hidden mb-8">
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/10 blur-3xl mix-blend-overlay animate-pulse"></div>
      </div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md shadow-inner border border-white/30 transform -rotate-3 hover:rotate-0 transition-transform duration-300">
              <PlusCircleIcon class="w-9 h-9 text-white drop-shadow-md" />
            </div>
            <div>
              <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight drop-shadow-sm">Add Album</h1>
              <p class="text-orange-100 mt-2 text-lg font-medium opacity-90">Create and manage your music collections</p>
            </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-gray-800/50 border border-gray-700/50 rounded-3xl p-8 backdrop-blur-md shadow-2xl">
        <div class="text-center py-12">
           <div class="inline-flex p-4 bg-orange-500/10 rounded-full mb-4">
             <MusicalNoteIcon class="w-12 h-12 text-orange-500" />
           </div>
           <h3 class="text-xl font-bold text-white mb-2">Module Under Construction</h3>
           <p class="text-gray-400">The album creation module will be available here.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { PlusCircleIcon, MusicalNoteIcon } from '@heroicons/vue/24/solid'
</script>
