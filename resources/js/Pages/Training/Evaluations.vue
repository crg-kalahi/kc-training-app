<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Dropdown from '@/Components/Dropdown.vue';
import Modal from '@/Components/Modal.vue';
import QRCodeVue3 from "qrcode-vue3";
import { QrCodeIcon, DocumentIcon } from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import moment from 'moment'
import { StarIcon } from '@heroicons/vue/20/solid';
</script>
<script>
  export default {
    props: ["training", "officeRep", "keyTraining", "keyRP", "keyLearning", "resourcePerson", "totalMale", "totalFemale", "overallRating"],
    computed: {
        ratings() {
            return ["Poor", "Fair", "Satisfactory", "V-Satisfactory", "Excellent"];
        },
        stats() {
            const ratings = this.ratings;
            return [
                { name: "Male", stat: this.totalMale },
                { name: "Female", stat: this.totalFemale },
                { name: "Total", stat: this.totalMale + this.totalFemale },
                { name: "Overall Rating", stat: this.overallRating },
            ];
        },
        trainingDate() {
            const { date_from, date_to } = this.training;
            return `${moment(date_from, "YYYY-MM-DD").format("MMM DD, YYYY")} - ${moment(date_to, "YYYY-MM-DD").format("MMM DD, YYYY")}`;
        },
    },
    data() {
        return {
            toggleAlert: false,
            showQrCode: false,
            form: useForm({
                key_training: [],
                key_RP: [],
                key_learning: [],
                is_female: 0,
                training_id: null,
                office_rep: null,
            }),
        };
    },
    mounted() {
        this.form.training_id = this.training.id;
        this.resetForm();
    },
    methods: {
        updateRpArea(indexRp, indexKey, item) {
            console.log(indexRp, indexKey, item);
            // let { key_RP } = this.form
            const { key_rp } = this.form.key_RP[indexRp];
            this.form.key_RP[indexRp].key_rp = key_rp.map(function (obj, index) {
                return { ...obj, stat: index == indexKey ? item : obj.stat };
            });
        },
        resetForm() {
            this.form.key_training = this.keyTraining.map(function ({ id, title }) {
                return { id, title, stat: 3 };
            });
            this.form.key_learning = this.keyLearning.map(function ({ id, title }) {
                return { id, title, answer: "" };
            });
            const key_rps = this.keyRP.map(function ({ id, title }) {
                return { id, title, stat: 3 };
            });
            this.form.key_RP = this.resourcePerson.map(function ({ id, full_name, topic }) {
                return { id, full_name, topic, key_rp: key_rps };
            });
            this.form.is_female = 0;
            this.form.office_rep = this.officeRep.length ? 1 : null;
        },
        submitForm() {
            this.form.post(route("training.evaluation.post"), {
                preserveScroll: true,
                onSuccess: () => { this.resetForm(); this.toggleAlert = false; }
            });
        },
        async downloadExcel(){
          try {
            const { id } = this.training
            window.open(route('training.export-report', {id}), "_blank")
            // await axios.get(route('training.export-report', {id}))
          } catch (error) {
            console.log(error)
          }
        }
    },
    components: { StarIcon }
}
</script>

<template>
    <Head title="Evaluations" />

    <BreezeAuthenticatedLayout>
      <Modal v-model="showQrCode">
        <div class="py-5">
          <QRCodeVue3 value="https://github.com/soldair/node-qrcode"
            :dotsOptions="{
              type: 'extra-rounded',
              color: '#26249a',
            }"
            :height="400"
            :width="400"
            class="flex justify-center"
          />
        </div>
        <div class="text-center bg-gray-50 pb-5 pt-3">
          <h2 class="text-base font-semibold leading-6 text-gray-900">{{ `${training.title}` }}</h2>
          <p class="mt-1 text-sm text-gray-700">{{ `${trainingDate} @ ${training.venue}` }}</p>
        </div>
      </Modal>
      <Modal v-model="toggleAlert">
        <div class="px-3 py-2 bg-yellow-500">
          <h2 class="text-base font-semibold text-white leading-6 tracking-wider">{{ `System Reminder` }}</h2>
        </div>
        <div class="px-3 py-2">
          <p class="text-sm text-gray-700 font-bold">Once the evaluation was submitted, it can't be undone. Proceed?</p>
        </div>
        <div class="border-t border-gray-300 px-3 py-2 flex justify-between items-center">
          <button @click="toggleAlert = false" type="button" class="inline-flex underline rounded-md py-1 px-3 text-center text-sm font-semibold text-gray-700 hover:bg-green-100">
            Review Again
          </button>
          <button @click="submitForm" type="button" class="inline-flex rounded-md bg-indigo-600 py-1 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Proceed
          </button>
        </div>
      </Modal>
        <!-- Page title & actions -->
    <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div class="min-w-0 flex-1">
            <!-- <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1> -->
          <div class="text-xs md:text-sm font-medium breadcrumbs">
              <ul>
                  <li><Link :href="route('training.index')">Training</Link></li>
                  <li><Link :href="route('training.edit', {id: training.id})">{{ training.id }}</Link></li>
                  <li><span class="text-teal-500 tracking-wider font-bold px-3 py-2 rounded-md">Evaluation</span></li>
              </ul>
          </div>
        </div>
    </div>
    <div class="px-4 lg:px-8 sm:px-6 mt-4 sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ `${training.title}` }}</h1>
        <p class="mt-2 text-sm text-gray-700">{{ `${trainingDate} @ ${training.venue}` }}</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="downloadExcel" type="button" class="inline-flex uppercase rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          Generate Report
        </button>
        <!-- <Dropdown>
          <template #trigger>
            <button type="button" class="block rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Evaluation</button>
          </template>
          <template #content>
            <ul class=" divide-y divide-gray-300">
              <li @click="showQrCode = true" class="p-2 text-gray-600 text-sm inline-flex w-full hover:bg-gray-50 cursor-pointer"><QrCodeIcon class="h-5 w-5 mr-2"/>Generate QR</li>
              <li class="p-2 text-gray-600 text-sm inline-flex w-full hover:bg-gray-50 cursor-pointer"><DocumentIcon class="h-5 w-5 mr-2"/>Proceed to Form</li>
            </ul>
          </template>
        </Dropdown> -->
      </div>
    </div>
    <div class="px-6">
      <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div v-for="item in stats" :key="item.name" class="overflow-hidden rounded-lg bg-white px-4 py-3 shadow sm:p-6">
          <dt class="truncate text-sm font-medium text-gray-500">{{ item.name }}</dt>
          <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ item.stat }}</dd>
        </div>
      </dl>
    </div>
    
    <div class="bg-yellow-100 border-t-4 border-dashed border-indigo-500 pt-8 mt-10 px-6">
      <h3 class="text-base font-semibold leading-6 text-gray-900">Evaluation Form</h3>
      <p class="max-w-4xl text-sm text-gray-500">After submitting the evaluation ratings, This can't be undone.</p>
    </div>
    
    <!-- Start -->
    <div class="px-6 pb-10 pt-5 bg-yellow-100">
      <div class="flex gap-x-3">
        <div>
          <label for="gender" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Gender</label>
          <select v-model="form.is_female" id="gender" name="gender" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option :value="0">Male</option>
            <option :value="1">Female</option>
          </select>
        </div>
        <div>
          <label for="office-rep" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Office Representative</label>
          <select v-model="form.office_rep" id="office-rep" name="office-rep" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option v-for="item in officeRep" :key="item.title" :value="item.id">{{ item.title }}</option>
          </select>
        </div>
      </div>
      <div class="mt-2 mb-10">
        <table class="min-w-full divide-y divide-gray-300">
          <thead>
            <tr class="divide-x divide-gray-200">
              <th scope="col" class="py-3.5 pl-4 pr-4 text-left text-sm font-semibold text-gray-900 sm:pl-0">Key Areas: Training</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Poor</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Fair</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Satisfactory</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Very Satisfactory</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Excellent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="area,index in form.key_training" :key="area.title" :class="[index % 2 === 0 ? 'bg-gray-50' : '', 'divide-x divide-gray-200']">
              <td class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-2">{{ area.title }}</td>
              <td v-for="item in 5" class="whitespace-nowrap px-2 text-center text-gray-500">
                <input :id="index" @input="form.key_training[index].stat = item" type="radio" :checked="area.stat == item" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-5" v-for="rp,indexRp in form.key_RP" :key="rp.id">
        <div class="rounded-t-md py-1">
          <h3 class="text-base font-semibold leading-6 text-gray-900">{{ `"${rp.topic}"` }}</h3>
          <p class="max-w-4xl text-sm text-gray-500">{{ `By: ${rp.full_name}` }}</p>
        </div>
        <table class="min-w-full divide-y divide-gray-300">
          <thead>
            <tr class="divide-x divide-gray-200">
              <th scope="col" class="py-3.5 pl-4 pr-4 text-left text-sm font-semibold text-gray-900 sm:pl-0">Key Areas: Resource Person</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Poor</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Fair</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Satisfactory</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Very Satisfactory</th>
              <th scope="col" class="px-2 py-3.5 text-center text-sm font-semibold text-gray-900">Excellent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="area,indexKey in rp.key_rp" :key="area.title" :class="[indexKey % 2 === 0 ? 'bg-gray-50' : '', 'divide-x divide-gray-200']">
              <td class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-2">{{ area.title }}</td>
              <td v-for="item in 5" :key="`${rp.id}-${area.title}-${item}`" class="whitespace-nowrap px-2 text-center text-gray-500">
                <!-- <input v-model="area.stat" type="radio" :value="item" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" /> -->
                <button type="button" @click="updateRpArea(indexRp, indexKey, item)">
                  <StarIcon :class="['h-6 w-6', area.stat == item ? 'text-yellow-500' : 'text-gray-200']"/>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-10">
        <div class="mt-3" v-for="item, index in form.key_learning" :key="item.title">
          <label :for="`${item.id}-key-learning`" class="block text-sm font-medium leading-6 text-gray-900">{{ `${index+1}.) ${item.title}` }}</label>
          <div class="mt-2">
            <textarea :id="`${item.id}-key-learning`" :name="`${item.id}-key-learning`" v-model="item.answer" rows="2" class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
          </div>
          <!-- <p class="mt-2 text-sm text-gray-500">Write a few sentences about yourself.</p> -->
        </div>
      </div>
      
      <div class="flex justify-end mt-5">
        <button @click="toggleAlert = true" type="button" class="tracking-wider inline-flex uppercase rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          Submit Evaluation
        </button>
      </div>
    </div>
    <!-- end -->
    </BreezeAuthenticatedLayout>
</template>
