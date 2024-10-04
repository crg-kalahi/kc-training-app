<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import moment from 'moment';
</script>

<script>

import { QrCodeIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
export default {
    props: ["certificateRequests"],
    computed: {
    },
    components: { QrCodeIcon, Modal },
    data(){
        return{}
    },
    mounted() {
        console.log(this.certificateRequests);
    },
    methods:{
        formatDate(dateString) {
            return moment(dateString).format('MMMM D, YYYY h:mm A'); // Example: "September 30, 2024 4:10 PM"
        },
        approveRequest(request){
            const _c = confirm("Are you sure to approve?");
            if(_c){
                Inertia.put(route('training.update-certificate-request'), {
                   id:request.id,
                   status: 1
                });
            }
            
        },
        rejectRequest(request){
            const _c = confirm("Are you sure to approve?");
            if(_c){
                Inertia.put(route('training.update-certificate-request'), {
                   id:request.id,
                   status: 2
                });
            }
            
        }
    }
}
</script>

<template>
    <Head title="Events" />

    <BreezeAuthenticatedLayout>
        <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div class="min-w-0 flex-1">
                <div class="text-xs md:text-sm font-medium breadcrumbs">
                    <ul>
                        <li><Link :href="route('training.index')">Trainings</Link></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="px-4 lg:px-8 sm:px-6 mt-4 sm:flex sm:items-center">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                        <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900">Requested At</th>
                        <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900">Title</th>
                        <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900">Venue</th>
                        <th class="border-b border-gray-200 bg-gray-50 px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="request in certificateRequests" :key="request.id">
                        <td class=" max-w-0 px-6 py-3 text-sm font-medium text-gray-900">{{ request.training_participants.full_name }}</td>
                        <td class=" max-w-0 px-6 py-3 text-sm font-medium text-gray-900">{{ formatDate(request.created_at) }}</td>
                        <td class=" max-w-0 px-6 py-3 text-sm font-medium text-gray-900">{{ request.training.title }}</td>
                        <td class=" max-w-0 px-6 py-3 text-sm font-medium text-gray-900">{{ request.training.venue }}</td>
                        <td>
                            <div>
    <!-- Approved Button -->
                            <button v-if="request.is_approve === 1"
                                    :disabled="true"
                                    class="bg-gray-300 cursor-not-allowed text-sm text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                            Approved
                            </button>

                            <!-- Approve Request Button -->
                            <button v-if="request.is_approve === 0"
                                    @click="approveRequest(request)"
                                    :disabled="request.is_approve === 1 || request.is_approve === 2"
                                    :class="[
                                    'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500',
                                    'text-sm text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200'
                                    ]">
                            Approve Request
                            </button>

                            <!-- Reject Request Button -->
                            <button v-if="request.is_approve === 0"
                                    @click="rejectRequest(request)"
                                    :disabled="request.is_approve === 1 || request.is_approve === 2"
                                    :class="[
                                    'bg-red-500 hover:bg-red-600 focus:ring-red-500',
                                    'text-sm text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200'
                                    ]">
                            Reject Request
                            </button>

                            <!-- Rejected Button -->
                            <button v-if="request.is_approve === 2"
                                    :disabled="true"
                                    class="bg-gray-300 cursor-not-allowed text-sm text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                            Rejected
                            </button>
                        </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </BreezeAuthenticatedLayout>
</template>
