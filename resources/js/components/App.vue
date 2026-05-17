<template>
  <div>
    <nav v-if="token" class="navbar navbar-dark bg-dark px-4 mb-4">
      <span class="navbar-brand">BookingApp</span>
      <div class="d-flex gap-3">
        <button class="btn btn-outline-light btn-sm" @click="page = 'rooms'">Pokoje</button>
        <button class="btn btn-outline-light btn-sm" @click="page = 'bookings'">Moje rezerwacje</button>
        <button class="btn btn-danger btn-sm" @click="logout">Wyloguj</button>
      </div>
    </nav>

    <div class="container">
      <AuthForm v-if="!token" @logged-in="onLogin" />
      <RoomList v-else-if="page === 'rooms'" @book="openBooking" />
      <BookingForm v-else-if="page === 'booking'" :room="selectedRoom" @done="page = 'bookings'" @cancel="page = 'rooms'" />
      <MyBookings v-else-if="page === 'bookings'" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import AuthForm from './AuthForm.vue';
import RoomList from './RoomList.vue';
import BookingForm from './BookingForm.vue';
import MyBookings from './MyBookings.vue';
import { apiFetch, setUnauthenticatedHandler } from '../api.js';

const token = ref(localStorage.getItem('token'));
const page = ref('rooms');
const selectedRoom = ref(null);

setUnauthenticatedHandler(() => {
  token.value = null;
  page.value = 'rooms';
});

function onLogin(t) {
  token.value = t;
  localStorage.setItem('token', t);
  page.value = 'rooms';
}

function openBooking(room) {
  selectedRoom.value = room;
  page.value = 'booking';
}

async function logout() {
  await apiFetch('/logout', { method: 'POST' });
  token.value = null;
  localStorage.removeItem('token');
}
</script>
