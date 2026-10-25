<template>
  <div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-slate-800">MyEvent Admin</h1>
          <p class="text-xs text-slate-500">Panel Kelola Event & Acara</p>
        </div>
        <button
          @click="handleLogout"
          class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-red-600 border border-slate-300 rounded-lg hover:border-red-200 hover:bg-red-50 transition"
        >
          Keluar
        </button>
      </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Event Active</h2>
        <button
          @click="openModal()"
          class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition"
        >
          + Tambah Event Baru
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100 text-slate-600 text-xs font-semibold uppercase tracking-wider">
              <th class="py-3 px-4">Judul Event</th>
              <th class="py-3 px-4">Lokasi</th>
              <th class="py-3 px-4">Tanggal</th>
              <th class="py-3 px-4">Kuota</th>
              <th class="py-3 px-4">Harga Tiket</th>
              <th class="py-3 px-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 text-sm">
            <tr v-if="events.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">
                Belum ada data event. Klik tombol "+ Tambah Event Baru" di atas.
              </td>
            </tr>
            <tr v-for="item in events" :key="item.id" class="hover:bg-slate-50">
              <td class="py-3 px-4 font-medium text-slate-800">{{ item.title }}</td>
              <td class="py-3 px-4 text-slate-600">{{ item.location }}</td>
              <td class="py-3 px-4 text-slate-600">{{ item.event_date }}</td>
              <td class="py-3 px-4 text-slate-600">{{ item.quota }} Orang</td>
              <td class="py-3 px-4 text-slate-600">Rp {{ Number(item.price).toLocaleString('id-ID') }}</td>
              <td class="py-3 px-4 text-right space-x-2">
                <button
                  @click="openModal(item)"
                  class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold"
                >
                  Edit
                </button>
                <button
                  @click="deleteEvent(item.id)"
                  class="text-red-600 hover:text-red-900 text-xs font-semibold"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <div v-if="showModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
        <h3 class="text-lg font-bold text-slate-800 mb-4">
          {{ isEditMode ? 'Edit Event' : 'Tambah Event Baru' }}
        </h3>

        <form @submit.prevent="saveEvent" class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Judul Event</label>
            <input v-model="form.title" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Deskripsi</label>
            <textarea v-model="form.description" required rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Lokasi</label>
              <input v-model="form.location" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal</label>
              <input v-model="form.event_date" type="date" required class="w-full px-3 py-2 border rounded-lg text-sm" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kuota Peserta</label>
              <input v-model="form.quota" type="number" required min="1" class="w-full px-3 py-2 border rounded-lg text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Harga Tiket (Rp)</label>
              <input v-model="form.price" type="number" required min="0" class="w-full px-3 py-2 border rounded-lg text-sm" />
            </div>
          </div>

          <div class="flex justify-end space-x-2 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border rounded-lg text-slate-600 text-sm hover:bg-slate-100"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700"
            >
              Simpan Data
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '../api/axios';

const events = ref([]);
const showModal = ref(false);
const isEditMode = ref(false);
const selectedId = ref(null);
const router = useRouter();

const form = ref({
  title: '',
  description: '',
  location: '',
  event_date: '',
  quota: 100,
  price: 0,
});

const fetchEvents = async () => {
  try {
    const response = await apiClient.get('/events');
    events.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data event:', error);
  }
};

const openModal = (item = null) => {
  if (item) {
    isEditMode.value = true;
    selectedId.value = item.id;
    form.value = { ...item };
  } else {
    isEditMode.value = false;
    selectedId.value = null;
    form.value = { title: '', description: '', location: '', event_date: '', quota: 100, price: 0 };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveEvent = async () => {
  try {
    if (isEditMode.value) {
      await apiClient.put(`/events/${selectedId.value}`, form.value);
    } else {
      await apiClient.post('/events', form.value);
    }
    closeModal();
    fetchEvents();
  } catch (error) {
    alert('Gagal menyimpan data event. Pastikan input valid.');
  }
};

const deleteEvent = async (id) => {
  if (confirm('Yakin ingin menghapus event ini?')) {
    try {
      await apiClient.delete(`/events/${id}`);
      fetchEvents();
    } catch (error) {
      alert('Gagal menghapus event.');
    }
  }
};

const handleLogout = async () => {
  try {
    await apiClient.post('/logout');
  } catch (error) {
    console.error('Logout error:', error);
  } finally {
    localStorage.removeItem('access_token');
    router.push('/login');
  }
};

onMounted(() => {
  fetchEvents();
});
</script>