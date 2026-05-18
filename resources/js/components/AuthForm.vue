<template>
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
              <button class="nav-link" :class="{ active: mode === 'login' }" @click="mode = 'login'">Logowanie</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" :class="{ active: mode === 'register' }" @click="mode = 'register'">Rejestracja</button>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <form @submit.prevent="submit">
            <div v-if="mode === 'register'" class="mb-3">
              <label class="form-label">Imię i nazwisko</label>
              <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" />
              <div class="invalid-feedback">{{ errors.name?.[0] }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }" />
              <div class="invalid-feedback">{{ errors.email?.[0] }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Hasło</label>
              <input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': errors.password }" />
              <div class="invalid-feedback">{{ errors.password?.[0] }}</div>
            </div>
            <div v-if="mode === 'register'" class="mb-3">
              <label class="form-label">Potwierdź hasło</label>
              <input v-model="form.password_confirmation" type="password" class="form-control" />
            </div>
            <div v-if="errors.general" class="alert alert-danger">{{ errors.general }}</div>
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">
              {{ loading ? 'Ładowanie...' : (mode === 'login' ? 'Zaloguj' : 'Zarejestruj') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';

const emit = defineEmits(['logged-in']);

const mode = ref('login');
const loading = ref(false);
const errors = reactive({});
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

watch(mode, () => Object.keys(errors).forEach(e => delete errors[e]));

async function submit() {
  loading.value = true;
  Object.keys(errors).forEach(e => delete errors[e]);

  const url = mode.value === 'login' ? '/api/login' : '/api/register';
  const body = mode.value === 'login'
    ? { email: form.email, password: form.password }
    : { name: form.name, email: form.email, password: form.password, password_confirmation: form.password_confirmation };

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(body),
    });

    const data = await res.json();

    if (!res.ok) {
      if (data.errors) Object.assign(errors, data.errors);
      else errors.general = data.message ?? 'Wystąpił błąd.';
      return;
    }

    emit('logged-in', { token: data.token, user: data.user });
  } finally {
    loading.value = false;
  }
}
</script>
