<template>
  <div>
    <!-- Hero Banner -->
    <div class="relative bg-gradient-to-r from-primary via-blue-600 to-cyan-500 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 py-12 md:py-16">
        <div class="grid md:grid-cols-2 gap-8 items-center">
          <div class="text-white">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-sm mb-4">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              <span>Hàng chính hãng · Nguồn gốc rõ ràng</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">
              Derma<span class="text-cyan-300">city</span><br/>
              <span class="text-blue-100 text-2xl md:text-3xl font-medium">Da Liễu Chuyên Sâu</span>
            </h1>
            <p class="text-blue-100 text-lg mb-8 leading-relaxed">
              Phân phối dược phẩm và mỹ phẩm chất lượng cao cho da nhạy cảm, da mụn, dưỡng trắng và chống lão hoá.
            </p>
            <div class="flex gap-3 flex-wrap">
              <button
                @click="scrollToProducts"
                class="bg-white text-primary font-bold px-6 py-3 rounded-xl hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                  <line x1="3" y1="6" x2="21" y2="6"/>
                  <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                Mua sắm ngay
              </button>
            </div>
          </div>
          <div class="hidden md:flex items-center justify-center">
            <div class="relative w-72 h-72">
              <div class="absolute inset-0 bg-white/10 rounded-full backdrop-blur-sm"></div>
              <div class="absolute inset-8 bg-white/20 rounded-full"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-40 h-40 text-white/40" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                </svg>
              </div>
              <!-- Floating badges -->
              <div class="absolute top-4 right-0 bg-white rounded-xl px-3 py-2 shadow-lg text-xs font-bold text-primary animate-bounce-slow">
                ✓ Chính hãng 100%
              </div>
              <div class="absolute top-16 left-2 bg-white rounded-xl px-3 py-2 shadow-lg text-xs font-bold text-cyan-700">
                🚛 Miễn phí vận chuyển
              </div>
              <div class="absolute bottom-8 left-0 bg-white rounded-xl px-3 py-2 shadow-lg text-xs font-bold text-green-600">
                🚚 Giao hỏa tốc
              </div>
              <div class="absolute bottom-16 right-2 bg-white rounded-xl px-3 py-2 shadow-lg text-xs font-bold text-rose-600">
                🔥 Giảm lên đến 80%
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 40" class="w-full fill-gray-50">
          <path d="M0,40 C360,0 720,40 1080,10 C1260,0 1380,30 1440,40 Z"/>
        </svg>
      </div>
    </div>

    <!-- Stats bar -->
    <div class="bg-white border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="min-h-[76px] rounded-2xl border border-gray-100 bg-gray-50/70 px-3 py-3 flex flex-col items-center justify-center text-center"
          >
            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center mb-1.5" v-html="stat.icon"></div>
            <div>
              <div class="text-sm font-bold text-gray-800 leading-none">{{ stat.value }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category shortcuts -->
    <div class="max-w-7xl mx-auto px-4 py-8">
      <h2 class="text-xl font-bold text-gray-800 mb-5">Danh mục nổi bật</h2>

      <div class="sm:hidden mb-4">
        <label class="block text-sm font-medium text-gray-600 mb-2">Lọc danh mục</label>
        <select
          v-model="selectedCategory"
          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white text-gray-800 text-sm font-medium focus:outline-none focus:border-primary shadow-sm"
        >
          <option value="">Tất cả sản phẩm</option>
          <option v-for="cat in categoryShortcuts" :key="cat.slug" :value="cat.slug">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <div class="hidden sm:flex sm:flex-nowrap gap-3">
        <button
          @click="selectedCategory = ''"
          :class="[
            'flex-1 min-w-0 min-h-12 px-4 py-2 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 text-center flex items-center justify-center',
            !selectedCategory
              ? 'bg-blue-50 text-primary border-blue-200 shadow-sm shadow-blue-100'
              : 'bg-white text-gray-700 border-gray-200 hover:border-primary/40 hover:text-primary'
          ]"
        >
          Tất cả sản phẩm
        </button>
        <button
          v-for="cat in categoryShortcuts"
          :key="cat.slug"
          @click="filterByCategory(cat.slug)"
          :class="[
            'flex-1 min-w-0 min-h-12 px-4 py-2 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 text-center flex items-center justify-center',
            selectedCategory === cat.slug
              ? cat.activeColor
              : cat.color
          ]"
        >
          {{ cat.name }}
        </button>
      </div>
    </div>

    <!-- Product grid -->
    <div ref="productsRef" class="max-w-7xl mx-auto px-4 pb-12">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-xl font-bold text-gray-800">
            {{ selectedCategory ? categoryShortcuts.find(c => c.slug === selectedCategory)?.name : 'Tất cả sản phẩm' }}
          </h2>
          <p class="text-sm text-gray-400">{{ displayedProducts.length }} sản phẩm</p>
        </div>
      </div>

      <!-- Grid -->
      <div v-if="displayedProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <ProductCard
          v-for="product in displayedProducts"
          :key="product.id"
          :product="product"
          @add-to-cart="handleAddToCart"
          @view="handleViewProduct"
        />
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-16">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
        </div>
        <p class="text-gray-500 font-medium">Không tìm thấy sản phẩm</p>
        <button @click="selectedCategory = ''; searchQuery = ''" class="mt-3 text-primary text-sm hover:underline">Xem tất cả</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/shopProducts'
import { useCartStore } from '@/stores/cart'
import ProductCard from '@/components/storefront/ProductCard.vue'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const cartStore = useCartStore()

const productsRef = ref(null)
const selectedCategory = ref(route.query.category || '')
const searchQuery = ref(route.query.q || '')

// Sync from route query
watch(() => route.query, (q) => {
  selectedCategory.value = q.category || ''
  searchQuery.value = q.q || ''
})

const categoryShortcuts = [
  { slug: 'tri-mun',             name: 'Trị mụn',            color: 'bg-green-50  text-green-700  border-green-200  hover:bg-green-100',  activeColor: 'bg-green-600  text-white border-green-600' },
  { slug: 'chong-nang',          name: 'Chống nắng',          color: 'bg-orange-50 text-orange-600 border-orange-200 hover:bg-orange-100', activeColor: 'bg-orange-500 text-white border-orange-500' },
  { slug: 'duong-am',            name: 'Dưỡng ẩm',            color: 'bg-sky-50    text-sky-700    border-sky-200    hover:bg-sky-100',    activeColor: 'bg-sky-600    text-white border-sky-600' },
  { slug: 'tri-nam',             name: 'Trị nám',             color: 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100', activeColor: 'bg-purple-600 text-white border-purple-600' },
  { slug: 'lam-sang-da',         name: 'Làm sáng da',         color: 'bg-amber-50  text-amber-700  border-amber-200  hover:bg-amber-100',  activeColor: 'bg-amber-500  text-white border-amber-500' },
  { slug: 'thuc-pham-chuc-nang', name: 'Thực phẩm CN',        color: 'bg-teal-50   text-teal-700   border-teal-200   hover:bg-teal-100',   activeColor: 'bg-teal-600   text-white border-teal-600' },
  { slug: 'cham-soc-co-the',     name: 'Cơ thể',               color: 'bg-pink-50   text-pink-700   border-pink-200   hover:bg-pink-100',   activeColor: 'bg-pink-600   text-white border-pink-600' },
]

const stats = [
  { label: 'Sản phẩm', value: '500+', icon: '<svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>' },
  { label: 'Khách hàng', value: '10,000+', icon: '<svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>' },
  { label: 'Thương hiệu', value: '50+', icon: '<svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 14.14 14.14"/></svg>' },
  { label: 'Đánh giá', value: '4.9★', icon: '<svg class="w-4 h-4 text-warning" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' },
]

const displayedProducts = computed(() => productStore.filteredProducts(selectedCategory.value, searchQuery.value))

const filterByCategory = (slug) => {
  selectedCategory.value = selectedCategory.value === slug ? '' : slug
}

const scrollToProducts = () => {
  productsRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const handleAddToCart = (product) => {
  cartStore.addItem(product)
  cartStore.openDrawer()
}

const handleViewProduct = (product) => {
  router.push(`/shop/product/${product.slug}`)
}
</script>
