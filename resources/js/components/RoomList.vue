<template>
  <div>
    <h2 class="mb-4">Dostępne pokoje</h2>
    <div v-if="loading" class="text-center">Ładowanie...</div>
    <div v-else class="row g-3">
      <div v-for="room in rooms" :key="room.id" class="col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">{{ room.name }}</h5>
            <p class="card-text text-muted">Pojemność: {{ room.capacity }} osób</p>
            <button class="btn btn-primary w-100" @click="emit('book', room)">Zarezerwuj</button>
          </div>
        </div>
      </div>
    </div>

    <nav v-if="lastPage > 1" class="mt-4 d-flex justify-content-center gap-2">
      <button class="btn btn-outline-secondary btn-sm" :disabled="currentPage === 1" @click="fetchRooms(currentPage - 1)">
        &laquo; Poprzednia
      </button>
      <span class="align-self-center text-muted small">Strona {{ currentPage }} z {{ lastPage }}</span>
      <button class="btn btn-outline-secondary btn-sm" :disabled="currentPage === lastPage" @click="fetchRooms(currentPage + 1)">
        Następna &raquo;
      </button>
    </nav>

    <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../api.js';

const emit = defineEmits(['book']);

const rooms = ref([]);
const loading = ref(true);
const error = ref(null);
const currentPage = ref(1);
const lastPage = ref(1);

async function fetchRooms(page = 1) {
  loading.value = true;
  error.value = null;
  try {
    const { res, data } = await apiFetch(`/rooms?page=${page}`);
    if (res.ok) {
      rooms.value = data.data;
      currentPage.value = data.current_page;
      lastPage.value = data.last_page;
    } else {
      error.value = 'Nie udało się załadować pokoi.';
    }
  } catch {
    error.value = 'Nie udało się załadować pokoi.';
  } finally {
    loading.value = false;
  }
}

onMounted(() => fetchRooms());
</script>
