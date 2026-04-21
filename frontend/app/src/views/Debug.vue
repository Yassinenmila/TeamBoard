<template>
  <div class="min-h-screen bg-slate-50 p-12">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-black text-slate-900 mb-8">🔍 Debug Page</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- AUTH STATE -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200">
          <h2 class="text-xl font-bold text-slate-900 mb-4">Auth State</h2>
          <div class="space-y-2">
            <p><strong>Token:</strong> {{ auth.token ? '✅ Present' : '❌ Missing' }}</p>
            <p><strong>User:</strong> {{ auth.user ? '✅ Present' : '❌ Missing' }}</p>
            <p><strong>Role:</strong> {{ auth.user?.role || 'N/A' }}</p>
            <p><strong>Name:</strong> {{ auth.user?.name || 'N/A' }}</p>
          </div>
        </div>

        <!-- ROUTER INFO -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200">
          <h2 class="text-xl font-bold text-slate-900 mb-4">Router Info</h2>
          <div class="space-y-2">
            <p><strong>Current Path:</strong> {{ $route.path }}</p>
            <p><strong>Route Name:</strong> {{ $route.name }}</p>
            <p><strong>Meta:</strong> {{ JSON.stringify($route.meta, null, 2) }}</p>
          </div>
        </div>

        <!-- ACTIONS -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 md:col-span-2">
          <h2 class="text-xl font-bold text-slate-900 mb-4">Actions</h2>
          <div class="flex gap-4">
            <router-link to="/login" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Go to Login</router-link>
            <router-link to="/admin/dashboard" class="px-4 py-2 bg-green-500 text-white rounded-lg">Go to Admin</router-link>
            <router-link to="/user/dashboard" class="px-4 py-2 bg-purple-500 text-white rounded-lg">Go to User</router-link>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
</script>