<template>
    <div>
      <h2>Set up Two-Factor Authentication</h2>
  
      <div v-html="qrCode" />
      <p>Or manually enter the secret:</p>
      <strong>{{ secret }}</strong>
  
      <form @submit.prevent="submit">
        <input v-model="form.code" placeholder="Enter code from app" />
        <button type="submit">Enable 2FA</button>
      </form>
      <div v-if="form.errors.code" class="text-red-500">{{ form.errors.code }}</div>
    </div>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  const props = defineProps(['qrCode', 'secret'])
  
  const form = useForm({ code: '' })
  
  function submit() {
    form.post('/2fa/setup') // you'll verify the initial code here
  }
  </script>
  