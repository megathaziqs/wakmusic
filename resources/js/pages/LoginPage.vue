<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f172a] text-white p-4">
    <div class="w-full max-w-md bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
      
      <!-- Decorative Elements -->
      <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-pink-500/20 rounded-full blur-3xl"></div>

      <div class="relative z-10">
        <div class="text-center mb-8">
           <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-500/30 transform rotate-3">
              <span class="text-3xl">🔐</span>
           </div>
           <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">Welcome Back</h2>
           <p class="text-gray-400 mt-2">Sign in to manage your music</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-300 ml-1">Email Address</label>
            <input 
              v-model="form.email"
              type="email" 
              required
              class="w-full bg-gray-900/50 border border-gray-600 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
              placeholder="you@example.com"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-300 ml-1">Password</label>
            <input 
              v-model="form.password"
              type="password" 
              required
              class="w-full bg-gray-900/50 border border-gray-600 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
              placeholder="••••••••"
            />
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/25 transform hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>{{ loading ? 'Signing in...' : 'Sign In' }}</span>
          </button>
        </form>
        
        <p v-if="error" class="mt-4 text-center text-red-400 text-sm font-medium">{{ error }}</p>

        <div class="mt-8 text-center text-sm text-gray-400">
           Don't have an account? 
           <button @click="$emit('switch', 'register')" class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors">Create one</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { setUser } from '../stores/authStore'

const emit = defineEmits(['switch', 'success'])

const form = reactive({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/api/login', form)
    
    // Axios throws on error statuses, so we're good here
    setUser(response.data.user, response.data.token)
    emit('success')

  } catch (e) {
    if (e.response && e.response.data) {
        error.value = e.response.data.message || 'Login failed'
    } else {
        error.value = 'An error occurred. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
