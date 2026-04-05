import axios from 'axios'

const API_BASE = '/api'

export const getMusicList = async () => {
  const res = await axios.get(`${API_BASE}/music-list`)
  return res.data
}

export const getRecentMp3 = async () => {
  const res = await axios.get(`${API_BASE}/recent-mp3`)
  return res.data
}

export const deleteMusic = async (filename) => {
  return axios.delete(`${API_BASE}/music/${encodeURIComponent(filename)}`)
}

export const renameMusic = async (oldName, newName) => {
  const res = await axios.post(`${API_BASE}/music/rename`, { oldName, newName })
  return res.data
}

export const getStats = async () => {
  const res = await axios.get(`${API_BASE}/stats`)
  return res.data
}
