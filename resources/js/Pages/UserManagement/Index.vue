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
import AutoComplete from '@/Components/AutoComplete.vue';

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
import UserTable from './TableComponent/UserTable.vue';

export default {
    props: ["pagination","users", "permissions"],
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
            usersData: this.$props.users,
            permissionsList: this.$props.permissions
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
                <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">User Management</h1>
            </div>
            <div class="flex sm:mt-0 sm:ml-4">
                <!-- <button type="button" class="sm:order-0 order-1 ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:ml-0">Share</button> -->
                <Link :href="route('training.create')" class="order-0 inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 sm:order-1 sm:ml-3">Add User</Link>
            </div>
        </div>
        <div class="mt-2 hidden sm:block">
            <div class="inline-block min-w-full border-b border-gray-200 align-middle p-4">
                <UserTable :users="usersData" :permissionsList="permissionsList" />
                <!-- <AutoComplete :suggestions="suggestions" /> -->
            </div>
        </div>
        
      
    </BreezeAuthenticatedLayout>
</template>
