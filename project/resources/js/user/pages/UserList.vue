<template>
  <div class="container mt-4">
    <h2>Danh sách người dùng</h2>
    
    <div v-if="loading" class="alert alert-info">Đang tải dữ liệu...</div>
    
    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
    
    <table v-else class="table table-bordered table-striped mt-3">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên người dùng</th>
          <th>Email</th>
          <th>Vai trò</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>
            <span class="badge" :class="user.role === 'admin' ? 'bg-danger' : 'bg-success'">
              {{ user.role }}
            </span>
          </td>
          <td>
            <router-link :to="`/users/${user.id}`" class="btn btn-sm btn-primary">
              Xem chi tiết
            </router-link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Khai báo các biến state
const users = ref([]);
const loading = ref(true);
const error = ref(null);

// Hàm gọi API lấy danh sách users
const fetchUsers = async () => {
  try {
    // Gọi tới API backend bạn vừa tạo
    const response = await axios.get('/api/users'); 
    users.value = response.data;
  } catch (err) {
    console.error(err);
    error.value = 'Không thể tải danh sách người dùng. Vui lòng thử lại!';
  } finally {
    loading.value = false;
  }
};

// Chạy hàm fetchUsers ngay khi component được mount (render) lên giao diện
onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
/* Bạn có thể thêm CSS tùy chỉnh ở đây */
</style>