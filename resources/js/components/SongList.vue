<template>
  <div>
    <h2>Senarai Lagu</h2>
    <ul>
      <li v-for="song in songs" :key="song.id">
        {{ song.title }} - {{ song.artist }}
        <button @click="$emit('play', song)">▶️ Play</button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'SongList',
  props: {
    songs: Array
  }
}
</script>