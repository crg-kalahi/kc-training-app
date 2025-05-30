<script setup>
import { ref, computed } from 'vue';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreezeAuthenticatedGuestLayout from '@/Layouts/AuthenticatedGuest.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { UserIcon } from '@heroicons/vue/20/solid';
import { Chart, registerables } from "chart.js";
import { LineChart, BarChart } from 'vue-chart-3';
import { Qalendar } from "qalendar";

Chart.register(...registerables);

const props = defineProps({
  participants: Array,
  plByMonth: Array,
  events: Array,
  permissions: Array,
  totalTrainings: Number,
  totalParticipants: Number,
  internalParticipants: Number,
  externalParticipants: Number,
  upcomingTrainingsCount: Number,
  latestTraining: Object,
  averageTrainingsPerMonth: Number,
  evaluations: Array,
  officeRepSummary: Array,
});

const stats = [
  { icon: UserIcon, name: 'Female', value: props.participants.filter(x => x.is_female && x.is_internal).length, type: 'Internal', changeType: 'positive' },
  { icon: UserIcon, name: 'Male', value: props.participants.filter(x => !x.is_female && x.is_internal).length, type: 'Internal', changeType: 'positive' },
  { icon: UserIcon, name: 'Female', value: props.participants.filter(x => x.is_female && !x.is_internal).length, type: 'External', changeType: 'negative' },
  { icon: UserIcon, name: 'Male', value: props.participants.filter(x => !x.is_female && !x.is_internal).length, type: 'External', changeType: 'negative' },
];

const data = ref(props.plByMonth);
const events = ref(props.events);
const officeRepSummary = ref(props.officeRepSummary);

const config = {
  week: { startsOn: 'sunday' },
  month: { showTrailingAndLeadingDates: false },
  defaultMode: 'month',
  isSilent: true,
  showCurrentTime: true,
};

const hasPermission = (permission) => props.permissions.includes(permission);

const currentLayout = computed(() => {
  return hasPermission('GUEST - Login') ? BreezeAuthenticatedGuestLayout : BreezeAuthenticatedLayout;
});
</script>

<template>
  <Head title="Dashboard" />
  <component :is="currentLayout">
    <!-- Page Title -->
    <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-6 text-gray-900">📊 Dashboard</h1>
      </div>
    </div>

    <!-- Participants Overview -->
    <div class="px-4 py-3 sm:px-6 lg:px-8 mt-4">
      <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-2 mb-4">Overall Trained Participants</h3>
      <dl class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div
          v-for="item in stats"
          :key="item.name"
          class="relative overflow-hidden rounded-xl bg-gradient-to-r from-white to-gray-50 px-4 pt-5 shadow hover:shadow-lg transition transform hover:scale-105 border border-gray-200"
        >
          <dt>
            <div
              :class="[
                'absolute rounded-full p-3',
                item.name === 'Male'
                  ? 'bg-gradient-to-r from-indigo-500 to-indigo-600'
                  : 'bg-gradient-to-r from-pink-500 to-pink-600'
              ]"
            >
              <component :is="item.icon" class="h-6 w-6 text-white" aria-hidden="true" />
            </div>
            <p class="ml-16 truncate text-sm font-medium text-gray-600">{{ item.name }}</p>
          </dt>
          <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
            <p class="text-3xl font-bold text-gray-900">{{ item.value }}</p>
            <p
              :class="[
                item.type === 'Internal' ? 'text-green-600' : 'text-red-600',
                'ml-2 text-sm font-semibold'
              ]"
            >
              {{ item.type }}
            </p>
          </dd>
        </div>
      </dl>
    </div>

    <!-- Training Summary -->
    <section class="mt-4 px-4 py-3 sm:px-6 lg:px-8">
      <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-2 mb-4 flex items-center">
        📅 Training Summary
      </h3>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Bar Chart -->
        <div class="rounded-xl bg-white shadow p-4 h-[400px] border border-gray-200">
          <BarChart
            :chart-data="{
              labels: data.map(item => `${item.mname} ${item.yr}`),
              datasets: [
                {
                  label: 'Total Trainings',
                  backgroundColor: '#90e0ef',
                  data: data.map(item => item.total_trainings)
                }
              ]
            }"
            :options="{ responsive: true, maintainAspectRatio: false }"
            style="height: 100%;"
          />
        </div>
        <!-- Calendar -->
        <div class="rounded-xl bg-white shadow p-4 h-[400px] border border-gray-200">
          <Qalendar :events="events" :config="config" class="h-full" />
        </div>
      </div>
    </section>

    <!-- Key Summary -->
    <div class="px-4 py-6 sm:px-6 lg:px-8 mt-6">
      <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-2 mb-4">
        🔑 Key Summary
      </h3>
      <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3 lg:grid-cols-6">
        <div
          v-for="(label, index) in [
            { title: 'Total Trainings', value: $page.props.totalTrainings },
            { title: 'Total Participants', value: $page.props.totalParticipants },
            { title: 'Internal Participants', value: $page.props.internalParticipants, color: 'text-green-600' },
            { title: 'External Participants', value: $page.props.externalParticipants, color: 'text-red-600' },
            { title: 'Upcoming Trainings', value: $page.props.upcomingTrainingsCount, color: 'text-indigo-600' },
            { title: 'Avg Trainings/Month', value: $page.props.averageTrainingsPerMonth }
          ]"
          :key="index"
          class="overflow-hidden rounded-xl bg-white px-6 py-6 shadow hover:shadow-lg transition transform hover:scale-105 border border-gray-200"
        >
          <dt class="text-sm font-medium text-gray-600 truncate">{{ label.title }}</dt>
          <dd
            class="mt-1 text-3xl font-bold"
            :class="label.color || 'text-gray-900'"
          >
            {{ label.value }}
          </dd>
        </div>
      </dl>
    </div>

    <!-- Evaluations by Office Representative -->
    <div class="px-4 py-6 sm:px-6 lg:px-8 mt-6">
      <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-2 mb-4">
        🏢 Evaluations by Office Representative
      </h3>
      <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="(item, index) in officeRepSummary"
          :key="index"
          class="overflow-hidden rounded-xl bg-white px-6 py-6 shadow hover:shadow-lg transition transform hover:scale-105 border border-gray-200"
        >
          <dt class="text-sm font-medium text-gray-600 truncate">{{ item.office_rep_title }}</dt>
          <dd class="mt-1 text-3xl font-bold text-gray-900">{{ item.total_evaluations }}</dd>
        </div>
      </dl>
    </div>

    <!-- Latest Trainings -->
    <div class="px-4 py-6 sm:px-6 lg:px-8 mt-6">
      <h4 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-2 mb-4">
        📝 Latest Trainings
      </h4>
      <ul class="space-y-4">
        <li
          v-for="training in latestTraining"
          :key="training.title + training.date_from"
          class="border rounded-xl p-4 bg-white shadow hover:shadow-lg transition transform hover:scale-105 border-gray-200"
        >
          <h5 class="text-md font-semibold text-indigo-600 mb-1 truncate">{{ training.title }}</h5>
          <p class="text-sm text-gray-500">
            <time>
              {{ new Date(training.date_from).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}
            </time>
          </p>
        </li>
      </ul>
    </div>

    <section><br></section>
  </component>
</template>
