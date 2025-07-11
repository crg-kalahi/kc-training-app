<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { PencilIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  user: Object,
});

const mfaEnabled = ref(Boolean(props.user.mfa_enabled));

function updateMfa() {
  Inertia.post('/profile/mfa', { mfa_enabled: mfaEnabled.value ? 1 : 0 }, {
    onSuccess: () => {
      console.log('MFA updated:', mfaEnabled.value);
    },
  });
}
</script>

<template>
  <Head title="My Profile" />

  <BreezeAuthenticatedLayout>
    <div class="px-4 py-6 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center border-b pb-4 mb-6">
        <h1 class="text-xl font-semibold text-gray-900">My Profile</h1>
        <button
          class="inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-medium text-white hover:bg-purple-500"
        >
          <PencilIcon class="h-4 w-4 mr-2" />
          Edit Profile
        </button>
      </div>

      <div class="bg-white shadow rounded-lg p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Full Name</label>
          <p class="mt-1 text-gray-900">{{ user.full_name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">ID Number</label>
          <p class="mt-1 text-gray-900">{{ user.id_number }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email Address</label>
          <p class="mt-1 text-gray-900">{{ user.email }}</p>
        </div>

        <div v-if="user.mobile_no">
          <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
          <p class="mt-1 text-gray-900">{{ user.mobile_no }}</p>
        </div>

        <div v-if="user.division">
          <label class="block text-sm font-medium text-gray-700">Division</label>
          <p class="mt-1 text-gray-900">{{ user.division }}</p>
        </div>

        <div v-if="user.section">
          <label class="block text-sm font-medium text-gray-700">Section</label>
          <p class="mt-1 text-gray-900">{{ user.section }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email Verified</label>
          <p class="mt-1 text-gray-900">
            {{ user.email_verified_at ? new Date(user.email_verified_at).toLocaleString() : 'Not Verified' }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Member Since</label>
          <p class="mt-1 text-gray-900">{{ new Date(user.created_at).toLocaleDateString() }}</p>
        </div>

        <!-- MFA Section -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Multi-Factor Authentication (MFA)</label>
          <div class="flex items-center space-x-6">
            <label class="flex items-center space-x-2">
              <input
                type="radio"
                :value="true"
                v-model="mfaEnabled"
                @change="updateMfa"
                class="text-purple-600 focus:ring-purple-500"
              />
              <span class="text-gray-700">Enabled</span>
            </label>

            <label class="flex items-center space-x-2">
              <input
                type="radio"
                :value="false"
                v-model="mfaEnabled"
                @change="updateMfa"
                class="text-purple-600 focus:ring-purple-500"
              />
              <span class="text-gray-700">Disabled</span>
            </label>
          </div>
          <p class="mt-2 text-sm text-gray-500">
            MFA adds an extra layer of security to your account.
          </p>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>
