<template>
  <div>
    <h2 class="mb-4">Panel admina — wszystkie rezerwacje</h2>
    <div v-if="loading" class="text-center">Ładowanie...</div>
    <div v-else-if="bookings.length === 0" class="alert alert-info">Brak rezerwacji.</div>
    <div v-else class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Użytkownik</th>
            <th>Pokój</th>
            <th>Od</th>
            <th>Do</th>
            <th>Uczestnicy</th>
            <th>Status</th>
            <th>Notatka admina</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in bookings" :key="booking.id">
            <td>{{ booking.user?.name ?? '—' }}</td>
            <td>{{ booking.room?.name ?? '—' }}</td>
            <td>{{ formatDate(booking.starts_at) }}</td>
            <td>{{ formatDate(booking.ends_at) }}</td>
            <td>{{ booking.participants_count }}</td>
            <td>
              <select class="form-select form-select-sm" v-model="booking.status" style="width: auto">
                <option value="pending">pending</option>
                <option value="confirmed">confirmed</option>
                <option value="cancelled">cancelled</option>
              </select>
            </td>
            <td>
              <input class="form-control form-control-sm" v-model="booking.admin_note" placeholder="Notatka..." />
            </td>
            <td>
              <button
                class="btn btn-sm btn-primary"
                :disabled="saving === booking.id"
                @click="save(booking)"
              >
                {{ saving === booking.id ? '...' : 'Zapisz' }}
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
const saving = ref(null);
const error = ref(null);

onMounted(async () => {
  try {
    const { res, data } = await apiFetch('/admin/bookings');
    if (res.ok) bookings.value = data;
    else error.value = 'Nie udało się załadować rezerwacji.';
  } catch {
    error.value = 'Nie udało się załadować rezerwacji.';
  } finally {
    loading.value = false;
  }
});

async function save(booking) {
  saving.value = booking.id;
  error.value = null;
  try {
    const { res, data } = await apiFetch(`/admin/bookings/${booking.id}/status`, {
      method: 'PATCH',
      body: JSON.stringify({ status: booking.status, admin_note: booking.admin_note }),
    });
    if (!res.ok) error.value = data.message ?? 'Błąd zapisu.';
  } finally {
    saving.value = null;
  }
}

function formatDate(dt) {
  return new Date(dt).toLocaleString('pl-PL');
}
</script>
