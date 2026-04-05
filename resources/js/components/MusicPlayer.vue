<template>
  <div v-if="currentSong">
    <h2>Now Playing</h2>
    <p>{{ currentSong.title }} - {{ currentSong.artist }}</p>
    <audio :src="currentSong.url" controls autoplay></audio>
  </div>
</template>

<script>
export default {
  name: 'MusicPlayer',
  props: {
    currentSong: Object
  }
}
</script>