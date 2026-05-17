<template>
  <div>
    <h2 class="mb-4">Moje rezerwacje</h2>
    <div v-if="loading" class="text-center">Ładowanie...</div>
    <div v-else-if="bookings.length === 0" class="alert alert-info">Brak rezerwacji.</div>
    <div v-else class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Pokój</th>
            <th>Od</th>
            <th>Do</th>
            <th>Uczestnicy</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in bookings" :key="booking.id">
            <td>{{ booking.room?.name ?? '—' }}</td>
            <td>{{ formatDate(booking.starts_at) }}</td>
            <td>{{ formatDate(booking.ends_at) }}</td>
            <td>{{ booking.participants_count }}</td>
            <td>
              <span class="badge" :class="statusClass(booking.status)">{{ booking.status }}</span>
            </td>
            <td>
              <button
                v-if="booking.status !== 'cancelled'"
                class="btn btn-sm btn-danger"
                :disabled="cancelling === booking.id"
                @click="cancel(booking)"
              >
                Anuluj
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../api.js';

const bookings = ref([]);
const loading = ref(true);
const cancelling = ref(null);
const error = ref(null);

onMounted(async () => {
  await fetchBookings();
});

async function fetchBookings() {
  try {
    const { res, data } = await apiFetch('/bookings');
    if (res.ok) bookings.value = data;
    else error.value = 'Nie udało się załadować rezerwacji.';
  } catch {
    error.value = 'Nie udało się załadować rezerwacji.';
  } finally {
    loading.value = false;
  }
}

async function cancel(booking) {
  cancelling.value = booking.id;
  try {
    const { res } = await apiFetch(`/bookings/${booking.id}/cancel`, { method: 'PATCH' });
    if (res.ok) booking.status = 'cancelled';
    else error.value = 'Nie udało się anulować rezerwacji.';
  } finally {
    cancelling.value = null;
  }
}

function formatDate(datatime) {
  return new Date(datatime).toLocaleString('pl-PL');
}

function statusClass(status) {
  return { pending: 'bg-warning text-dark', confirmed: 'bg-success', cancelled: 'bg-secondary' }[status] ?? 'bg-secondary';
}
</script>
