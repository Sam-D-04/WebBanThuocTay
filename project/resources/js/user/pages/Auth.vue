<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

axios.defaults.baseURL = '/api'

const router = useRouter()

const mode = ref('login')
const loading = ref(false)
const errors = ref(null)

const loginForm = ref({ email: '', password: '' })
const registerForm = ref({ name: '', email: '', password: '', password_confirmation: '' })

async function submitLogin() {
  loading.value = true
  errors.value = null
  try {
    const { data } = await axios.post('/login', loginForm.value)
    localStorage.setItem('token', data.token)
    axios.defaults.headers.common.Authorization = `Bearer ${data.token}`
    alert('Đăng nhập thành công')
    await router.push({ name: 'home' })
    window.location.reload()
  } catch (e) {
    errors.value =
      e.response?.data?.message ||
      (e.response?.data || e).errors ||
      'Đăng nhập thất bại'
  } finally {
    loading.value = false
  }
}

async function submitRegister() {
  loading.value = true
  errors.value = null
  try {
    await axios.post('/register', registerForm.value)
    alert('Đăng ký thành công, vui lòng đăng nhập')
    loginForm.value.email = registerForm.value.email
    loginForm.value.password = ''
    mode.value = 'login'
  } catch (e) {
    errors.value =
      e.response?.data?.message ||
      (e.response?.data || e).errors ||
      'Đăng ký thất bại'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 p-6"
  >
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div
          class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl shadow-lg mb-4"
        >
          <i class="fa-solid fa-user text-white text-3xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-slate-900">Chào mừng trở lại</h1>
        <p class="text-slate-600 mt-2">
          {{ mode === 'login' ? 'Đăng nhập để tiếp tục' : 'Tạo tài khoản mới' }}
        </p>
      </div>

      <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="flex border-b border-slate-200">
          <button
            @click="mode = 'login'"
            :class="[
              'flex-1 py-4 text-sm font-semibold transition-all',
              mode === 'login'
                ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50'
                : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            Đăng nhập
          </button>
          <button
            @click="mode = 'register'"
            :class="[
              'flex-1 py-4 text-sm font-semibold transition-all',
              mode === 'register'
                ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50'
                : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            Đăng ký
          </button>
        </div>

        <div class="p-8">
          <div
            v-if="errors"
            class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg"
          >
            <div class="flex items-start gap-3">
              <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
              <pre
                class="text-sm text-red-700 whitespace-pre-wrap flex-1"
              >{{ errors }}</pre>
            </div>
          </div>

          <form v-if="mode === 'login'" @submit.prevent="submitLogin" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Địa chỉ email
              </label>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fa-solid fa-envelope text-slate-400"></i>
                </div>
                <input
                  v-model="loginForm.email"
                  type="email"
                  class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="name@example.com"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Mật khẩu
              </label>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fa-solid fa-lock text-slate-400"></i>
                </div>
                <input
                  v-model="loginForm.password"
                  type="password"
                  class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="••••••••"
                  required
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] transition-all"
            >
              <span v-if="loading" class="flex items-center justify-center gap-2">
                <i class="fa-solid fa-spinner animate-spin"></i>
                Đang xử lý...
              </span>
              <span v-else>Đăng nhập</span>
            </button>
          </form>

          <form v-else @submit.prevent="submitRegister" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Họ và tên
              </label>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fa-solid fa-user text-slate-400"></i>
                </div>
                <input
                  v-model="registerForm.name"
                  type="text"
                  class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="Nguyễn Văn A"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Địa chỉ email
              </label>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fa-solid fa-envelope text-slate-400"></i>
                </div>
                <input
                  v-model="registerForm.email"
                  type="email"
                  class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="name@example.com"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Mật khẩu
              </label>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fa-solid fa-lock text-slate-400"></i>
                </div>
                <input
                  v-model="registerForm.password"
                  type="password"
                  class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="••••••••"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Xác nhận mật khẩu
              </label>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fa-solid fa-check-circle text-slate-400"></i>
                </div>
                <input
                  v-model="registerForm.password_confirmation"
                  type="password"
                  class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="••••••••"
                  required
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] transition-all"
            >
              <span v-if="loading" class="flex items-center justify-center gap-2">
                <i class="fa-solid fa-spinner animate-spin"></i>
                Đang xử lý...
              </span>
              <span v-else>Đăng ký</span>
            </button>
          </form>
        </div>
      </div>

      <p class="text-center text-sm text-slate-600 mt-6">
        Bằng cách tiếp tục, bạn đồng ý với
        <a href="#" class="text-blue-600 hover:underline">Điều khoản dịch vụ</a>
        và
        <a href="#" class="text-blue-600 hover:underline">Chính sách bảo mật</a>
      </p>
    </div>
  </div>
</template>
