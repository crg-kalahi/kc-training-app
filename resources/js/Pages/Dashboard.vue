<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
  } from '@headlessui/vue'
import { ChevronRightIcon, EllipsisVerticalIcon, UserIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  participants: Array
})
const projects = [
    {
      id: 1,
      title: 'Crisis Communication Workshop - Batch 1',
      initials: 'CCW',
      venue: 'Goat 2 Geder Hotel and Restaurant, Butuan City',
      members: [
        {
          name: 'Dries Vincent',
          handle: 'driesvincent',
          imageUrl:
            'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
        },
        {
          name: 'Lindsay Walton',
          handle: 'lindsaywalton',
          imageUrl:
            'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
        },
        {
          name: 'Courtney Henry',
          handle: 'courtneyhenry',
          imageUrl:
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
        },
        {
          name: 'Tom Cook',
          handle: 'tomcook',
          imageUrl:
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
        },
      ],
      totalMembers: 12,
      lastUpdated: 'March 17, 2020',
      pinned: true,
      bgColorClass: 'bg-pink-600',
    },
    // More projects...
  ]
  const pinnedProjects = projects.filter((project) => project.pinned)
  const stats = [
    { icon: UserIcon, name: 'Female', value: props.participants.filter(x => x.is_female && x.is_internal).length, type: 'Internal', changeType: 'positive' },
    { icon: UserIcon, name: 'Male', value: props.participants.filter(x => !x.is_female && x.is_internal).length, type: 'Internal', changeType: 'positive' },
    { icon: UserIcon, name: 'Female', value: props.participants.filter(x => x.is_female && !x.is_internal).length, type: 'External', changeType: 'negative' },
    { icon: UserIcon, name: 'Male', value: props.participants.filter(x => !x.is_female && !x.is_internal).length, type: 'External', changeType: 'negative' },
  ]
  import { LineChart, PieChart } from 'vue-chart-3';
  // import { valueOr }
  import { Chart, registerables } from "chart.js";
  Chart.register(...registerables);
  // const ChartNumberUtils = (config) => {
  //   var cfg = config || {};
  //   var min = valueOrDefault(cfg.min, 0);
  //   var max = valueOrDefault(cfg.max, 100);
  //   var from = valueOrDefault(cfg.from, []);
  //   var count = valueOrDefault(cfg.count, 8);
  //   var decimals = valueOrDefault(cfg.decimals, 8);
  //   var continuity = valueOrDefault(cfg.continuity, 1);
  //   var dfactor = Math.pow(10, decimals) || 0;
  //   var data = [];
  //   var i, value;

  //   for (i = 0; i < count; ++i) {
  //     value = (from[i] || 0) + this.rand(min, max);
  //     if (this.rand() <= continuity) {
  //       data.push(Math.round(dfactor * value) / dfactor);
  //     } else {
  //       data.push(null);
  //     }
  //   }

  //   return data;
  // }
  // const chartData = {
  //  labels: ['red', 'orange'],
  //  datasets: [
  //   {
  //     data: ChartNumberUtils({count: 5, min: 0, max: 100}),
  //     backgroundColor: '#FF6B6C',
  //   },
  //   {
  //     data: ChartNumberUtils({count: 10, min: 0, max: 100}),
  //     backgroundColor: '#FF6B6C',
  //   },
  // ]
  // }
</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
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
        <dl class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div class="relative overflow-hidden rounded-lg bg-white px-4 shadow">
              <PieChart :chartData="chartData"/>
          </div>
          <div class="relative overflow-hidden rounded-lg bg-white px-4 shadow">

          </div>
        </dl>
    </section>
    </BreezeAuthenticatedLayout>
</template>
