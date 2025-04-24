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
    props: ["participants"],
    computed: {
   
    },
    components: { QrCodeIcon, Modal },
    data(){
        return{}
    },
    mounted() {
        console.log(this.participants)
    },
    methods:{
        formattedDateRange(from,to) {
            const startDate = moment(from).format('MMMM D, YYYY');
            const endDate = moment(to).format('MMMM D, YYYY');
            return `${startDate} - ${endDate}`;
        }
    }
}
</script>
<template>
    <Head title="Training Attendance" />
    <div class="p-5">
        
        <div class="relative mb-4">
            <!-- Logos on top-left -->
            <div class="absolute left-0 top-0 flex gap-4">
                <img src="/images/dswd.jpg" alt="Logo 1" class="h-12 w-auto">
                <img src="/images/bp.png" alt="Logo 2" class="h-12 w-auto">
            </div>

            <!-- Centered headers -->
            <div class="text-center mt-5">
                <h3 class="text-xl font-bold mt-10 mb-5">ATTENDANCE SHEET</h3>
                <h5 class="font-bold">{{ participants[0].training.title }}</h5>
                <h6>{{ formattedDateRange(participants[0].training.date_from, participants[0].training.date_to) }}</h6>
                <h5>{{ participants[0].training.venue }}</h5>
            </div>
        </div>
        <br>
        <table >
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left" rowspan="2">NO.</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" rowspan="2">NAME</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" rowspan="2">OFFICE/SERVICE/ DIVISION/UNIT</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" rowspan="2">POSITION</th>
                    <th class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap" rowspan="2">SEX (M/F)</th>
                    <th class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap" rowspan="2">EMAIL ADDRESS</th>
                    <th class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap" rowspan="2">CONTACT NO.</th>
                    <th class="border border-gray-300 px-4 py-2 text-center" colspan="2">AM</th>
                    <th class="border border-gray-300 px-4 py-2 text-center" colspan="2">PM</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" rowspan="2">SIGNATURE</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" rowspan="2">REMARKS/OTHER INFORMATION</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-center">IN</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">OUT</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">IN</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">OUT</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example row -->
                <tr class="bg-white hover:bg-gray-100" v-for="(participant, index) in participants" :key="participant.id">
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ index + 1 }}</td> <!-- Display the count -->
                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ participant.full_name }}</td>
                    <td class="border border-gray-300 px-4 py-2"> </td>
                    <td class="border border-gray-300 px-4 py-2"> </td>
                    <td class="border border-gray-300 px-4 py-2 text-center"> </td>
                    <td class="border border-gray-300 px-4 py-2">{{ participant.email }}</td>
                    <td class="border border-gray-300 px-4 py-2"> </td>
                    <td class="border border-gray-300 px-4 py-2 text-center"> </td>
                    <td class="border border-gray-300 px-4 py-2 text-center"> </td>
                    <td class="border border-gray-300 px-4 py-2 text-center"> </td>
                    <td class="border border-gray-300 px-4 py-2 text-center"> </td>
                    <td class="border border-gray-300 px-4 py-2"> </td>
                    <td class="border border-gray-300 px-4 py-2"> </td>
                </tr>
                <!-- Additional rows as needed -->
            </tbody>
            <tfoot>
                <!-- Optional footer rows here -->
            </tfoot>
        </table>
        <p class="text-center" ><span >***NOTHING   FOLLOWS*** </span></p>
        <br>
       <p class="text-center"><b><u contenteditable="true">SIGNATORIES HERE</u></b></p>
       <p class="text-center" ><span contenteditable="true">POSITION</span></p>
       

       
    </div>
</template>

<style>
    .grecaptcha-badge {
        visibility: hidden;
    }

    @page {
        size: A4 landscape; /* Change to A4 landscape if needed */
        margin: 20mm; /* Adjust margins as needed */
    }

    *{
        font-family: Arial, Helvetica, sans-serif
    }

    body {
        margin: 0; /* Remove default body margins */
        padding: 0; /* Remove default body padding */
    }

    table {
        border-collapse: collapse; /* Remove gaps between cells */
        width: 100%; /* Use full width of the page */
    }
    
    th, td {
        page-break-inside: avoid; /* Prevent page breaks inside cells */
        font-size: 10px; /* Adjust font size for print */
        border: 1px solid gray; /* Ensure borders are visible */
    }

</style>
