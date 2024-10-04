<script setup>
import { ref,computed } from 'vue'; 
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreezeAuthenticatedGuestLayout from '@/Layouts/AuthenticatedGuest.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { UserIcon } from '@heroicons/vue/20/solid'
import { Chart, registerables } from "chart.js";
import { LineChart, BarChart } from 'vue-chart-3';
import { Qalendar } from "qalendar";


Chart.register(...registerables);

const props = defineProps({
  participants: Array,
  plByMonth: Array,
  events: Array,
  permissions: Array
})

const stats = [
  { icon: UserIcon, name: 'Female', value: props.participants.filter(x => x.is_female && x.is_internal).length, type: 'Internal', changeType: 'positive' },
  { icon: UserIcon, name: 'Male', value: props.participants.filter(x => !x.is_female && x.is_internal).length, type: 'Internal', changeType: 'positive' },
  { icon: UserIcon, name: 'Female', value: props.participants.filter(x => x.is_female && !x.is_internal).length, type: 'External', changeType: 'negative' },
  { icon: UserIcon, name: 'Male', value: props.participants.filter(x => !x.is_female && !x.is_internal).length, type: 'External', changeType: 'negative' },
]

const data = ref(props.plByMonth);
const events = ref(props.events);

const config=  {
      week: {
        startsOn: 'sunday',
      },
      month: {
        // Hide leading and trailing dates in the month view (defaults to true when not set)
        showTrailingAndLeadingDates: false,
      },
      defaultMode: 'month',
      isSilent: true,
      showCurrentTime: true,
}


const hasPermission = (permission) => {
  return props.permissions.includes(permission);
}

const currentLayout = computed(() => {
  return hasPermission('GUEST - Login') ? BreezeAuthenticatedGuestLayout : BreezeAuthenticatedLayout;
});

</script>

<template>
    <Head title="Dashboard" />

    <component :is="currentLayout">
        <!-- Page title & actions -->
    <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div class="min-w-0 flex-1">
            <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Dashboard</h1>
        </div>
        <div class="mt-4 flex sm:mt-0 sm:ml-4">
            <!-- <button type="button" class="sm:order-0 order-1 ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:ml-0">Share</button> -->
            <!-- <button type="button" class="order-0 inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 sm:order-1 sm:ml-3">Create</button> -->
        </div>
    </div>
    <div class="px-4 py-3 sm:px-6 lg:px-8 mt-3">
      <h3 class="text-base font-semibold leading-6 text-gray-900">Overall Trained Participants</h3>
      <dl class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div v-for="item in stats" :key="item.id" class="relative overflow-hidden rounded-lg bg-white px-4 pt-5 shadow sm:px-6 sm:pt-6">
          <dt>
            <div :class="['absolute rounded-md p-3', item.name == 'Male' ? 'bg-indigo-500' : 'bg-pink-500']">
              <component :is="item.icon" class="h-6 w-6 text-white" aria-hidden="true" />
            </div>
            <p class="ml-16 truncate text-sm font-medium text-gray-500">{{ item.name }}</p>
          </dt>
          <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
            <p class="text-2xl font-semibold text-gray-900">{{ item.value }}</p>
            <p :class="[item.type === 'Internal' ? 'text-green-600' : 'text-red-600', 'ml-2 flex items-baseline text-sm font-semibold']">
              {{ item.type }}
            </p>
          </dd>
        </div>
      </dl>
    </div>
    <section class="mt-3 px-4 py-3 sm:px-6 lg:px-8">
        <h3 class="text-base font-semibold leading-6 text-gray-900">Training Summary</h3>
        <dl class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-1">
          <div class="relative overflow-hidden rounded-lg bg-white px-4 shadow">
              <BarChart
              :chart-data="{
                labels: data.map(item => `${item.mname} ${item.yr}`),
                datasets: [
                  {
                    label: 'TOTAL TRAININGS',
                    backgroundColor: '#90e0ef',
                    data: data.map(item => item.total_trainings)
                  },
                ]
              }"
              :options="{
                responsive: true,
                maintainAspectRatio: false
              }"
            />



            
          </div>
          <Qalendar 
              :events="events"
              :config="config"
            />
        </dl>
    </section>
    <section>
      <br>
    </section>
  </component>
</template>


