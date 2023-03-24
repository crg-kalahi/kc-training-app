<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import UserSelection from '@/Components/UserSelection.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
</script>

<script>
import moment from 'moment'
import { useForm } from '@inertiajs/inertia-vue3'
import KeyTrainingEdit from './EditComponents/KeyTrainingEdit.vue';
import ResourcePerson from './EditComponents/ResourcePerson.vue';
import { ArrowUpRightIcon } from '@heroicons/vue/24/outline';
export default {
    props: ["training", "users", "listTraining", "listLearning", "listKeyResourcePerson"],
    methods: {
        moment,
    },
    data() {
        return {
            formEvent: useForm({
                id: null,
                title: "",
                venue: "",
                date_from: "",
                date_to: ""
            }),
            formFacilitators: useForm({
                id: null,
                id_numbers: []
            })
        };
    },
    methods: {
        removeFacilitator(index) {
            const { id_numbers } = this.formFacilitators;
            this.formFacilitators.id_numbers = id_numbers.filter((x, index_) => index_ != index);
        },
        updateGeneralInformation() {
            const { id } = this.formEvent;
            this.formEvent.put(route("training.update", id));
        },
        updateFacilitatorInformation() {
            this.formFacilitators.put(route("training.facilitators"), {
                onSuccess: () => { this.resetForm(2); },
                preserveScroll: true
            });
        },
        resetForm(section) {
            if (section == 1) {
                const { id, title, venue, date_from, date_to } = this.training;
                this.formEvent.clearErrors();
                this.formEvent = Object.assign({}, this.formEvent, {
                    id,
                    title,
                    venue,
                    date_from,
                    date_to
                });
            }
            if (section == 2) {
                const { id, facilitators_ } = this.training;
                this.formFacilitators.clearErrors();
                this.formFacilitators = Object.assign({}, this.formFacilitators, {
                    id_numbers: facilitators_.map(x => x.user_id),
                    id
                });
            }
        }
    },
    mounted() {
        this.resetForm(1);
        this.resetForm(2);
    },
    components: { KeyTrainingEdit, ResourcePerson, ResourcePerson, ArrowUpRightIcon }
}
</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout class="pb-32">
        <!-- Page title & actions -->
    <div class="border-b border-gray-200 px-4 py-4 md:px-6">
        <div class="flex justify-between items-center">
            <!-- <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1> -->
            <div class="text-xs md:text-sm font-medium breadcrumbs">
                <ul>
                    <li><Link :href="route('training.index')">Training</Link></li>
                    <li>{{ training.id }}<span class="hidden lg:block ml-3 badge badge-info badge-outline">{{ `Date Encoded: ${moment(training.created_at, 'YYYY-MM-DD').format('MMMM DD, YYYY')}` }}</span></li>
                    <li>Edit Event</li>
                </ul>
            </div>
            <div>
              <div class="flex gap-3">
                <Link :href="route('training.participants', {id: training.id})" class="inline-flex items-center justify-center rounded-md bg-teal-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-500">Participants<ArrowUpRightIcon class="ml-1 h-4 w-4"/></Link>
                <Link :href="route('training.evaluations', {id: training.id})" class="inline-flex items-center justify-center rounded-md bg-teal-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-500">Evaluation<ArrowUpRightIcon class="ml-1 h-4 w-4"/></Link>
              </div>
            </div>
        </div>
    </div>
<!-- 
    <div class="toast">
        <p class="truncate text-sm text-gray-500">Encoded By</p>
        <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm hover:border-gray-400">
            <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full" :src="training.encoded_by.avatar" alt="" />
            </div>
            <div class="min-w-0 flex-1">
                <a href="#" class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true" />
                <p class="text-sm font-medium text-gray-900">{{ training.encoded_by.full_name }}</p>
                <p class="truncate text-sm text-gray-500">{{ training.encoded_by.id_number }}</p>
                </a>
            </div>
        </div>
    </div> -->

    <div class="mt-5 px-3 lg:px-6 lg:py-3">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
          <h3 class="text-base font-semibold leading-6 text-gray-900">General Information</h3>
          <p class="mt-1 text-sm text-gray-600">This information will be displayed publicly to other users.</p>
        </div>
      </div>
      <div class="mt-5 md:col-span-2 md:mt-0">
        <form @submit.prevent="updateGeneralInformation">
          <div class="shadow sm:overflow-hidden sm:rounded-md">
            <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
							<div class="grid grid-cols-6 gap-6">
								<div class="col-span-3 lg:col-span-2">
										<label for="event-date-from" class="block text-sm font-medium leading-6 text-gray-900">Date Start</label>
										<div class="mt-2">
										<input v-model="formEvent.date_from" @change="formEvent.date_to = ''" type="date" name="event-date-from" id="event-date-from" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
										<p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.date_from">{{ formEvent.errors.date_from }}</p>
								</div>
								<div class="col-span-3 lg:col-span-2">
										<label for="event-date-to" class="block text-sm font-medium leading-6 text-gray-900">Date End</label>
										<div class="mt-2">
										<input v-model="formEvent.date_to" :readonly="formEvent.date_from ? false : true" :min="formEvent.date_from" type="date" name="event-date-to" id="event-date-to" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
										<p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.date_to">{{ formEvent.errors.date_to }}</p>
								</div>
							</div>
              <div class="grid grid-cols-3 gap-6">
                <div class="col-span-3">
                    <label for="event-title" class="block text-sm font-medium leading-6 text-gray-900">Title of the Event</label>
                    <div class="mt-2">
                    <input v-model="formEvent.title" type="text" name="event-title" id="event-title" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                    <p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.title">{{ formEvent.errors.title }}</p>
                </div>
              </div>

              <div>
                <label for="event-venue" class="block text-sm font-medium leading-6 text-gray-900">Venue / Area</label>
                <div class="mt-2">
                    <textarea v-model="formEvent.venue" id="event-venue" name="event-venue" rows="3" class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
                </div>
                <p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.venue">{{ formEvent.errors.venue }}</p>
              </div>
            </div>
            <div class="bg-gray-50 flex gap-x-3 justify-end px-4 py-3 sm:px-6">
                <button type="button" @click="resetForm(1)" class="inline-flex justify-center rounded-md border border-yellow-600 py-2 px-3 text-sm font-semibold text-yellow-600 shadow-sm hover:bg-yellow-500 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-500">Reset Form</button>
                <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Update</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
      <div class="border-t border-gray-200" />
    </div>
  </div>

  <div class="mt-10 sm:mt-0 px-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
          <h3 class="text-base font-semibold leading-6 text-gray-900">Event Facilitator</h3>
          <p class="mt-1 text-sm text-gray-600">Only users who are registered in the application are visible for selection.</p>
        </div>
      </div>
      <div class="mt-5 md:col-span-2 md:mt-0">
        <form @submit.prevent="updateFacilitatorInformation">
          <div class="overflow-hidden shadow sm:rounded-md">
            <div class="bg-white min-h-16 px-6 p-5">
              <div v-for="people, index in formFacilitators.id_numbers" :key="people" class="mb-2 grid grid-cols-6 gap-3">
                  <div class="col-span-5">
                    <label for="first-name" class="block text-sm font-medium leading-6 text-gray-900">Faciltator # {{ index + 1 }}</label>
                    <UserSelection class="mt-1" v-model:id_number="formFacilitators.id_numbers[index]" :items="users"/>
                  </div>
                  <div class="col-span-1 flex items-end justify-start">
                    <button type="button" @click="removeFacilitator(index)" v-if="formFacilitators.id_numbers.length > 1" class="inline-flex justify-center rounded-md bg-red-600 lg:py-2 lg:px-3 py-2 px-1 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500">Remove</button>
                  </div>
              </div>
              <button type="button" :disabled="users.length == formFacilitators.id_numbers.length" @click="formFacilitators.id_numbers.push(null)" :class="[users.length == formFacilitators.id_numbers.length ? 'bg-gray-200' : 'bg-indigo-400', 'text-sm mt-2 px-2 py-1 rounded-md text-white border']">Add Row</button>
            </div>
            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
              <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
      <div class="border-t border-gray-200" />
    </div>
  </div>

  <div class="mt-10 sm:mt-0 px-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-3">
        <div class="px-4 sm:px-0">
          <h3 class="text-base font-semibold leading-6 text-gray-900">Resource Person</h3>
          <p class="mt-1 text-sm text-gray-600">This will be the list of presenter in the event</p>
        </div>
      </div>
      <div class="mt-5 md:col-span-3 md:mt-0">
        <ResourcePerson :training="training"/>
        <!-- <KeyTrainingEdit :listTraining="listTraining" :listLearning="listLearning" :listKeyResourcePerson="listKeyResourcePerson" :training="training"/> -->
      </div>
    </div>
  </div>

  <div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
      <div class="border-t border-gray-200" />
    </div>
  </div>

  <div class="mt-10 sm:mt-0 px-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-3">
        <div class="px-4 sm:px-0">
          <h3 class="text-base font-semibold leading-6 text-gray-900">Evaluation Form Defaults</h3>
          <p class="mt-1 text-sm text-gray-600">Items with checked label will be used in the evaluation form</p>
        </div>
      </div>
      <div class="mt-5 md:col-span-3 md:mt-0">
        <KeyTrainingEdit :listTraining="listTraining" :listLearning="listLearning" :listKeyResourcePerson="listKeyResourcePerson" :training="training"/>
      </div>
    </div>
  </div>

  </BreezeAuthenticatedLayout>
</template>
