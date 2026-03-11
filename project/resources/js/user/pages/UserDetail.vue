<template>
  <div class="container mt-4">
    <h2>Chi tiết người dùng</h2>

    <div v-if="loading" class="alert alert-info">Đang tải dữ liệu...</div>

    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-else-if="user" class="card mt-3" style="max-width: 500px;">
      <div class="card-body">
        <h5 class="card-title">{{ user.name }}</h5>
        <hr>
        <p class="card-text"><strong>ID:</strong> {{ user.id }}</p>
        <p class="card-text"><strong>Email:</strong> {{ user.email }}</p>
        <p class="card-text"><strong>Vai trò:</strong> <span class="text-uppercase">{{ user.role }}</span></p>
        <p class="card-text">
          <strong>Ngày tham gia:</strong> 
          {{ new Date(user.created_at).toLocaleDateString('vi-VN') }}
        </p>
        
        <router-link to="/users" class="btn btn-secondary mt-3">
          &laquo; Quay lại danh sách
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute(); // Lấy thông tin URL hiện tại
const user = ref(null);
const loading = ref(true);
const error = ref(null);

const fetchUserDetail = async () => {
  // Lấy params {id} từ URL (ví dụ: /users/1 -> id = 1)
  const userId = route.params.id; 
  
  try {
    // Gọi API lấy chi tiết
    const response = await axios.get(`/api/users/${userId}`);
    user.value = response.data;
  } catch (err) {
    console.error(err);
    // Nếu lỗi 404 (Không tìm thấy user)
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