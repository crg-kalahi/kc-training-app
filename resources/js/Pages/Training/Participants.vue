<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import RightPanel from '@/Components/RightPanel.vue';
import Modal from '@/Components/Modal.vue';
import DangerAlertDialog from '@/Components/DangerAlertDialog.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import moment from 'moment'
import { CheckIcon, ChevronUpDownIcon, PlusIcon } from '@heroicons/vue/20/solid'
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxLabel,
  ComboboxOption,
  ComboboxOptions,
} from '@headlessui/vue'
</script>
<script>
  export default {
    props: ['training', 'people'],
    computed:{
      stats(){
        const people = this.people
        const male = people.filter(x => !x.is_female).length
        const total = people.length
        const test = people.map(function(x){
          const tmp = x.post_test - x.pre_test
          return ( tmp <= 0 ? 0 : tmp / x.post_test ) * 100
        })
        const KG = people.length ? `${(test.reduce((partialSum, a) => partialSum + a, 0) / total).toFixed(2)} %` : 'No Participant'
        return [
          { name: 'Male', stat: male },
          { name: 'Female', stat: Math.abs(male - total) },
          { name: 'Overall Total', stat: total },
          { name: 'Knowledge Gained', stat: KG},
        ]
      },
      filteredQuery(){
        return this.queryPeople
      },
      filteredPeople(){
        const q = this.searchParticipant.toLowerCase()
        return this.people.filter(x => x.full_name.toLowerCase().includes(q))
      },
      trainingDate(){
        const { date_from, date_to } = this.training
        return `${moment(date_from, 'YYYY-MM-DD').format('MMM DD, YYYY')} - ${moment(date_to, 'YYYY-MM-DD').format('MMM DD, YYYY')}`
      },
    },
    data(){
      return{
        openForm: false,
        openEditForm: false,
        toggleAlert: false,
        selectedInternalPerson: null,
        searchParticipant: "",
        form: useForm({
            id: null,
            lname: "",
            fname: "",
            mname: "",
            ext_name: "",
            email: "",
            training_id: "",
            is_internal: true,
            is_female: false,
            position: "",
            pre_test: 0,
            post_test: 0
        }),
        querySearch: "",
        queryPeople: []
      }
    },
    watch:{
      querySearch(){
        this.getParticipantList()
      }
    },
    mounted(){
      this.form.training_id = this.training.id
      this.getParticipantList()
    },
    methods:{
      knowGained(post, pre){
        const tmp = post - pre
        return `${(( tmp <= 0 ? 0 : tmp / post ) * 100).toFixed(2)} %`
      },
      async getParticipantList(){
        try {
          const q = this.querySearch
          const res = await axios.get(route('training.participant.index', {q}))
          this.queryPeople = res.data
        } catch (error) {
          // location.reload(); 
          console.log(error)
        }
      },
      toggleForm(){
        this.formReset()
        this.openForm = true
      },
      formReset(){
        this.form.reset('id','lname', 'fname', 'mname', 'ext_name', 'email', 'is_internal', 'is_female', 'position', 'pre_test', 'post_test')
      },
      fillTheFields(){
        const { lname, fname, mname, ext_name, email, is_internal, is_female, position } = this.selectedInternalPerson
        this.form = Object.assign({}, this.form, {
          lname, fname, mname, ext_name, email, is_internal, is_female, position
        })
        this.selectedInternalPerson = null
      },
      submitForm(){
        this.form.post(route('training.participant.store'),{
          preserveScroll: true,
          onSuccess: () => this.formReset()
        })
      },
      submitEditForm(){
        const { id } = this.form
        this.form.put(route('training.participant.update', {id}), {
          preserveScroll: true
        })
      },
      removeItem(){
        const { id } = this.form
        this.form.delete(route('training.participant.destroy', {id}), {
          preserveScroll: true,
          onSuccess: () => { this.toggleAlert = false }
        })
      },
      configItem(person, type){
        const { id, lname, fname, mname, ext_name, email, is_internal, is_female, position, pre_test, post_test } = person
        this.form = Object.assign({}, this.form, {
          id, lname, fname, mname, ext_name, email, is_internal: is_internal ? true : false, is_female, position, pre_test, post_test
        })
        if(type == 'edit'){
          this.openEditForm = true
        }else if(type == 'delete'){
          this.toggleAlert = true
        }else if(type == 'changeTest'){
          this.submitEditForm()
        }
      }
    }
  }
</script>

<template>
    <Head title="Participants" />

    <BreezeAuthenticatedLayout>
        <!-- Page title & actions -->
    <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div class="min-w-0 flex-1">
            <!-- <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1> -->
            <div class="text-xs md:text-sm font-medium breadcrumbs">
                <ul>
                    <li><Link :href="route('training.index')">Training</Link></li>
                    <li><Link :href="route('training.edit', {id: training.id})">{{ training.id }}</Link></li>
                    <li><span class="text-teal-500 uppercase tracking-wider font-bold px-3 py-2 rounded-md">Participants</span></li>
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
        <button v-if="people.length" @click="toggleForm" type="button" class="block rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Participant</button>
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
    <div class="mt-5 px-4 sm:px-6 lg:px-8">
      <div v-if="people.length">
        <input v-model="searchParticipant" type="text"
          name="search-item-participant" id="search-item-participant" placeholder="Search . . ."
          class="block mt-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
        <div class="mt-5 flow-root">
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gender</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Position</th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Pre-Test</th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Post-Test</th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">K.G.</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                      <span class="sr-only">Edit</span>
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                      <span class="sr-only">Delete</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white">
                  <tr v-for="(person, personIdx) in filteredPeople" :key="person.email" :class="personIdx % 2 === 0 ? undefined : 'bg-gray-50'">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">{{ person.full_name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ person.email || '' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ person.is_female ? 'Female' : 'Male' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ person.position }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-right text-sm text-gray-500">
                      <input type="number" :min="0" class="border-none w-14 px-0 py-0" @change="configItem(person, 'changeTest')" v-model="person.pre_test"/>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-right text-sm text-gray-500">
                      <input type="number" :min="0" class="border-none w-14 px-0 py-0" @change="configItem(person, 'changeTest')" v-model="person.post_test"/>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-right text-sm text-gray-500">{{ knowGained(person.post_test, person.pre_test) }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                      <button @click="configItem(person, 'edit')" type="button" class="text-indigo-600 hover:text-indigo-900"
                        >Edit<span class="sr-only">, {{ person.full_name }}</span></button>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                      <button @click="configItem(person, 'delete')" type="button" class="text-red-600 hover:text-red-900"
                        >Delete<span class="sr-only">, {{ person.full_name }}</span></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <div class="text-center mt-8">
          <h3 class="mt-2 text-md font-semibold text-gray-900">No Participant</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding a new participant.</p>
          <div class="mt-6">
            <button @click="toggleForm" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
              Add Participant
            </button>
          </div>
        </div>
      </div>
    </div>
    <RightPanel v-model="openForm" title="New Participant" subtitle="" @submit="submitForm">
      <div class="">
        <Combobox as="div" v-model="selectedInternalPerson">
          <!-- <ComboboxLabel class="block text-sm font-medium leading-6 text-gray-900">Search</ComboboxLabel> -->
          <div class="relative">
            <ComboboxInput placeholder="Search Participants" class="w-full rounded-t-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" @change="querySearch = $event.target.value" :display-value="(person) => person ? `${person.is_female ? '(F)' : '(M)'} ${person.full_name} - ${person.is_internal ? 'Internal' : 'External'}` : ''" />
            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
              <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </ComboboxButton>
            <ComboboxOptions v-if="filteredQuery.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
              <ComboboxOption v-for="person in filteredQuery" :key="person.full_name" :value="person" as="template" v-slot="{ active, selected }">
                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-100 text-white' : 'text-gray-900']">
                  <div class="flex">
                    <span :class="[person.is_female ? 'text-purple-500' : 'text-green-500', 'mr-2']">
                      {{ person.is_female ? '(F)' : '(M)' }}
                    </span>
                    <span :class="['truncate', selected && 'font-semibold', active ? 'text-indigo-500' : 'text-gray-500']">
                      {{ person.full_name }}
                    </span>
                    <span :class="['ml-2 text-gray-500']">
                      {{ person.is_internal ? 'Internal' : 'External' }}
                    </span>
                  </div>
                  <span v-if="selected" :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                  </span>
                </li>
              </ComboboxOption>
            </ComboboxOptions>
          </div>
        </Combobox>
        <button @click="fillTheFields" :disabled="selectedInternalPerson ? false : true" type="button" :class="['w-full inline-flex justify-center rounded-b-md py-1 px-3 text-sm font-semibold shadow-sm', selectedInternalPerson ? 'bg-indigo-600 hover:bg-indigo-500 text-white cursor-pointer' : 'text-gray-200']">
          Fill Inputs
        </button>
      </div>
      <div aria-hidden="true">
        <div class="py-5">
          <div class="border-t border-gray-200" />
        </div>
      </div>
      <form>
        <div class="grid grid-cols-1 gap-y-1 gap-x-4 sm:grid-cols-6 pb-5">
          <div class="sm:col-span-6 relative flex items-start pb-4">
            <div class="flex h-6 items-center">
              <input id="create-item-is-internal" v-model="form.is_internal"
                name="create-item-is-internal" type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
            </div>
            <div class="ml-3 text-sm leading-6">
              <label for="create-item-is-internal"
                class="font-medium text-gray-900">Internal Participant?</label>
              <!-- <p class="text-gray-500">Either the Resource Person was internal or external.</p> -->
            </div>
          </div>
          <div class="sm:col-span-6">
            <label for="create-item-lname"
              class="block text-sm font-medium leading-6 text-gray-900">Last Name
              <span class="text-red-600">*</span></label>
            <div class="mt-2">
              <input v-model="form.lname" type="text"
                name="create-item-lname" id="create-item-lname"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.lname">{{
              form.errors.lname }}</p>
          </div>
          <div class="sm:col-span-6">
            <label for="create-item-fname"
              class="block text-sm font-medium leading-6 text-gray-900">First Name
              <span class="text-red-600">*</span></label>
            <div class="mt-2">
              <input v-model="form.fname" type="text"
                name="create-item-fname" id="create-item-fname"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.fname">{{
              form.errors.fname }}</p>
          </div>
          <div class="sm:col-span-6">
            <label for="create-item-mname"
              class="block text-sm font-medium leading-6 text-gray-900">Middle Name</label>
            <div class="mt-2">
              <input v-model="form.mname" type="text"
                name="create-item-mname" id="create-item-mname"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.mname">{{
              form.errors.mname }}</p>
          </div>
          <div class="sm:col-span-6">
            <label for="create-item-ext_name"
              class="block text-sm font-medium leading-6 text-gray-900">Extension Name</label>
            <div class="mt-2">
              <input v-model="form.ext_name" type="text"
                name="create-item-ext_name" id="create-item-ext_name"
                placeholder="Jr., Sr., etc..."
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.ext_name">{{
              form.errors.ext_name }}</p>
          </div>
          <div class="sm:col-span-6 relative flex items-start pt-4">
            <fieldset class="pb-3">
              <legend class="sr-only">Gender</legend>
              <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                <div class="flex items-center">
                  <input name="notification-method" type="radio" @input="form.is_female = false" :checked="!form.is_female" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                  <label class="ml-3 block text-sm font-medium leading-6 text-gray-900">Male</label>
                </div>
                <div class="flex items-center">
                  <input name="notification-method" type="radio" @input="form.is_female = true" :checked="form.is_female" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                  <label class="ml-3 block text-sm font-medium leading-6 text-gray-900">Female</label>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="sm:col-span-6">
            <label for="create-item-ext_name"
              class="block text-sm font-medium leading-6 text-gray-900">Position</label>
            <div class="mt-2">
              <input v-model="form.position" type="text"
                name="create-item-position" id="create-item-position"
                placeholder="PDO III, PDO II, CDO IV, ETC..."
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.position">{{
              form.errors.position }}</p>
          </div>
          <div class="sm:col-span-6 pt-2">
            <label for="create-item-email"
              class="block text-sm font-medium leading-6 text-gray-900">Email</label>
            <div class="mt-2">
              <input v-model="form.email" type="email"
                name="create-item-email" id="create-item-email"
                placeholder="Valid & Active email"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.email">{{
              form.errors.email }}</p>
          </div>
        </div>
      </form>
    </RightPanel>
    
    <Modal v-model="openEditForm">
      <div class="grid grid-cols-1 gap-y-1 gap-x-4 sm:grid-cols-6 p-4">
        <div class="sm:col-span-6 relative flex items-start pb-4">
            <div class="flex h-6 items-center">
              <input id="create-item-is-internal" v-model="form.is_internal"
                name="create-item-is-internal" type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
            </div>
            <div class="ml-3 text-sm leading-6">
              <label for="create-item-is-internal"
                class="font-medium text-gray-900">Internal Participant?</label>
              <!-- <p class="text-gray-500">Either the Resource Person was internal or external.</p> -->
            </div>
          </div>
          <div class="sm:col-span-3">
            <label for="create-item-lname"
              class="block text-sm font-medium leading-6 text-gray-900">Last Name
              <span class="text-red-600">*</span></label>
            <div class="mt-2">
              <input v-model="form.lname" type="text"
                name="create-item-lname" id="create-item-lname"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.lname">{{
              form.errors.lname }}</p>
          </div>
          <div class="sm:col-span-3">
            <label for="create-item-fname"
              class="block text-sm font-medium leading-6 text-gray-900">First Name
              <span class="text-red-600">*</span></label>
            <div class="mt-2">
              <input v-model="form.fname" type="text"
                name="create-item-fname" id="create-item-fname"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.fname">{{
              form.errors.fname }}</p>
          </div>
          <div class="sm:col-span-3">
            <label for="create-item-mname"
              class="block text-sm font-medium leading-6 text-gray-900">Middle Name</label>
            <div class="mt-2">
              <input v-model="form.mname" type="text"
                name="create-item-mname" id="create-item-mname"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.mname">{{
              form.errors.mname }}</p>
          </div>
          <div class="sm:col-span-3">
            <label for="create-item-ext_name"
              class="block text-sm font-medium leading-6 text-gray-900">Extension Name</label>
            <div class="mt-2">
              <input v-model="form.ext_name" type="text"
                name="create-item-ext_name" id="create-item-ext_name"
                placeholder="Jr., Sr., etc..."
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.ext_name">{{
              form.errors.ext_name }}</p>
          </div>
          <div class="sm:col-span-6 relative flex items-start pt-4">
            <fieldset class="pb-3">
              <legend class="sr-only">Gender</legend>
              <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                <div class="flex items-center">
                  <input name="notification-method" type="radio" @input="form.is_female = false" :checked="!form.is_female" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                  <label class="ml-3 block text-sm font-medium leading-6 text-gray-900">Male</label>
                </div>
                <div class="flex items-center">
                  <input name="notification-method" type="radio" @input="form.is_female = true" :checked="form.is_female" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                  <label class="ml-3 block text-sm font-medium leading-6 text-gray-900">Female</label>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="sm:col-span-6">
            <label for="create-item-ext_name"
              class="block text-sm font-medium leading-6 text-gray-900">Position</label>
            <div class="mt-2">
              <input v-model="form.position" type="text"
                name="create-item-position" id="create-item-position"
                placeholder="PDO III, PDO II, CDO IV, ETC..."
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.position">{{
              form.errors.position }}</p>
          </div>
          <div class="sm:col-span-6 pt-2">
            <label for="create-item-email"
              class="block text-sm font-medium leading-6 text-gray-900">Email</label>
            <div class="mt-2">
              <input v-model="form.email" type="email"
                name="create-item-email" id="create-item-email"
                placeholder="Valid & Active email"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.email">{{
              form.errors.email }}</p>
          </div>
          <div class="sm:col-span-3">
            <label for="create-item-pre-test"
              class="block text-sm font-medium leading-6 text-gray-900">Pre Test</label>
            <div class="mt-2">
              <input v-model="form.pre_test" type="number"
                name="create-item-pre-test" id="create-item-pre-test"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.pre_test">{{
              form.errors.pre_test }}</p>
          </div>
          <div class="sm:col-span-3">
            <label for="create-item-post-test"
              class="block text-sm font-medium leading-6 text-gray-900">Post Test</label>
            <div class="mt-2">
              <input v-model="form.post_test" type="number"
                name="create-item-post-test" id="create-item-post-test"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
            <p class="mt-2 text-sm text-red-500" v-if="form.errors.post_test">{{
              form.errors.post_test }}</p>
          </div>
      </div>
      <div class="py-2 bg-gray-200 border-t border-gray-500 flex items-center justify-end gap-x-2 px-4">
        <button @click="openEditForm = false" type="button" class="border border-gray-500 inline-flex items-center py-1 px-3 rounded-md text-sm">CANCEL</button>
        <button @click="submitEditForm" type="button" class=" inline-flex items-center py-1 px-3 rounded-md text-sm bg-indigo-600 text-white">UPDATE</button>
      </div>
    </Modal>

    <DangerAlertDialog v-model="toggleAlert" @approved="removeItem" title="System Alert" :subtitle="`Proceed to remove '${form.lname}, ${form.fname}' as Participant?`"/>
    </BreezeAuthenticatedLayout>
</template>
