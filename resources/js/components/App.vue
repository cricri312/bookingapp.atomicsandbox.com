<template>
  <div>
    <nav v-if="token" class="navbar navbar-dark bg-dark px-4 mb-4">
      <span class="navbar-brand">BookingApp</span>
      <div class="d-flex gap-3">
        <button class="btn btn-outline-light btn-sm" @click="page = 'rooms'">Pokoje</button>
        <button class="btn btn-outline-light btn-sm" @click="page = 'bookings'">Moje rezerwacje</button>
        <button v-if="user?.is_admin" class="btn btn-outline-warning btn-sm" @click="page = 'admin'">Admin</button>
        <button class="btn btn-danger btn-sm" @click="logout">Wyloguj</button>
      </div>
    </nav>

    <div class="container">
      <AuthForm v-if="!token" @logged-in="onLogin" />
      <RoomList v-else-if="page === 'rooms'" @book="openBooking" />
      <BookingForm v-else-if="page === 'booking'" :room="selectedRoom" @done="page = 'bookings'" @cancel="page = 'rooms'" />
      <MyBookings v-else-if="page === 'bookings'" />
      <AdminBookings v-else-if="page === 'admin'" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import AuthForm from './AuthForm.vue';
import RoomList from './RoomList.vue';
import BookingForm from './BookingForm.vue';
import MyBookings from './MyBookings.vue';
import AdminBookings from './AdminBookings.vue';
import { apiFetch, setUnauthenticatedHandler } from '../api.js';

const token = ref(localStorage.getItem('token'));
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const page = ref('rooms');
const selectedRoom = ref(null);

setUnauthenticatedHandler(() => {
  token.value = null;
  user.value = null;
  page.value = 'rooms';
});

function onLogin({ token: t, user: u }) {
  token.value = t;
  user.value = u;
  localStorage.setItem('token', t);
  localStorage.setItem('user', JSON.stringify(u));
  page.value = 'rooms';
}

function openBooking(room) {
  selectedRoom.value = room;
  page.value = 'booking';
}

async function logout() {
  await apiFetch('/logout', { method: 'POST' });
  token.value = null;
  user.value = null;
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}
</script>
