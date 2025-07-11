<script setup>
import { ref,computed } from 'vue'; 
import BreezeAuthenticatedGuestLayout from '@/Layouts/AuthenticatedGuest.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { UserIcon } from '@heroicons/vue/20/solid'
import { Chart, registerables } from "chart.js";
import { LineChart, BarChart } from 'vue-chart-3';
import { Qalendar } from "qalendar";
import MyTable from './Components/MyTable.vue';
import { Link } from '@inertiajs/inertia-vue3';

Chart.register(...registerables);
const props = defineProps({
    training: Array,
})

</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedGuestLayout>
 
        <section class="mt-3 px-4 py-3 sm:px-6 lg:px-8">
            <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">My Trainings</h1>
                </div>
                <div class="flex sm:mt-0 sm:ml-4">
                    <!-- <button type="button" class="sm:order-0 order-1 ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:ml-0">Share</button> -->
                    <!-- <Link :href="route('training.create')" class="order-0 inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 sm:order-1 sm:ml-3">Add User</Link> -->
                </div>
            </div>
            <div class="mt-2 hidden sm:block overflow-x-auto">
                <div class="inline-block min-w-full border-b border-gray-200 align-middle p-4">
                        <MyTable :training="training" />
                </div>
            </div>
        </section>
        
    </BreezeAuthenticatedGuestLayout>
</template>


