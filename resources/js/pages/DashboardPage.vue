<template>
  <div class="flex h-screen bg-[#0f172a] text-white font-sans overflow-hidden">
    
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 relative bg-gray-900/80 backdrop-blur-2xl border-r border-white/5 flex flex-col z-20 transition-all duration-300">
      <!-- Logo Area -->
      <div class="h-24 flex items-center px-8 border-b border-white/5">
        <div 
          class="flex items-center gap-3 cursor-pointer group hover:opacity-80 transition-all" 
          @click="emit('switch', 'home')"
        >
           <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform duration-300 overflow-hidden">
             <img v-if="brandLogo" :src="brandLogo" class="w-full h-full object-contain" />
             <MusicalNoteIcon v-else class="w-6 h-6 text-white" />
           </div>
           <div class="min-w-0">
             <img v-if="brandText" :src="brandText" class="h-8 object-contain" />
             <span v-else class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 truncate block">{{ appName }}</span>
           </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto custom-scrollbar">
        <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Menu</p>
        
        <button 
          v-for="item in navItems" 
          :key="item.id"
          @click="currentModule = item.id"
          :class="[
            'w-full flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 group relative overflow-hidden',
            currentModule === item.id 
              ? 'bg-gradient-to-r from-orange-600/20 to-amber-600/20 text-orange-400 shadow-lg shadow-orange-900/10 border border-orange-500/10' 
              : 'text-gray-400 hover:text-white hover:bg-white/5'
          ]"
        >
          <!-- Active Indicator -->
          <div v-if="currentModule === item.id" class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-amber-500"></div>
          
          <component :is="item.icon" :class="[
            'w-6 h-6 transition-colors',
            currentModule === item.id ? 'text-orange-400' : 'text-gray-500 group-hover:text-white'
          ]" />
          <span class="font-medium tracking-wide">{{ item.label }}</span>
        </button>
      </nav>

      <!-- User / Logout -->
      <div class="p-4 border-t border-white/5">
        <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:text-red-400 hover:bg-red-500/5 transition-all group">
          <ArrowLeftOnRectangleIcon class="w-6 h-6 group-hover:scale-110 transition-transform" />
          <span class="font-medium">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="flex-1 relative overflow-y-auto overflow-x-hidden bg-[#0f172a]">
      <!-- Background Ambience -->
      <div class="fixed inset-0 pointer-events-none">
         <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-orange-500/10 blur-[100px] rounded-full mix-blend-screen opacity-50"></div>
         <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-amber-600/10 blur-[100px] rounded-full mix-blend-screen opacity-50"></div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-6 py-8">
        
        <!-- MODULE: OVERVIEW -->
        <div v-if="currentModule === 'overview'" class="space-y-8 animate-fade-in">
          <!-- Welcome Banner -->
          <div class="relative bg-gradient-to-br from-orange-600 via-orange-500 to-amber-600 rounded-3xl p-8 sm:p-12 shadow-2xl overflow-hidden border border-white/10">
             <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/10 rounded-full blur-3xl mix-blend-overlay"></div>
             <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-black/10 rounded-full blur-3xl mix-blend-overlay"></div>
             
             <div class="relative z-10">
               <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight mb-2">Welcome Back!</h1>
               <p class="text-orange-100 text-lg opacity-90 max-w-xl">
                 Here's what's happening with your music library today. Check out your stats and recent activity.
               </p>
             </div>
          </div>

          <!-- Quick Actions -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button @click="currentModule = 'converter'; setConverterMode('url')" class="flex flex-col items-center justify-center p-6 bg-orange-600/10 border border-orange-500/20 rounded-2xl hover:bg-orange-600/20 transition-all group">
              <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform">
                <BoltIcon class="w-6 h-6 text-white" />
              </div>
              <span class="text-sm font-bold text-orange-200">Convert Link</span>
            </button>
            <button @click="currentModule = 'converter'; setConverterMode('source')" class="flex flex-col items-center justify-center p-6 bg-blue-600/10 border border-blue-500/20 rounded-2xl hover:bg-blue-600/20 transition-all group">
              <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                <PlusCircleIcon class="w-6 h-6 text-white" />
              </div>
              <span class="text-sm font-bold text-blue-200">Video Source</span>
            </button>
            <button @click="openAlbumModal()" class="flex flex-col items-center justify-center p-6 bg-amber-600/10 border border-amber-500/20 rounded-2xl hover:bg-amber-600/20 transition-all group">
              <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                <FolderIcon class="w-6 h-6 text-white" />
              </div>
              <span class="text-sm font-bold text-amber-200">New Album</span>
            </button>
            <button @click="currentModule = 'library'" class="flex flex-col items-center justify-center p-6 bg-purple-600/10 border border-purple-500/20 rounded-2xl hover:bg-purple-600/20 transition-all group">
              <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-purple-500/20 group-hover:scale-110 transition-transform">
                <MusicalNoteIcon class="w-6 h-6 text-white" />
              </div>
              <span class="text-sm font-bold text-purple-200">My Library</span>
            </button>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Stats -->
            <div class="lg:col-span-1 space-y-6">
              <h3 class="text-xl font-bold text-white flex items-center gap-2 px-1">
                <Squares2X2Icon class="w-5 h-5 text-orange-500" />
                Library Insights
              </h3>
              <div class="grid grid-cols-1 gap-4">
                <div v-for="(stat, idx) in statsCards" :key="idx" 
                  class="bg-gray-800/40 backdrop-blur-xl border border-white/5 p-6 rounded-2xl shadow-xl hover:bg-gray-800/60 transition-all group"
                >
                  <div class="flex items-center justify-between mb-4">
                    <p class="text-gray-400 font-medium text-sm">{{ stat.title }}</p>
                    <div :class="`p-2 rounded-lg ${stat.bgClass}`">
                      <component :is="stat.icon" :class="`w-6 h-6 ${stat.iconClass}`" />
                    </div>
                  </div>
                  <p class="text-3xl font-bold text-white">{{ stat.value }}</p>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-2 space-y-6">
              <div class="flex items-center justify-between px-1">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                  <ClockIcon class="w-5 h-5 text-orange-500" />
                  Recent Activity
                </h3>
                <button @click="currentModule = 'library'" class="text-orange-400 text-sm font-medium hover:underline">View All</button>
              </div>
              
              <div class="bg-gray-800/40 backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden divide-y divide-white/5 shadow-xl">
                <template v-if="recentActivity.length > 0">
                   <div v-for="(item, i) in recentActivity" :key="i" class="flex items-center gap-4 p-4 hover:bg-white/5 transition-colors group">
                      <div class="w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-orange-500/20 transition-colors">
                         <MusicalNoteIcon class="w-6 h-6 text-orange-400" />
                      </div>
                      <div class="flex-1 min-w-0">
                         <h4 class="text-white font-bold truncate group-hover:text-orange-400 transition-colors">{{ item.title }}</h4>
                         <div class="flex items-center gap-2 text-xs text-gray-500">
                             <span class="bg-white/5 px-2 py-0.5 rounded text-[10px] font-medium border border-white/5">{{ item.album }}</span>
                             <span>•</span>
                             <span>{{ item.created_at }}</span>
                         </div>
                      </div>
                      <div class="flex items-center gap-2">
                         <a :href="item.url" target="_blank" class="p-2 bg-white/5 hover:bg-orange-500/10 text-gray-400 hover:text-orange-400 rounded-lg transition-all" title="Play">
                            <PlusCircleIcon class="w-5 h-5 rotate-45 transform" />
                         </a>
                      </div>
                   </div>
                </template>
                <div v-else class="py-20 text-center">
                   <div class="w-16 h-16 bg-gray-900/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                      <ClockIcon class="w-8 h-8 text-gray-700" />
                   </div>
                   <p class="text-gray-500 font-medium">No recent activity detected</p>
                   <button @click="currentModule = 'converter'" class="mt-4 text-orange-500 text-sm font-bold hover:underline">Start Converting Now</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODULE: ALBUMS -->
        <div v-if="currentModule === 'albums'" class="animate-fade-in space-y-6">
           <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                 <h2 class="text-3xl font-bold text-white">Album Management</h2>
                 <p class="text-gray-400 text-sm mt-1">Create and manage your music collections</p>
              </div>
              <button 
                @click="openAlbumModal()"
                class="px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold rounded-xl shadow-lg transition-all flex items-center gap-2"
              >
                <PlusIcon class="w-5 h-5" />
                <span>New Album</span>
              </button>
           </div>

           <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="album in albums" :key="album.id" class="bg-gray-800/40 border border-white/5 rounded-2xl overflow-hidden backdrop-blur-xl group hover:bg-gray-800/60 transition-all">
                 <div class="aspect-video relative overflow-hidden bg-gray-900 flex items-center justify-center">
                    <img v-if="album.cover_image" :src="album.cover_image" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" />
                    <MusicalNoteIcon v-else class="w-12 h-12 text-gray-700" />
                 </div>
                 <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-1">{{ album.name }}</h3>
                    <p class="text-gray-400 text-sm mb-4">{{ album.artist || 'Various Artists' }}</p>
                    <div class="flex gap-2">
                       <button @click="openAlbumModal(album)" class="flex-1 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-colors flex items-center justify-center gap-2 text-sm font-medium border border-white/5">
                          <PencilIcon class="w-4 h-4" /> Edit
                       </button>
                       <button @click="handleDeleteAlbum(album.id)" class="py-2 px-4 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg transition-colors border border-red-500/20">
                          <TrashIcon class="w-4 h-4" />
                       </button>
                    </div>
                 </div>
              </div>
              <div v-if="albums.length === 0" class="col-span-full py-20 text-center bg-gray-900/30 rounded-3xl border border-dashed border-white/5">
                 <MusicalNoteIcon class="w-12 h-12 text-gray-700 mx-auto mb-4" />
                 <p class="text-gray-500">No albums created yet.</p>
              </div>
           </div>
        </div>

        <!-- MODULE: CONVERTER -->
        <div v-if="currentModule === 'converter'" class="animate-fade-in max-w-5xl mx-auto pt-6 space-y-6">
           <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
              <div>
                 <h2 class="text-4xl font-black text-white tracking-tight">Converter</h2>
                 <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold uppercase tracking-widest">
                    <span class="px-3 py-1 rounded-full bg-orange-500/15 text-orange-200 border border-orange-500/20">URL</span>
                    <span class="px-3 py-1 rounded-full bg-sky-500/15 text-sky-200 border border-sky-500/20">Computer</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-200 border border-emerald-500/20">Drive</span>
                 </div>
              </div>
              <div class="grid grid-cols-3 gap-3 text-center">
                 <div class="bg-gray-900/60 border border-white/10 rounded-xl px-4 py-3">
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Output</p>
                    <p class="text-white font-bold">{{ selectedFormat.toUpperCase() }}</p>
                 </div>
                 <div class="bg-gray-900/60 border border-white/10 rounded-xl px-4 py-3">
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Limit</p>
                    <p class="text-white font-bold">{{ MAX_UPLOAD_SIZE_MB }} MB</p>
                 </div>
                 <div class="bg-gray-900/60 border border-white/10 rounded-xl px-4 py-3">
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Mode</p>
                    <p class="text-white font-bold">{{ converterMode === 'url' ? 'URL' : 'Source' }}</p>
                 </div>
              </div>
           </div>

           <div class="bg-gray-900/70 border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
              <div class="grid grid-cols-2 border-b border-white/10">
                 <button
                   @click="setConverterMode('url')"
                   class="h-16 flex items-center justify-center gap-3 text-sm font-black uppercase tracking-widest transition-all"
                   :class="converterMode === 'url' ? 'bg-orange-600 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white'"
                 >
                   <LinkIcon class="w-5 h-5" />
                   URL
                 </button>
                 <button
                   @click="setConverterMode('source')"
                   class="h-16 flex items-center justify-center gap-3 text-sm font-black uppercase tracking-widest transition-all"
                   :class="converterMode === 'source' ? 'bg-sky-600 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white'"
                 >
                   <VideoCameraIcon class="w-5 h-5" />
                   Video Source
                 </button>
              </div>

              <div class="p-5 sm:p-7 space-y-6">
                 <div v-if="converterMode === 'url'" class="space-y-3 animate-fade-in">
                    <label for="converter-url" class="text-xs font-black text-gray-500 uppercase tracking-widest">Paste URL</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                       <div class="relative flex-1 group">
                          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                             <BoltIcon class="h-5 w-5 text-gray-500 group-focus-within:text-orange-400 transition-colors" />
                          </div>
                          <input 
                            id="converter-url"
                            v-model="youtubeUrl"
                            type="text" 
                            placeholder="https://youtube.com/..."
                            class="w-full h-14 bg-gray-950/70 border border-gray-700 rounded-xl pl-11 pr-4 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-medium"
                            @keyup.enter.prevent="convertVideo"
                          />
                       </div>
                       <button 
                          @click="convertVideo"
                          :disabled="converting || fetchingPreview || !youtubeUrl"
                          class="h-14 px-7 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap"
                       >
                          <span v-if="converting || fetchingPreview" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                          <span>{{ (converting || fetchingPreview) ? 'Converting' : `Convert ${selectedFormat.toUpperCase()}` }}</span>
                       </button>
                    </div>
                 </div>

                 <div v-else class="space-y-5 animate-fade-in">
                    <div class="grid grid-cols-2 gap-2 bg-gray-950/70 border border-gray-800 rounded-xl p-1.5">
                       <button
                         @click="setSourceMode('file')"
                         class="h-11 flex items-center justify-center gap-2 rounded-lg font-bold text-sm transition-all"
                         :class="sourceMode === 'file' ? 'bg-sky-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                       >
                         <ComputerDesktopIcon class="w-5 h-5" />
                         Computer
                       </button>
                       <button
                         @click="setSourceMode('cloud')"
                         class="h-11 flex items-center justify-center gap-2 rounded-lg font-bold text-sm transition-all"
                         :class="sourceMode === 'cloud' ? 'bg-emerald-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                       >
                         <CloudArrowUpIcon class="w-5 h-5" />
                         Google Drive
                       </button>
                    </div>

                    <div v-if="sourceMode === 'file'" class="space-y-3">
                       <label for="source-file" class="text-xs font-black text-gray-500 uppercase tracking-widest">Computer Video</label>
                       <div class="flex flex-col sm:flex-row gap-3">
                          <div class="relative flex-1 group">
                             <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <PlusCircleIcon class="h-5 w-5 text-gray-500 group-focus-within:text-sky-400 transition-colors" />
                             </div>
                             <input 
                               id="source-file"
                               :key="fileInputKey"
                               type="file" 
                               accept="video/*"
                               @change="handleFileSelect"
                               class="w-full h-14 bg-gray-950/70 border border-gray-700 rounded-xl pl-11 pr-4 text-white file:hidden cursor-pointer focus:outline-none focus:border-sky-500 transition-all"
                             />
                             <div class="absolute inset-y-0 left-11 right-4 flex items-center pointer-events-none text-gray-400 font-medium truncate">
                                {{ selectedVideoFile ? `${selectedVideoFile.name} - ${formatFileSize(selectedVideoFile.size)}` : 'Choose video file' }}
                             </div>
                          </div>
                          <button 
                             @click="convertSourceVideo"
                             :disabled="converting || !selectedVideoFile"
                             class="h-14 px-7 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl shadow-lg shadow-sky-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap"
                          >
                             <span v-if="converting" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                             <span>{{ converting ? 'Converting' : `Convert ${selectedFormat.toUpperCase()}` }}</span>
                          </button>
                       </div>
                    </div>

                    <div v-else class="space-y-3">
                       <label for="source-url" class="text-xs font-black text-gray-500 uppercase tracking-widest">Google Drive / Cloud Link</label>
                       <div class="flex flex-col sm:flex-row gap-3">
                          <div class="relative flex-1 group">
                             <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <CloudArrowUpIcon class="h-5 w-5 text-gray-500 group-focus-within:text-emerald-400 transition-colors" />
                             </div>
                             <input
                               id="source-url"
                               v-model="sourceUrl"
                               type="text"
                               placeholder="https://drive.google.com/..."
                               class="w-full h-14 bg-gray-950/70 border border-gray-700 rounded-xl pl-11 pr-4 text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-medium"
                               @keyup.enter.prevent="convertSourceVideo"
                             />
                          </div>
                          <button 
                             @click="convertSourceVideo"
                             :disabled="converting || !sourceUrl"
                             class="h-14 px-7 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap"
                          >
                             <span v-if="converting" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                             <span>{{ converting ? 'Converting' : `Convert ${selectedFormat.toUpperCase()}` }}</span>
                          </button>
                       </div>
                    </div>
                 </div>

                 <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="space-y-2">
                       <label class="text-xs font-black text-gray-500 uppercase tracking-widest">Format</label>
                       <div class="grid grid-cols-2 gap-2 bg-gray-950/70 border border-gray-800 rounded-xl p-1.5 h-[54px]">
                          <button 
                            @click="selectedFormat = 'mp3'"
                            class="flex items-center justify-center gap-2 rounded-lg transition-all font-bold text-sm"
                            :class="selectedFormat === 'mp3' ? 'bg-orange-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                          >
                             <MusicalNoteIcon class="w-4 h-4" />
                             MP3
                          </button>
                          <button 
                            @click="selectedFormat = 'mp4'"
                            class="flex items-center justify-center gap-2 rounded-lg transition-all font-bold text-sm"
                            :class="selectedFormat === 'mp4' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                          >
                             <VideoCameraIcon class="w-4 h-4" />
                             MP4
                          </button>
                       </div>
                    </div>

                    <div class="space-y-2">
                       <label for="converter-album" class="text-xs font-black text-gray-500 uppercase tracking-widest">Save to Album</label>
                       <select 
                         id="converter-album"
                         v-model="selectedAlbumId"
                         class="w-full bg-gray-950/70 border border-gray-700 rounded-xl px-4 h-[54px] text-white focus:outline-none focus:border-orange-500 transition-all font-medium"
                       >
                         <option value="">None (Single Track)</option>
                         <option v-for="album in albums" :key="album.id" :value="album.id">{{ album.name }}</option>
                       </select>
                    </div>
                 </div>

                 <div v-if="fetchingPreview || videoPreview" class="animate-fade-in">
                    <div class="bg-gray-950/70 border border-white/10 rounded-xl p-4 flex flex-col sm:flex-row gap-5 overflow-hidden">
                       <div v-if="fetchingPreview" class="w-full py-8 flex flex-col items-center justify-center gap-3">
                          <div class="w-8 h-8 border-2 border-orange-500/20 border-t-orange-500 rounded-full animate-spin"></div>
                          <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Searching Video</p>
                       </div>
                       
                       <template v-else-if="videoPreview">
                          <div class="relative w-full sm:w-44 h-28 rounded-lg overflow-hidden shadow-xl flex-shrink-0">
                             <img :src="previewThumbnail(videoPreview)" class="w-full h-full object-cover" alt="Thumbnail"/>
                             <div class="absolute bottom-2 right-2 bg-black/80 px-1.5 py-0.5 rounded text-[10px] font-bold text-white">
                                {{ Math.floor(videoPreview.duration / 60) }}:{{ (videoPreview.duration % 60).toString().padStart(2, '0') }}
                             </div>
                          </div>
                          
                          <div class="flex-1 min-w-0 flex flex-col justify-center">
                             <span class="text-orange-500 text-[10px] font-black uppercase tracking-widest mb-1">Preview</span>
                             <h4 class="text-lg font-bold text-white truncate leading-tight mb-1">{{ videoPreview.title }}</h4>
                             <p class="text-gray-400 text-sm flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                {{ videoPreview.uploader }}
                             </p>
                          </div>
                       </template>
                    </div>
                 </div>

                 <transition name="fade">
                   <div v-if="converting || conversionMessage" class="bg-gray-950/70 rounded-xl p-4 border border-white/10">
                      <div v-if="converting" class="mb-2">
                         <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 w-1/2 animate-progress origin-left"></div>
                         </div>
                         <p class="text-xs text-orange-300 mt-2 font-medium animate-pulse">{{ conversionProgressLabel }}</p>
                      </div>
                      <div v-if="conversionMessage" class="flex items-center gap-2">
                         <CheckIcon v-if="conversionSuccess" class="w-5 h-5 text-green-400" />
                         <XMarkIcon v-else class="w-5 h-5 text-red-400" />
                         <span :class="conversionSuccess ? 'text-green-400' : 'text-red-400'" class="font-medium text-sm">
                            {{ conversionMessage }}
                         </span>
                      </div>
                   </div>
                 </transition>
              </div>
           </div>
        </div>

        <!-- MODULE: PREFERENCES -->
        <div v-if="currentModule === 'preferences'" class="animate-fade-in space-y-8 max-w-4xl mx-auto pt-4">
           <div class="flex items-center gap-6 mb-10">
              <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-orange-500/20">
                 <Cog6ToothIcon class="w-10 h-10 text-white" />
              </div>
              <div>
                 <h2 class="text-4xl font-black text-white tracking-tight">Preferences</h2>
                 <p class="text-gray-400 font-medium">Customize your WakMusic experience & branding.</p>
              </div>
           </div>

           <div class="grid grid-cols-1 gap-8">
              <!-- Branding Settings -->
              <div class="bg-gray-800/40 border border-white/5 rounded-3xl p-8 backdrop-blur-xl shadow-2xl space-y-8">
                 <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <PhotoIcon class="w-6 h-6 text-orange-500" /> Branding & Identity
                 </h3>
                 
                 <div class="space-y-6">
                    <div>
                       <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3" for="pref-app-name">Application Name</label>
                       <input 
                         id="pref-app-name"
                         v-model="appSettings.app_name" 
                         type="text" 
                         placeholder="e.g. WakMusic"
                         class="w-full bg-gray-900/60 border border-white/10 rounded-xl px-4 py-4 text-white focus:outline-none focus:border-orange-500 transition-all font-bold text-lg"
                       />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                       <!-- Logo Upload -->
                       <div class="space-y-4">
                          <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Logo Icon (WM.svg)</label>
                          <div class="flex flex-col gap-4">
                             <div class="w-24 h-24 bg-gray-900 rounded-2xl flex items-center justify-center border border-white/5 overflow-hidden shadow-inner group relative">
                                <img v-if="appSettings.brand_logo" :src="appSettings.brand_logo" class="w-full h-full object-contain p-4" />
                                <MusicalNoteIcon v-else class="w-10 h-10 text-gray-800" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                   <label class="cursor-pointer p-2 bg-white/10 rounded-full hover:bg-white/20 transition-colors">
                                      <PlusIcon class="w-5 h-5 text-white" />
                                      <input type="file" @change="(e) => handleLogoUpload(e, 'brand_logo')" class="hidden" />
                                   </label>
                                </div>
                             </div>
                             <p class="text-[10px] text-gray-500 font-medium leading-tight">Recommended: Square SVG or PNG with transparent background.</p>
                          </div>
                       </div>

                       <!-- Wordmark Upload -->
                       <div class="space-y-4">
                          <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Brand Wordmark (WakkMusic.svg)</label>
                          <div class="flex flex-col gap-4">
                             <div class="w-full h-24 bg-gray-900 rounded-2xl flex items-center justify-center border border-white/5 overflow-hidden shadow-inner group relative">
                                <img v-if="appSettings.brand_text" :src="appSettings.brand_text" class="w-full h-full object-contain p-4" />
                                <span v-else class="text-xl font-black text-gray-800">WakkMusic</span>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                   <label class="cursor-pointer p-2 bg-white/10 rounded-full hover:bg-white/20 transition-colors">
                                      <PlusIcon class="w-5 h-5 text-white" />
                                      <input type="file" @change="(e) => handleLogoUpload(e, 'brand_text')" class="hidden" />
                                   </label>
                                </div>
                             </div>
                             <p class="text-[10px] text-gray-500 font-medium leading-tight">Recommended: Horizontal SVG with transparent background.</p>
                          </div>
                       </div>
                    </div>

                    <!-- Hero Background Upload -->
                    <div class="space-y-4 pt-4">
                       <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Hero Background Image</label>
                       <div class="flex flex-col gap-4">
                          <div class="w-full h-48 bg-gray-900 rounded-2xl flex items-center justify-center border border-white/5 overflow-hidden shadow-inner group relative">
                             <img v-if="appSettings.hero_background" :src="appSettings.hero_background" class="w-full h-full object-cover" />
                             <div v-else class="flex flex-col items-center gap-2 text-gray-700">
                                <PhotoIcon class="w-12 h-12" />
                                <span class="text-xs font-bold">No background image set</span>
                             </div>
                             <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <label class="cursor-pointer p-4 bg-white/10 rounded-full hover:bg-white/20 transition-colors flex items-center gap-2">
                                   <PlusIcon class="w-6 h-6 text-white" />
                                   <span class="text-white font-bold text-sm">Upload Background</span>
                                   <input type="file" @change="(e) => handleLogoUpload(e, 'hero_background')" class="hidden" />
                                </label>
                             </div>
                          </div>
                          <p class="text-[10px] text-gray-500 font-medium leading-tight">Recommended: High resolution landscape image (1920x1080+). This will be displayed on the Home Page with an overlay.</p>
                       </div>
                    </div>

                    <div class="pt-8 border-t border-white/5 flex justify-end">
                       <button 
                         @click="savePreferences"
                         :disabled="savingPrefs"
                         class="px-10 py-4 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-2xl transition-all flex items-center gap-3 disabled:opacity-50"
                       >
                          <ArrowPathIcon v-if="savingPrefs" class="w-4 h-4 animate-spin" />
                          <CheckIcon v-else class="w-4 h-4" />
                          <span>{{ savingPrefs ? 'Applying...' : 'Save Preferences' }}</span>
                       </button>
                    </div>
                 </div>
              </div>
           </div>
        </div>

        <!-- MODULE: LIBRARY -->
        <div v-if="currentModule === 'library'" class="animate-fade-in space-y-6">
           <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                 <h2 class="text-3xl font-bold text-white">Music Library</h2>
                 <p class="text-gray-400 text-sm mt-1">Manage your collection of {{ musicList.length }} songs</p>
              </div>
              
              <!-- Search Bar -->
              <div class="relative group w-full md:w-96">
                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-500 group-focus-within:text-orange-400 transition-colors" />
                 </div>
                 <input 
                   v-model="searchQuery"
                   type="text" 
                   placeholder="Search files..." 
                   class="w-full bg-gray-900/50 border border-gray-700/50 rounded-xl pl-10 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/50 transition-all backdrop-blur-sm"
                 />
              </div>
           </div>

           <!-- Library List -->
           <div class="bg-gray-800/40 border border-white/5 rounded-3xl overflow-hidden backdrop-blur-xl shadow-xl min-h-[500px]">
              <div class="overflow-x-auto">
                 <table class="w-full text-left">
                    <thead class="bg-white/5 text-gray-400 text-xs uppercase tracking-wider font-semibold border-b border-white/5">
                       <tr>
                          <th class="px-6 py-4">Track Name</th>
                          <th class="px-6 py-4 w-32 text-center">Actions</th>
                       </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                       <tr v-for="music in filteredMusicList" :key="music.name" class="hover:bg-white/5 transition-colors group">
                          <td class="px-6 py-4">
                             <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-gray-700/50 flex items-center justify-center text-gray-400 group-hover:text-orange-400 group-hover:bg-orange-500/10 transition-all">
                                   <MusicalNoteIcon class="w-5 h-5" />
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                   <!-- View Mode -->
                                   <div v-if="editingFile !== music.name" class="font-medium text-gray-200 truncate">
                                      {{ music.name }}
                                   </div>
                                   <!-- Edit Mode -->
                                   <div v-else class="flex items-center gap-2">
                                      <input 
                                         v-model="newName"
                                         @keyup.enter="saveRename(music.name)"
                                         @keyup.esc="cancelRename"
                                         class="bg-gray-900 text-white px-2 py-1 rounded border border-orange-500 focus:outline-none w-full"
                                         autoFocus
                                      />
                                   </div>
                                </div>
                             </div>
                          </td>
                          <td class="px-6 py-4">
                             <div class="flex items-center justify-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <template v-if="editingFile !== music.name">

                                    <button @click="downloadMusic(music.name)" class="p-2 hover:bg-green-500/10 hover:text-green-400 rounded-lg transition-colors" title="Download">
                                       <ArrowDownTrayIcon class="w-4 h-4" />
                                    </button>

                                   <button @click="openAddToPlaylist(music.name)" class="p-2 hover:bg-indigo-500/10 hover:text-indigo-400 rounded-lg transition-colors" title="Add to Playlist">
                                      <PlusIcon class="w-4 h-4" />
                                   </button>
                                   <button @click="startRename(music)" class="p-2 hover:bg-blue-500/10 hover:text-blue-400 rounded-lg transition-colors" title="Rename">
                                      <PencilIcon class="w-4 h-4" />
                                   </button>
                                   <button @click="handleDelete(music.name)" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-lg transition-colors" title="Delete">
                                      <TrashIcon class="w-4 h-4" />
                                   </button>
                                </template>
                                <template v-else>
                                   <button @click="saveRename(music.name)" class="p-2 hover:bg-green-500/10 text-green-400 rounded-lg transition-colors">
                                      <CheckIcon class="w-4 h-4" />
                                   </button>
                                   <button @click="cancelRename" class="p-2 hover:bg-gray-700 text-gray-400 rounded-lg transition-colors">
                                      <XMarkIcon class="w-4 h-4" />
                                   </button>
                                </template>
                             </div>
                          </td>
                       </tr>
                       <tr v-if="filteredMusicList.length === 0">
                          <td colspan="2" class="px-6 py-12 text-center text-gray-500">
                             No songs found matching your search.
                          </td>
                       </tr>
                    </tbody>
                 </table>
              </div>
           </div>
        </div>

        <!-- MODULE: PLAYLISTS -->
        <div v-if="currentModule === 'playlists'" class="animate-fade-in space-y-8">
           <div class="relative bg-gradient-to-br from-indigo-600 via-purple-500 to-pink-600 rounded-3xl p-8 sm:p-12 shadow-2xl overflow-hidden mb-8">
              <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/10 blur-3xl mix-blend-overlay animate-pulse"></div>
              </div>
              <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                  <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md shadow-inner border border-white/30">
                      <QueueListIcon class="w-9 h-9 text-white drop-shadow-md" />
                    </div>
                    <div>
                      <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight drop-shadow-sm">Playlist Manager</h1>
                      <p class="text-indigo-100 mt-2 text-lg font-medium opacity-90">Organize your favorite songs into custom collections</p>
                    </div>
                  </div>
                  <button 
                    @click="showCreatePlaylist = true"
                    class="px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-gray-100 transition-all flex items-center gap-2"
                  >
                    <PlusIcon class="w-5 h-5" />
                    <span>New Playlist</span>
                  </button>
              </div>
           </div>

           <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div 
                v-for="playlist in playlists" 
                :key="playlist.id"
                class="bg-gray-800/40 border border-white/5 rounded-2xl p-6 backdrop-blur-xl hover:bg-gray-800/60 transition-all group"
              >
                <div class="flex items-start justify-between mb-4">
                  <div class="p-3 bg-indigo-500/10 rounded-xl">
                    <QueueListIcon class="w-8 h-8 text-indigo-400" />
                  </div>
                  <button @click="deletePlaylist(playlist.id)" class="p-2 text-gray-500 hover:text-red-400 transition-colors">
                    <TrashIcon class="w-5 h-5" />
                  </button>
                </div>
                <h3 class="text-xl font-bold text-white mb-1">{{ playlist.name }}</h3>
                <p class="text-gray-400 text-sm mb-4">{{ playlist.songs?.length || 0 }} songs</p>
                
                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-2">
                  <div v-for="(song, idx) in playlist.songs" :key="idx" class="flex items-center justify-between text-xs text-gray-300 bg-white/5 p-2 rounded-lg border border-white/5">
                    <span class="truncate pr-2">{{ song }}</span>
                    <button @click="removeSongFromPlaylist(playlist.id, song)" class="text-gray-500 hover:text-red-400">
                      <XMarkIcon class="w-3 h-3" />
                    </button>
                  </div>
                  <p v-if="!playlist.songs || playlist.songs.length === 0" class="text-gray-500 text-xs italic text-center py-2">Empty playlist</p>
                </div>
              </div>

              <div 
                v-if="showCreatePlaylist"
                class="bg-gray-900/60 border-2 border-dashed border-indigo-500/30 rounded-2xl p-6 flex flex-col justify-center animate-fade-in"
              >
                <input 
                  v-model="newPlaylistName" 
                  @keyup.enter="createPlaylist"
                  placeholder="Playlist name..."
                  class="bg-gray-800 border border-indigo-500/50 rounded-xl px-4 py-3 text-white focus:outline-none mb-4"
                  autoFocus
                />
                <div class="flex gap-2">
                  <button @click="createPlaylist" class="flex-1 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-500 transition-all">Create</button>
                  <button @click="showCreatePlaylist = false" class="px-4 py-2 bg-gray-700 text-gray-300 font-bold rounded-lg hover:bg-gray-600 transition-all">Cancel</button>
                </div>
              </div>
           </div>
        </div>

      </div>

      <!-- Playlist Selector Modal -->
      <div v-if="showPlaylistSelector" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
         <div class="bg-gray-900 border border-white/10 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/5">
               <h3 class="text-xl font-bold">Add to Playlist</h3>
               <button @click="showPlaylistSelector = null" class="p-2 hover:bg-white/5 rounded-full transition-colors">
                  <XMarkIcon class="w-6 h-6" />
               </button>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
               <div v-if="playlists.length === 0" class="text-center py-8">
                  <p class="text-gray-500 mb-4">No playlists found</p>
                  <button @click="currentModule = 'playlists'; showPlaylistSelector = null; showCreatePlaylist = true" class="text-indigo-400 font-bold hover:underline">
                     Create your first playlist
                  </button>
               </div>
               <button 
                  v-for="playlist in playlists" 
                  :key="playlist.id"
                  @click="addSongToPlaylist(playlist.id, showPlaylistSelector)"
                  class="w-full flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-indigo-600/20 hover:border-indigo-500/30 transition-all group"
               >
                  <div class="flex items-center gap-4">
                     <div class="p-2 bg-indigo-500/10 rounded-lg group-hover:bg-indigo-500/20">
                        <QueueListIcon class="w-5 h-5 text-indigo-400" />
                     </div>
                     <span class="font-bold text-gray-200">{{ playlist.name }}</span>
                  </div>
                  <PlusIcon class="w-5 h-5 text-gray-500 group-hover:text-indigo-400" />
               </button>
            </div>
         </div>
      </div>

      <!-- Album CRUD Modal -->
      <div v-if="showAlbumModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
         <div class="bg-gray-900 border border-white/10 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/5">
               <h3 class="text-xl font-bold">{{ editingAlbum ? 'Edit Album' : 'New Album' }}</h3>
               <button @click="showAlbumModal = false" class="p-2 hover:bg-white/5 rounded-full transition-colors">
                  <XMarkIcon class="w-6 h-6" />
               </button>
            </div>
            <div class="p-6 space-y-4">
               <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Album Title</label>
                  <input v-model="albumForm.name" type="text" placeholder="e.g. Best of Lo-Fi" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition-all"/>
               </div>
               <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Artist (Optional)</label>
                  <input v-model="albumForm.artist" type="text" placeholder="e.g. Various Artists" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition-all"/>
               </div>
               <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Cover Image URL</label>
                  <input v-model="albumForm.cover_image" type="text" placeholder="https://..." class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition-all"/>
               </div>
               <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Description</label>
                  <textarea v-model="albumForm.description" rows="3" placeholder="Tell us more about this collection..." class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition-all"></textarea>
               </div>
               <div class="flex gap-3 pt-2">
                  <button @click="saveAlbum" class="flex-1 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-xl shadow-lg transition-all">
                     {{ editingAlbum ? 'Update Album' : 'Create Album' }}
                  </button>
                  <button @click="showAlbumModal = false" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-xl transition-all">
                     Cancel
                  </button>
               </div>
            </div>
         </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import { 
  Squares2X2Icon, 
  MusicalNoteIcon, 
  ClockIcon, 
  CpuChipIcon,
  BoltIcon,
  PencilIcon,
  TrashIcon,
  CheckIcon,
  XMarkIcon,
  MagnifyingGlassIcon,
  FolderIcon,
  ArrowLeftOnRectangleIcon,
  PlusCircleIcon,
  PlusIcon,
  Cog6ToothIcon,
  PhotoIcon,
  QueueListIcon,
  ArrowPathIcon,
  LinkIcon,
  VideoCameraIcon,
  ComputerDesktopIcon,
  CloudArrowUpIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/solid'
import { fetchSettings, updateSettings, appSettings, appName, brandLogo, brandText } from '../stores/settingsStore.js'
import { getMusicList, getRecentMp3, deleteMusic, renameMusic, getStats } from '../services/musicService.js'
import { logout } from '../stores/authStore.js'

const emit = defineEmits(['switch'])
const MAX_UPLOAD_SIZE_MB = 500
const MAX_UPLOAD_SIZE_BYTES = MAX_UPLOAD_SIZE_MB * 1024 * 1024

// Navigation
const currentModule = ref('overview')
const navItems = [
  { id: 'overview', label: 'Overview', icon: Squares2X2Icon },
  { id: 'albums', label: 'Manage Albums', icon: FolderIcon },
  { id: 'playlists', label: 'Playlists', icon: QueueListIcon },
  { id: 'converter', label: 'Converter', icon: BoltIcon },
  { id: 'library', label: 'My Library', icon: MusicalNoteIcon },
  { id: 'preferences', label: 'Preferences', icon: Cog6ToothIcon },
]

// Data States
const musicList = ref([])
const albums = ref([])
const playlists = ref(JSON.parse(localStorage.getItem('wakmusic_playlists') || '[]'))
const recentActivity = ref([])
const youtubeUrl = ref('')
const selectedAlbumId = ref('')
const converting = ref(false)
const conversionMessage = ref('')
const conversionSuccess = ref(false)
const dashboardStats = ref({
  total_songs: 0,
  total_albums: 0,
  storage_usage: '0 MB',
  status: 'Online'
})
const searchQuery = ref('')
const editingFile = ref(null)
const newName = ref('')
const renaming = ref(false)
const converterMode = ref('url')
const sourceMode = ref('file')
const sourceUrl = ref('')
const selectedFormat = ref('mp3')
const selectedVideoFile = ref(null)
const fileInputKey = ref(0)
const savingPrefs = ref(false)

// Playlist States
const showCreatePlaylist = ref(false)
const newPlaylistName = ref('')
const showPlaylistSelector = ref(null)

// Album CRUD states
const showAlbumModal = ref(false)
const editingAlbum = ref(null)
const albumForm = ref({ name: '', artist: '', description: '', cover_image: '' })

// Actions
const fetchStats = async () => {
  try {
    const [music, recent, stats] = await Promise.all([
      getMusicList(),
      getRecentMp3(),
      getStats()
    ])
    musicList.value = music
    recentActivity.value = recent.slice(0, 5)
    dashboardStats.value = stats
  } catch (error) {
    console.error('Failed to load dashboard stats:', error)
  }
}

async function fetchAlbums() {
  try {
     const res = await axios.get('/api/albums')
     albums.value = res.data
  } catch (err) {
     console.error('Failed to fetch albums', err)
  }
}

onMounted(() => {
  fetchStats()
  fetchAlbums()
  fetchSettings()
})

// DOWNLOAD MUSIC
const downloadMusic = (fileName) => {
  window.open(`/api/download/${encodeURIComponent(fileName)}`, '_blank')
}

const openAlbumModal = (album = null) => {
  if (album) {
    editingAlbum.value = album
    albumForm.value = { ...album }
  } else {
    editingAlbum.value = null
    albumForm.value = { name: '', artist: '', description: '', cover_image: '' }
  }
  showAlbumModal.value = true
}

const saveAlbum = async () => {
  try {
    if (editingAlbum.value) {
      await axios.put(`/api/albums/${editingAlbum.value.id}`, albumForm.value)
    } else {
      await axios.post('/api/albums', albumForm.value)
    }
    showAlbumModal.value = false
    await fetchAlbums()
  } catch (err) {
    console.error('Save album failed', err)
    alert('Failed to save album')
  }
}

const handleDeleteAlbum = async (id) => {
  if (!confirm('Delete this album?')) return
  try {
    await axios.delete(`/api/albums/${id}`)
    await fetchAlbums()
  } catch (err) {
    console.error('Delete album failed', err)
  }
}

// Converter logic
const videoPreview = ref(null)
const fetchingPreview = ref(false)
const conversionProgressLabel = computed(() => {
  if (converterMode.value === 'url') return 'Reading URL and converting stream...'
  if (sourceMode.value === 'cloud') return 'Fetching cloud source and converting...'
  return 'Converting temporary computer source...'
})

const setConverterMode = (mode) => {
  converterMode.value = mode
  conversionMessage.value = ''
  conversionSuccess.value = false
  if (mode !== 'url') videoPreview.value = null
}

const setSourceMode = (mode) => {
  sourceMode.value = mode
  conversionMessage.value = ''
  conversionSuccess.value = false
}

watch(() => youtubeUrl.value, (newUrl) => {
  if (!newUrl) {
    videoPreview.value = null
    return
  }
  if (newUrl.includes('youtube.com/') || newUrl.includes('youtu.be/')) {
    setTimeout(() => { if (youtubeUrl.value === newUrl) fetchVideoInfo(newUrl) }, 500)
  }
})

async function fetchVideoInfo(url) {
  fetchingPreview.value = true
  try {
    const res = await axios.post('/api/video-info', { url })
    videoPreview.value = res.data
  } catch (err) {
    videoPreview.value = null
  } finally {
    fetchingPreview.value = false
  }
}

function previewThumbnail(preview) {
  if (preview?.thumbnail) return preview.thumbnail
  if (preview?.video_id) return `https://i.ytimg.com/vi/${preview.video_id}/hqdefault.jpg`
  return 'https://i.ytimg.com/vi_webp/dQw4w9WgXcQ/hqdefault.webp'
}

const convertVideo = async () => {
  if (!youtubeUrl.value) return
  converting.value = true
  conversionMessage.value = 'Initializing...'
  try {
    await axios.post('/api/convert', { 
      url: youtubeUrl.value,
      album_id: selectedAlbumId.value || null,
      format: selectedFormat.value
    })
    conversionSuccess.value = true
    conversionMessage.value = `${selectedFormat.value.toUpperCase()} saved to library!`
    youtubeUrl.value = ''
    videoPreview.value = null
    await fetchStats()
    setTimeout(() => { conversionMessage.value = '' }, 5000)
  } catch (error) {
    conversionSuccess.value = false
    conversionMessage.value = conversionErrorMessage(error, 'Failed to convert URL.')
  } finally {
    converting.value = false
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files?.[0] || null
  conversionMessage.value = ''

  if (!file) {
    selectedVideoFile.value = null
    return
  }

  if (file.size > MAX_UPLOAD_SIZE_BYTES) {
    selectedVideoFile.value = null
    conversionSuccess.value = false
    conversionMessage.value = `This video is ${formatFileSize(file.size)}. Please choose a file under ${MAX_UPLOAD_SIZE_MB} MB.`
    event.target.value = ''
    return
  }

  selectedVideoFile.value = file
}

const formatFileSize = (bytes) => {
  const units = ['B', 'KB', 'MB', 'GB']
  let size = bytes
  let unitIndex = 0

  while (size >= 1024 && unitIndex < units.length - 1) {
    size /= 1024
    unitIndex += 1
  }

  return `${size.toFixed(size >= 10 || unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`
}

const conversionErrorMessage = (error, fallback = 'Conversion failed.') => {
  const status = error?.response?.status
  const data = error?.response?.data

  if (status === 413) {
    return `That video is larger than the server allows. Keep it under ${MAX_UPLOAD_SIZE_MB} MB, or increase PHP/web server upload limits.`
  }

  if (status === 422 && data?.errors) {
    return Object.values(data.errors).flat()[0] || 'Validation failed.'
  }

  return data?.message || data?.error || fallback
}

const convertFileSource = async () => {
  if (!selectedVideoFile.value) return
  const formData = new FormData()
  formData.append('video', selectedVideoFile.value)
  formData.append('format', selectedFormat.value)
  if (selectedAlbumId.value) formData.append('album_id', selectedAlbumId.value)

  await axios.post('/api/convert-file', formData)
}

const convertCloudSource = async () => {
  if (!sourceUrl.value) return

  await axios.post('/api/convert-source-url', {
    url: sourceUrl.value,
    album_id: selectedAlbumId.value || null,
    format: selectedFormat.value
  })
}

const convertSourceVideo = async () => {
  if (sourceMode.value === 'file' && !selectedVideoFile.value) return
  if (sourceMode.value === 'cloud' && !sourceUrl.value) return

  converting.value = true
  conversionMessage.value = 'Initializing...'

  try {
    if (sourceMode.value === 'file') {
      await convertFileSource()
      selectedVideoFile.value = null
      fileInputKey.value += 1
    } else {
      await convertCloudSource()
      sourceUrl.value = ''
    }

    conversionSuccess.value = true
    conversionMessage.value = `${selectedFormat.value.toUpperCase()} saved to library!`
    await fetchStats()
    setTimeout(() => { conversionMessage.value = '' }, 5000)
  } catch (error) {
    conversionSuccess.value = false
    conversionMessage.value = conversionErrorMessage(error, 'Failed to convert source.')
  } finally {
    converting.value = false
  }
}

// Library Actions
const startRename = (music) => {
  editingFile.value = music.name
  newName.value = music.name.replace(/\.[^/.]+$/, "")
}

const cancelRename = () => {
  editingFile.value = null
  newName.value = ''
}

const saveRename = async (oldName) => {
  if (!newName.value.trim()) return
  let finalName = newName.value.trim()
  
  // Preserve original extension
  const oldExt = oldName.split('.').pop()
  if (!finalName.toLowerCase().endsWith('.' + oldExt.toLowerCase())) {
     finalName += '.' + oldExt
  }
  
  try {
    await renameMusic(oldName, finalName)
    await fetchStats()
    editingFile.value = null
  } catch (error) {
    alert('Rename failed')
  }
}

const handleDelete = async (filename) => {
  if (!confirm(`Delete "${filename}"?`)) return
  try {
    await deleteMusic(filename)
    await fetchStats()
  } catch (error) {
    alert('Delete failed')
  }
}

// Playlist Actions
const savePlaylists = () => {
  localStorage.setItem('wakmusic_playlists', JSON.stringify(playlists.value))
}

const createPlaylist = () => {
  if (!newPlaylistName.value.trim()) return
  playlists.value.push({
    id: Date.now(),
    name: newPlaylistName.value.trim(),
    songs: []
  })
  newPlaylistName.value = ''
  showCreatePlaylist.value = false
  savePlaylists()
}

const deletePlaylist = (id) => {
  if (!confirm('Delete this playlist?')) return
  playlists.value = playlists.value.filter(p => p.id !== id)
  savePlaylists()
}

const openAddToPlaylist = (filename) => {
  showPlaylistSelector.value = filename
}

const addSongToPlaylist = (playlistId, filename) => {
  const playlist = playlists.value.find(p => p.id === playlistId)
  if (playlist && !playlist.songs.includes(filename)) {
    playlist.songs.push(filename)
    savePlaylists()
  }
  showPlaylistSelector.value = null
}

const removeSongFromPlaylist = (playlistId, filename) => {
  const playlist = playlists.value.find(p => p.id === playlistId)
  if (playlist) {
    playlist.songs = playlist.songs.filter(s => s !== filename)
    savePlaylists()
  }
}

const handleLogout = () => {
  logout()
  emit('switch', 'home')
}

const handleLogoUpload = async (event, type) => {
  const file = event.target.files[0]
  if (!file) return
  const formData = new FormData()
  formData.append('logo', file)
  formData.append('type', type)
  try {
     const res = await axios.post('/api/settings/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
     })
     appSettings.value[type] = res.data.url
  } catch (err) {
     alert('Upload failed')
  }
}

const savePreferences = async () => {
  savingPrefs.value = true
  try {
     await updateSettings({ app_name: appSettings.value.app_name })
     alert('Preferences saved successfully!')
  } catch (err) {
     alert('Failed to save preferences')
  } finally {
     savingPrefs.value = false
  }
}

// Computed
const statsCards = computed(() => [
  { title: 'Total Songs', value: dashboardStats.value.total_songs, icon: MusicalNoteIcon, bgClass: 'bg-orange-500/10', iconClass: 'text-orange-400' },
  { title: 'Storage Used', value: dashboardStats.value.storage_usage, icon: FolderIcon, bgClass: 'bg-amber-500/10', iconClass: 'text-amber-400' },
  { title: 'System Status', value: dashboardStats.value.status, icon: CpuChipIcon, bgClass: 'bg-green-500/10', iconClass: 'text-green-400' },
])

const filteredMusicList = computed(() => {
  if (!musicList.value) return []
  if (!searchQuery.value) return musicList.value
  const lower = searchQuery.value.toLowerCase()
  return musicList.value.filter(m => m.name.toLowerCase().includes(lower))
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

@keyframes progress {
  0% { width: 0%; margin-left: -50%; }
  50% { width: 50%; }
  100% { width: 100%; margin-left: 100%; }
}
.animate-progress {
  animation: progress 2s infinite ease-in-out;
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
