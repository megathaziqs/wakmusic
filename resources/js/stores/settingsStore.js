import { ref, computed } from 'vue'
import axios from 'axios'

export const appSettings = ref({
    app_name: 'WakMusic',
    brand_logo: null,
    brand_text: null,
    hero_background: null,
    loaded: false
})

export const fetchSettings = async () => {
    try {
        const res = await axios.get('/api/settings')
        appSettings.value = { ...appSettings.value, ...res.data, loaded: true }
    } catch (err) {
        console.error('Failed to fetch settings', err)
        appSettings.value.loaded = true
    }
}

export const updateSettings = async (data) => {
    try {
        await axios.post('/api/settings', data)
        appSettings.value = { ...appSettings.value, ...data }
    } catch (err) {
        console.error('Failed to update settings', err)
        throw err
    }
}

export const appName = computed(() => appSettings.value.app_name || 'WakMusic')
export const brandLogo = computed(() => appSettings.value.brand_logo)
export const brandText = computed(() => appSettings.value.brand_text)
export const heroBackground = computed(() => appSettings.value.hero_background)
export const isLoaded = computed(() => appSettings.value.loaded)
