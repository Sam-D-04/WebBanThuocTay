import { createRouter, createWebHistory } from 'vue-router'
import { defineAsyncComponent } from 'vue'
import warehouseRoutes from './warehouse'

// Layouts
const AdminLayout = defineAsyncComponent(() => import('@/layouts/AdminLayout.vue'))

// Pages
const Dashboard = defineAsyncComponent(() => import('@/pages/Dashboard.vue'))
const Products = defineAsyncComponent(() => import('@/pages/Products.vue'))
const Batches = defineAsyncComponent(() => import('@/pages/Batches.vue'))
const Orders = defineAsyncComponent(() => import('@/pages/Orders.vue'))
const Customers = defineAsyncComponent(() => import('@/pages/Customers.vue'))
const Alerts = defineAsyncComponent(() => import('@/pages/Alerts.vue'))
const Reports = defineAsyncComponent(() => import('@/pages/Reports.vue'))
const Settings = defineAsyncComponent(() => import('@/pages/Settings.vue'))

const routes = [
  {
    path: '/',
    component: AdminLayout,
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { title: 'Tổng quan', icon: 'bar-chart' }
      },
      {
        path: '/products',
        name: 'Products',
        component: Products,
        meta: { title: 'Sản phẩm', icon: 'package' }
      },
      {
        path: '/batches',
        name: 'Batches',
        component: Batches,
        meta: { title: 'Lô thuốc', icon: 'box' }
      },
      {
        path: '/orders',
        name: 'Orders',
        component: Orders,
        meta: { title: 'Đơn hàng', icon: 'shopping-cart' }
      },
      {
        path: '/customers',
        name: 'Customers',
        component: Customers,
        meta: { title: 'Khách hàng', icon: 'users' }
      },
      {
        path: '/alerts',
        name: 'Alerts',
        component: Alerts,
        meta: { title: 'Cảnh báo', icon: 'alert-circle' }
      },
      {
        path: '/reports',
        name: 'Reports',
        component: Reports,
        meta: { title: 'Báo cáo', icon: 'chart-line' }
      },
      {
        path: '/settings',
        name: 'Settings',
        component: Settings,
        meta: { title: 'Cài đặt', icon: 'settings' }
      }
    ]
  },
  ...warehouseRoutes
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
