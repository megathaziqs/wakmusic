// resources/js/stores/audioStore.js
import { ref, computed, nextTick } from 'vue'

export const queue = ref([])
export const currentIndex = ref(0)
export const queuePlayer = ref(null)
export const isPlaying = ref(false)
export const currentTime = ref(0)
export const duration = ref(0)

export const volume = ref(1)

export const currentTrack = computed(() => queue.value[currentIndex.value] || '')
export const currentTrackName = computed(() =>
  currentTrack.value ? decodeURIComponent(currentTrack.value.split('/').pop()) : ''
)

// ================= Queue Controls =================
export function addToQueue(mp3) {
  queue.value.push(mp3)
  if (queue.value.length === 1) playTrack(0)
}

export function playTrack(index) {
  currentIndex.value = index
  nextTick(() => {
    queuePlayer.value?.play()
    isPlaying.value = true
  })
}

export function nextTrack() {
  if (currentIndex.value < queue.value.length - 1) currentIndex.value++
  else currentIndex.value = 0
  playTrack(currentIndex.value)
}

export function prevTrack() {
  if (currentIndex.value > 0) currentIndex.value--
  else currentIndex.value = queue.value.length - 1
  playTrack(currentIndex.value)
}

export function clearQueue() {
  queue.value = []
  currentIndex.value = 0
  isPlaying.value = false
}

// ================= Player Controls =================
export function updateTime() {
  currentTime.value = queuePlayer.value?.currentTime || 0
}

export function setDuration() {
  duration.value = queuePlayer.value?.duration || 0
}

// Seek to position
export function seek(e) {
  const rect = e.target.getBoundingClientRect()
  if (queuePlayer.value && duration.value) {
    queuePlayer.value.currentTime = ((e.clientX - rect.left) / rect.width) * duration.value
  }
}

// Toggle play/pause
export function togglePlay() {
  if (!queuePlayer.value) return
  if (isPlaying.value) {
    queuePlayer.value.pause()
    isPlaying.value = false
  } else {
    queuePlayer.value.play()
    isPlaying.value = true
  }
}

// Set the actual audio element from template
export function setAudioElement(el) {
  queuePlayer.value = el
}

export function formatTime(sec = 0) {
  const m = Math.floor(sec / 60)
  const s = Math.floor(sec % 60)
  return `${m}:${s.toString().padStart(2, '0')}`
}

export function setVolume(val) {
  const v = Math.max(0, Math.min(1, val))
  volume.value = v
  if (queuePlayer.value) {
    queuePlayer.value.volume = v
  }
}
