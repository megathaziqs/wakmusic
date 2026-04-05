<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f172a] text-white p-4">
    <div class="w-full max-w-md bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
      
      <!-- Decorative Elements -->
      <div class="absolute top-0 left-0 -mt-10 -ml-10 w-40 h-40 bg-pink-500/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-0 -mb-10 -mr-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>

      <div class="relative z-10">
        <div class="text-center mb-8">
           <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/30 transform -rotate-3">
              <span class="text-3xl">🚀</span>
           </div>
           <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">Join WakMusic</h2>
           <p class="text-gray-400 mt-2">Start your musical journey today</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-300 ml-1">Full Name</label>
            <input 
              v-model="form.name"
              type="text" 
              required
              class="w-full bg-gray-900/50 border border-gray-600 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all"
              placeholder="John Doe"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-300 ml-1">Email Address</label>
            <input 
              v-model="form.email"
              type="email" 
              required
              class="w-full bg-gray-900/50 border border-gray-600 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all"
              placeholder="you@example.com"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-300 ml-1">Password</label>
            <input 
              v-model="form.password"
              type="password" 
              required
              class="w-full bg-gray-900/50 border border-gray-600 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all"
              placeholder="••••••••"
            />
          </div>
          
           <div class="space-y-2">
            <label class="text-sm font-medium text-gray-300 ml-1">Confirm Password</label>
            <input 
              v-model="form.password_confirmation"
              type="password" 
              required
              class="w-full bg-gray-900/50 border border-gray-600 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all"
              placeholder="••••••••"
            />
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-3.5 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold rounded-xl shadow-lg shadow-pink-500/25 transform hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>{{ loading ? 'Creating Account...' : 'Create Account' }}</span>
          </button>
        </form>
        
        <p v-if="error" class="mt-4 text-center text-red-400 text-sm font-medium">{{ error }}</p>

        <div class="mt-8 text-center text-sm text-gray-400">
           Already have an account? 
           <button @click="$emit('switch', 'login')" class="text-pink-400 font-medium hover:text-pink-300 transition-colors">Sign in</button>
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
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  
  if (form.password !== form.password_confirmation) {
     error.value = 'Passwords do not match'
     loading.value = false
     return
  }
  
  try {
    const response = await axios.post('/api/register', form)
    
    // Axios throws on non-2xx
    setUser(response.data.user, response.data.token)
    emit('success')
    
  } catch (e) {
    if (e.response && e.response.data) {
        error.value = e.response.data.message || 'Registration failed'
    } else {
        error.value = 'An error occurred. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
