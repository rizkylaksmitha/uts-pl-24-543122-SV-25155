<template>
  <div class="flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-sm border border-slate-200">
      <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-800">MyEvent</h1>
        <p class="text-sm text-slate-500 mt-1">Masuk untuk mengelola event Anda</p>
      </div>

      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-100">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            required
            placeholder="admin@myevent.com"
            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Password</label>
          <input
            v-model="password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
          />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full py-2.5 bg-indigo-600 text-white font-medium text-sm rounded-lg hover:bg-indigo-700 transition duration-150 disabled:opacity-50"
        >
          {{ isLoading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '../api/axios';

const email = ref('');
const password = ref('');
const errorMessage = ref('');
const isLoading = ref(false);
const router = useRouter();

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await apiClient.post('/login', {
      email: email.value,
      password: password.value,
    });

    localStorage.setItem('access_token', response.data.access_token);
    router.push('/dashboard');
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Gagal login. Periksa kembali email dan password.';
  } finally {
    isLoading.value = false;
  }
};
</script>