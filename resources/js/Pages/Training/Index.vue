<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
  } from '@headlessui/vue'
import { ChevronRightIcon, EllipsisVerticalIcon, StarIcon } from '@heroicons/vue/20/solid'
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
</script>

<script>
import moment from 'moment';
import { QrCodeIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
export default {
    props: ["pagination","certificateRequestsCount"],
    computed: {
        ratings(){
            const rates = ['Poor', 'Fair', 'Satisfactory', 'Very Satisfactory', 'Excellent']
            if(this.eventDetail == null) return rates.map(() => 'text-white')
            const { evaluation_status } = this.eventDetail
            if(evaluation_status == 'No Evaluation') return rates.map(() => 'text-white')
            let flag = rates.findIndex(x => x == evaluation_status)
            return rates.map(function(item, index){
                return flag >= index ? 'text-yellow-500' : 'text-white'
            })
        },
        user() {
            return this.$page.props.auth.user;
        },
        events() {
            const { id } = this.user;
            return this.pagination.data.map(function (obj) {
                const date_e = `${moment(obj.date_from, "YYYY-MM-DD").format("MMM DD, YYYY")} - ${moment(obj.date_to, "YYYY-MM-DD").format("MMM DD, YYYY")}`;
                const { facilitators } = obj;
                let bgColorClass = "bg-red-500"; // Green if included as facilitator sa event, Gray if not
                if (facilitators.length) {
                    bgColorClass = facilitators.map(x => x.id).includes(id) ? "bg-green-500" : "bg-gray-500";
                }
                return { ...obj, bgColorClass, totalMembers: 0, date_e };
            });
        }
    },
    components: { QrCodeIcon, Modal, StarIcon },
    data(){
        return{
            toggleTrainingDetails: false,
            eventDetail: null,
        }
    },
    methods:{
        showTrainingDetails(event){
            this.eventDetail = event
            this.toggleTrainingDetails = true
        }
    }
}
</script>

<template>
    <Head title="Events" />

    <BreezeAuthenticatedLayout>
        <!-- Page title & actions -->
    <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div class="min-w-0 flex-1">
            <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1>
        </div>
        <div class="flex sm:mt-0 sm:ml-4">
            <div class="relative">
                <Link :href="route('training.certificate-request')" class="sm:order-0 order-1 ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:ml-0">Certificate Request</Link>
                <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                    {{certificateRequestsCount}}
                </span>
            </div>
            <Link :href="route('training.create')" class="order-0 inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 sm:order-1 sm:ml-3">Create Training</Link>
        </div>
    </div>
        <div class="mt-2 hidden sm:block">
        <div class="inline-block min-w-full border-b border-gray-200 align-middle">
            <table class="min-w-full">
            <thead>
                <tr class="border-t border-gray-200">
                <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900" scope="col">
                    <span class="lg:pl-2">Title and Venue</span>
                </th>
                <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900 md:table-cell" scope="col">Rating</th>
                <th class="hidden border-b border-gray-200 bg-gray-50 px-6 py-3 text-right text-sm font-semibold text-gray-900 md:table-cell" scope="col">Facilitators</th>
                <th class="hidden border-b border-gray-200 bg-gray-50 px-6 py-3 text-right text-sm font-semibold text-gray-900 md:table-cell" scope="col">Date</th>
                <th class="border-b border-gray-200 bg-gray-50 py-3 pr-6 text-right text-sm font-semibold text-gray-900" scope="col" />
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <tr v-for="event in events" :key="event.id">
                    <td class="w-full max-w-0 whitespace-nowrap px-6 py-3 text-sm font-medium text-gray-900">
                        <div class="flex items-center space-x-3 lg:pl-2">
                        <div :class="[event.bgColorClass, 'h-2.5 w-2.5 flex-shrink-0 rounded-full']" aria-hidden="true" />
                        <div type="button" @click="showTrainingDetails(event)" class="truncate hover:text-gray-600 cursor-pointer">
                            <p class="truncate">
                            {{ event.title }}
                            {{ ' ' }}
                            </p>
                            <span class="font-normal text-gray-500">@ {{ event.venue }}</span>
                        </div>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-3 text-left text-sm text-gray-500 md:table-cell">{{ event.evaluation_status }}</td>
                    <td class="px-6 py-3 text-sm font-medium text-gray-500">
                        <div class="flex justify-center items-center space-x-2">
                        <div class="flex flex-shrink-0 -space-x-1">
                            <img v-for="member in event.facilitators" :key="member.handle" :class="['h-6 w-6 max-w-none rounded-full ring-2 ring-white', member.id == user.id ? 'ring ring-green-500' : ''] " :src="member.avatar" :alt="member.name" />
                        </div>
                        <span v-if="event.totalMembers > event.facilitators.length" class="flex-shrink-0 text-xs font-medium leading-5">+{{ project.totalMembers - project.members.length }}</span>
                        </div>
                    </td>
                    <td class="hidden whitespace-nowrap px-6 py-3 text-right text-sm text-gray-500 md:table-cell">{{ event.date_e }}</td>
                    <td class="whitespace-nowrap px-6 py-3 text-right text-sm font-medium">
                        <Link :href="route('training.edit', event.id)" class="text-indigo-600 hover:text-indigo-900">Edit</Link>
                    </td>
                </tr>
            </tbody>
            </table>
        </div>
        <Pagination :links="pagination.links" :from="pagination.from" :to="pagination.to" :total="pagination.total"/>
        <Modal v-model="toggleTrainingDetails">
            <div v-if="eventDetail != null">
                <div class="bg-yellow-100 pt-5 pb-3">
                    <div class="flex justify-center items-center gap-1">
                        <StarIcon v-for="rate in ratings" :key="rate" :class="['h-10 w-10', rate]"/>
                    </div>
                    <p class="max-w-2xl text-sm text-gray-700 text-center mt-1">{{ `${eventDetail.evaluation_status}` }}</p>
                </div>
                <div class="px-4 py-5 border-b border-gray-300">
                    <p class="mb-3 max-w-2xl text-sm text-gray-700">{{ `${eventDetail.date_e}` }}</p>
                    <h3 class="text-base font-bold leading-6 text-gray-900">{{ eventDetail.title }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ `@ ${eventDetail.venue}` }}</p>
                </div>
                <div class="px-4 py-3">
                    <p class="my-1 max-w-2xl text-sm text-gray-500">Facilitated By</p>
                    <h3 v-for="member in eventDetail.facilitators" :key="`${member.handle}-show`" class="text-base font-semibold leading-6 text-gray-900">{{ member.full_name }}</h3>
                </div>
            </div>
        </Modal>
        </div>
    </BreezeAuthenticatedLayout>
</template>
