<script setup>
import { ref,computed } from 'vue'; 
import BreezeAuthenticatedGuestLayout from '@/Layouts/AuthenticatedGuest.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { UserIcon } from '@heroicons/vue/20/solid'
import { Chart, registerables } from "chart.js";
import { LineChart, BarChart } from 'vue-chart-3';
import { Qalendar } from "qalendar";


Chart.register(...registerables);
const props = defineProps({
  plByMonth: Array,
  events: Array,
})

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


</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedGuestLayout>
 
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
  </BreezeAuthenticatedGuestLayout>
</template>


