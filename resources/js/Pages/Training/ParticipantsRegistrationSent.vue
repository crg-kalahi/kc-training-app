<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import {
  CheckCircleIcon,
  EnvelopeOpenIcon,
  ClipboardDocumentCheckIcon,
  HomeIcon,
} from '@heroicons/vue/24/outline';
</script>

<template>
  <Head title="Registration submitted" />

  <div
    class="min-h-screen bg-gradient-to-b from-slate-100 via-sky-50/80 to-indigo-100/90 px-4 py-12 sm:px-6 lg:px-8"
  >
    <div class="mx-auto flex max-w-lg flex-col items-center">
      <!-- Success card -->
      <div
        class="w-full overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/50 ring-1 ring-slate-900/5"
      >
        <div
          class="relative bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-10 text-center sm:px-10"
        >
          <div
            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/15 backdrop-blur"
          >
            <CheckCircleIcon class="h-10 w-10 text-white" aria-hidden="true" />
          </div>
          <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
            You’re registered
          </h1>
          <p class="mt-2 text-base text-indigo-100">
            Your training registration was received successfully.
          </p>
        </div>

        <div class="space-y-6 px-6 py-8 sm:px-10">
          <p class="text-center text-slate-600">
            Thank you for signing up. The organizers have your details on file.
          </p>

          <ul class="space-y-4 text-sm text-slate-700">
            <li class="flex gap-3">
              <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"
              >
                <EnvelopeOpenIcon class="h-5 w-5" aria-hidden="true" />
              </span>
              <span>
                <span class="font-medium text-slate-900">Check your email</span>
                — confirmations, schedules, or certificates are often sent to the address you
                provided. Add the sender to your contacts if messages go to spam.
              </span>
            </li>
            <li class="flex gap-3">
              <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"
              >
                <ClipboardDocumentCheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
              <span>
                <span class="font-medium text-slate-900">Keep your details handy</span>
                — bring a valid ID if the training requires on-site verification.
              </span>
            </li>
          </ul>

          <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-center">
            <Link
              href="/"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              <HomeIcon class="h-5 w-5" aria-hidden="true" />
              Back to home
            </Link>
            <Link
              :href="route('login')"
              class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Staff login
            </Link>
          </div>
        </div>
      </div>

      <p class="mt-8 text-center text-xs text-slate-500">
        If you need to correct your registration, contact the training secretariat or organizer.
      </p>
    </div>
  </div>
</template>
