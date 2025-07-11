<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md space-y-6">
      <h2 class="text-2xl font-bold text-gray-800 text-center">
        Two-Factor Authentication
      </h2>

      <div class="text-center space-y-2">
        <div v-html="qrCode" class="inline-block" />
        <p class="text-sm text-gray-600">Scan QR using your Google Authenticator App.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <input
          v-model="form.code"
          placeholder="Enter 6-digit code"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
        />
        <button
          type="submit"
          class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-150"
        >
          Verify
        </button>
      </form>

      <div v-if="form.errors.code" class="text-red-500 text-sm text-center">
        {{ form.errors.code }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps(['qrCode', 'secret'])

const form = useForm({ code: '' })

function submit() {
  form.post('/mfa')
}
</script>
