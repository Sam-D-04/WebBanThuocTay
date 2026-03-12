<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
      <router-link to="/users" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
      </router-link>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <div v-else-if="error" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
      <p class="text-sm text-red-700">{{ error }}</p>
    </div>

    <div v-else-if="user" class="bg-white overflow-hidden shadow-md ring-1 ring-black ring-opacity-5 sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex items-center justify-between bg-gray-50 border-b border-gray-200">
        <div>
          <h3 class="text-lg leading-6 font-bold text-gray-900">Hồ sơ người dùng</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Chi tiết thông tin tài khoản.</p>
        </div>
        <img class="h-16 w-16 rounded-full border-2 border-white shadow-sm" 
             :src="'https://ui-avatars.com/api/?name=' + user.name + '&background=random&size=128'" alt="Avatar" />
      </div>
      <div class="px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50">
            <dt class="text-sm font-medium text-gray-500">Họ và tên</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">{{ user.name }}</dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50">
            <dt class="text-sm font-medium text-gray-500">Mã định danh (ID)</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">#{{ user.id }}</dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50">
            <dt class="text-sm font-medium text-gray-500">Địa chỉ Email</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 text-blue-600">{{ user.email }}</dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50">
            <dt class="text-sm font-medium text-gray-500">Vai trò</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                    :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'">
                {{ user.role.toUpperCase() }}
              </span>
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50">
            <dt class="text-sm font-medium text-gray-500">Trạng thái xác thực</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <span v-if="user.email_verified_at" class="text-green-600 font-medium flex items-center">
                <i class="fas fa-check-circle mr-1.5"></i> Đã xác thực email
              </span>
              <span v-else class="text-yellow-600 font-medium flex items-center">
                <i class="fas fa-exclamation-triangle mr-1.5"></i> Chưa xác thực
              </span>
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50">
            <dt class="text-sm font-medium text-gray-500">Ngày đăng ký</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ new Date(user.created_at).toLocaleString('vi-VN') }}
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute(); 
const user = ref(null);
const loading = ref(true);
const error = ref(null);

const fetchUserDetail = async () => {
  const userId = route.params.id; 
  
  try {
    const response = await axios.get(`/users/${userId}`);
    user.value = response.data;
  } catch (err) {
    console.error(err);
    if (err.response && err.response.status === 404) {
      error.value = 'Không tìm thấy người dùng này!';
    } else {
      error.value = 'Đã xảy ra lỗi khi tải dữ liệu.';
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUserDetail();
});
</script>