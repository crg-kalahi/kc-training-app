<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md space-y-6">
      <h2 class="text-2xl font-bold text-gray-800 text-center">
        Set up Two-Factor Authentication
      </h2>

      <div class="text-center space-y-4">
        <div class="flex justify-center">
          <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <p class="text-gray-700 font-medium">
          QR code sent to your email
        </p>
        <p class="text-sm text-gray-600">
          We've sent the QR code to <strong>{{ email }}</strong>. Please check your inbox and follow the instructions in the email to complete the setup.
        </p>
        <p class="text-sm text-gray-500 italic">
          {{ message }}
        </p>
      </div>

      <div class="border-t pt-4">
        <p class="text-sm text-gray-600 mb-4">
          Once you've scanned the QR code from your email, enter the 6-digit code from your authenticator app below:
        </p>
        <form @submit.prevent="submit" class="space-y-4">
          <input
            v-model="form.code"
            placeholder="Enter 6-digit code from app"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            maxlength="6"
          />
          <button
            type="submit"
            class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-150"
          >
            Enable 2FA
          </button>
        </form>
        <div v-if="form.errors.code" class="text-red-500 text-sm text-center mt-2">
          {{ form.errors.code }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps(['email', 'message'])

const form = useForm({ code: '' })

function submit() {
  form.post('/2fa/setup')
}
</script>
  