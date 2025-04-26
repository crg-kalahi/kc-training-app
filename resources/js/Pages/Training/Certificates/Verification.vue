<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center px-4">
      <div class="bg-white shadow-2xl rounded-2xl p-8 max-w-xl w-full text-center border border-blue-100">
        <h1 class="text-3xl font-bold text-blue-800 mb-4">🎓 Certificate Verified</h1>
        <p class="text-lg text-gray-700 mb-6">
          This certificate has been <span class="font-semibold text-green-600">awarded</span> to:
        </p>
        <p class="text-2xl font-semibold text-gray-900 mb-2">
          {{ fullname }}
        </p>
        <div class="mt-6">
          <span class="inline-block px-4 py-2 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
            Certificate ID: {{ token }}
          </span>
        </div>
        <div class="mt-8">
          <p class="text-sm text-gray-400">Verified by DSWD Field Office Caraga TRAIN IS • {{ new Date().getFullYear() }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    token: String,
    fullname: String,
  });
  </script>
  