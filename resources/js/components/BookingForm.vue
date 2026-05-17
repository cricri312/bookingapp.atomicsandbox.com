<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h2 class="mb-4">Rezerwacja: {{ room.name }}</h2>
      <p class="text-muted">Pojemność: {{ room.capacity }} osób</p>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label">Data i godzina rozpoczęcia</label>
          <input v-model="form.starts_at" type="datetime-local" class="form-control" :class="{ 'is-invalid': errors.starts_at }" />
          <div class="invalid-feedback">{{ errors.starts_at?.[0] }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Data i godzina zakończenia</label>
          <input v-model="form.ends_at" type="datetime-local" class="form-control" :class="{ 'is-invalid': errors.ends_at }" />
          <div class="invalid-feedback">{{ errors.ends_at?.[0] }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Liczba uczestników</label>
          <input v-model.number="form.participants_count" type="number" min="1" :max="room.capacity" class="form-control" :class="{ 'is-invalid': errors.participants_count }" />
          <div class="invalid-feedback">{{ errors.participants_count?.[0] }}</div>
        </div>
        <div v-if="errors.general" class="alert alert-danger">{{ errors.general }}</div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Zapisywanie...' : 'Zarezerwuj' }}
          </button>
          <button type="button" class="btn btn-secondary" @click="emit('cancel')">Anuluj</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { apiFetch } from '../api.js';

const props = defineProps({ room: Object });
const emit = defineEmits(['done', 'cancel']);

const loading = ref(false);
const errors = reactive({});
const form = reactive({ starts_at: '', ends_at: '', participants_count: 1 });

async function submit() {
  loading.value = true;
  Object.keys(errors).forEach(k => delete errors[k]);

  try {
    const { res, data } = await apiFetch('/bookings', {
      method: 'POST',
      body: JSON.stringify({ ...form, room_id: props.room.id }),
    });

    if (!res.ok) {
      if (data.errors) Object.assign(errors, data.errors);
      else errors.general = data.message ?? 'Wystąpił błąd.';
      return;
    }

    emit('done');
  } finally {
    loading.value = false;
  }
}
</script>
