<template>
  <div class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-10">
    <div class="rounded-3xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/70 p-4 md:p-6 shadow-sm">
      <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-800">Tài khoản của tôi</h1>
          <p class="text-sm md:text-base text-gray-500 mt-1">Xin chào, <strong class="text-primary">{{ authStore.userName }}</strong></p>
        </div>
        <div class="h-px w-full bg-slate-200 md:hidden"></div>
        <div class="flex items-center md:justify-end">
          <button
            @click="goToShop"
            class="w-full md:w-auto inline-flex items-center justify-center gap-2 text-sm font-medium text-primary hover:text-blue-700 transition-colors border border-blue-200 px-4 py-2.5 rounded-xl hover:border-blue-300 bg-blue-50/70"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 18l-6-6 6-6"/>
            </svg>
            Quay lại mua hàng
          </button>
        </div>
      </div>

      <!-- Tabs -->
      <div class="mb-6 rounded-xl bg-slate-100/80 p-1 inline-flex w-full md:w-auto">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            'flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
            activeTab === tab.key
              ? 'bg-white text-primary shadow-sm border border-primary/15'
              : 'text-gray-500 hover:text-gray-700'
          ]"
        >
          <span v-html="tab.icon" class="inline-flex"></span>
          {{ tab.label }}
        </button>
      </div>

      <!-- Orders Tab -->
      <div v-if="activeTab === 'orders'">
        <div v-if="orders.length === 0" class="text-center py-14 rounded-2xl bg-white border border-slate-200">
          <div class="text-5xl mb-4">📦</div>
          <p class="text-gray-500 font-medium">Bạn chưa có đơn hàng nào</p>
          <router-link to="/shop" class="mt-3 inline-block text-primary text-sm font-medium hover:underline">Mua sắm ngay</router-link>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="order in orders"
            :key="order.id"
            class="bg-white rounded-2xl border border-slate-200 p-4 md:p-5 shadow-sm hover:border-primary/30 transition-colors"
          >
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between mb-3">
              <div class="flex items-center flex-wrap gap-2">
                <span class="text-sm font-bold text-gray-800">{{ order.id }}</span>
                <span class="text-xs text-gray-400 hidden md:inline">·</span>
                <span class="text-xs text-gray-500">{{ order.orderDate }}</span>
              </div>
              <span :class="[
                'text-xs font-semibold px-2.5 py-1 rounded-full w-fit',
                order.order_status === 'delivered' ? 'bg-green-100 text-green-700' :
                order.order_status === 'cancelled' ? 'bg-red-100 text-red-600' :
                order.order_status === 'shipping' ? 'bg-blue-100 text-blue-700' :
                'bg-yellow-100 text-yellow-700'
              ]">{{ statusLabels[order.order_status] || order.order_status }}</span>
            </div>

            <div class="space-y-2 mb-3">
              <div v-for="item in order.items" :key="item.productId" class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-2.5">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex-shrink-0 flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-700 truncate">{{ item.productName || item.product_name }}</p>
                  <p class="text-xs text-gray-400">x{{ item.quantity }}</p>
                </div>
                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">{{ formatPrice(item.subtotal || item.price * item.quantity) }}</span>
              </div>
            </div>

            <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
              <span class="text-sm text-gray-500">Tổng tiền</span>
              <span class="text-base font-bold text-primary">{{ formatPrice(order.final_amount || order.total_amount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Tab -->
      <div v-if="activeTab === 'profile'" class="bg-white rounded-2xl border border-slate-200 p-5 md:p-6 max-w-xl shadow-sm">
        <h2 class="font-bold text-gray-800 mb-4">Thông tin cá nhân</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Họ và tên</label>
            <input type="text" :value="authStore.user?.name" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50" readonly />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
            <input type="email" :value="authStore.user?.email" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50" readonly />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Số điện thoại</label>
            <input type="tel" :value="authStore.user?.phone" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50" readonly />
          </div>
          <p class="text-xs text-gray-400 italic">Chức năng chỉnh sửa thông tin sẽ được tích hợp API backend.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/orders'

const router = useRouter()
const authStore = useAuthStore()
const orderStore = useOrderStore()

const activeTab = ref('orders')
const orders = ref([])

const tabs = [
  { key: 'orders', label: 'Đơn hàng', icon: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>' },
  { key: 'profile', label: 'Hồ sơ', icon: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' },
]

const statusLabels = {
  pending: 'Chờ xử lý',
  confirmed: 'Đã xác nhận',
  packing: 'Đang đóng gói',
  shipping: 'Đang giao',
  delivered: 'Đã giao',
  cancelled: 'Đã huỷ',
}

const formatPrice = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)

const goToShop = () => {
  router.push('/shop')
}

onMounted(() => {
  // In real app: fetch orders for current user from API
  // For demo: show mock orders
  const user = authStore.user
  if (user) {
    orders.value = orderStore.orders.filter(o => o.customerId === `CUST-00${user.id}` || o.customerEmail === user.email)
  }
})
</script>
