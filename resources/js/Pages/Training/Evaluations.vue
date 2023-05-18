<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Modal from '@/Components/Modal.vue';
import QRCodeVue3 from "qrcode-vue3";
import { QrCodeIcon, EyeIcon, DocumentArrowDownIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import moment from 'moment'
import { StarIcon, ChevronDownIcon  } from '@heroicons/vue/20/solid';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
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
                sex: '0',
                training_id: null,
                office_rep: 1,
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
            this.form.sex = '0';
            this.form.office_rep = this.officeRep.length ? 1 : 1;
        },
        submitForm() {
            this.form.post(route("training.evaluation.post"), {
                preserveScroll: true,
                onSuccess: () => { this.resetForm(); this.toggleAlert = false; }
            });
        },
        previewReport(type){
          const { id } = this.training
          const url = type ? route('training.preview-report', {id}) : route('training.export-report', {id})
          window.open(url, "_blank")
        }
    },
    components: { StarIcon, DocumentArrowDownIcon, DocumentTextIcon }
}
</script>

<template>
    <Head title="Evaluations" />

    <BreezeAuthenticatedLayout>
      <Modal v-model="showQrCode">
        <div class="py-5">
          <QRCodeVue3 :value="route('public.training.evaluation-response', {id: training.id})"
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
        
        <Menu as="div" class="relative inline-block text-left">
          <div>
            <MenuButton class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
              Options
              <ChevronDownIcon class="-mr-1 h-5 w-5 text-gray-400" aria-hidden="true" />
            </MenuButton>
          </div>

          <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
              <div class="py-1 divide-y divide-gray-300">
                <MenuItem v-slot="{ active }">
                  <button type="button" @click="showQrCode = true" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'px-4 py-2 text-sm inline-flex w-full items-center']">
                  <QrCodeIcon class="h-6 w-6 mr-3" />
                  QR Code</button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <Link :href="route('training.evaluation-response', {id: training.id})" type="button" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'px-4 py-2 text-sm inline-flex w-full items-center']">
                  <EyeIcon class="h-6 w-6 mr-3" />
                  Preview Response</Link>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button type="button" @click="previewReport(true)" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'px-4 py-2 text-sm inline-flex w-full items-center']">
                  <DocumentTextIcon class="h-6 w-6 mr-3" />
                  Preview Page</button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button type="button" @click="previewReport(false)" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'px-4 py-2 text-sm inline-flex w-full items-center']">
                  <DocumentArrowDownIcon class="h-6 w-6 mr-3" />
                  Download Excel</button>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
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
          <label for="Sex" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Sex</label>
          <select v-model="form.sex" id="Sex" name="Sex" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option :value="'0'">Prefer not to say</option>
            <option :value="'male'">Male</option>
            <option :value="'female'">Female</option>
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
