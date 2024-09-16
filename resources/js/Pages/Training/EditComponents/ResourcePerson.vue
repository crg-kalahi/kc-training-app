<template>
	<div>
		<div class="overflow-hidden bg-white shadow sm:rounded-md">
			<ul role="list" class="divide-y divide-gray-200">
				<li v-for="item, index in items" :key="item.id"  :class="[index % 2 == 0 ? '' : 'bg-gray-50' ,'block']">
						<div :class="[indexToEdit != index ? 'flex items-center' : 'bg-green-50','px-4 py-4 sm:px-6']">
							<div v-if="indexToEdit != index" class="flex min-w-0 flex-1 items-center">
								<div class="flex-shrink-0">
									<!-- <img class="h-12 w-12 rounded-full" :src="item.avatar" alt="" /> -->
									<UserIcon :class="[item.is_female ? 'text-pink-600' : 'text-teal-600' ,'h-12 w-12 mr-1']"/>
								</div>
								<div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
									<div>
										<p class="truncate text-sm font-bold tracking-wide text-indigo-600">{{ item.full_name }} </p>
									
										<p class="mt-2 flex items-center text-sm text-gray-500">
											<!-- <UserIcon :class="[item.is_female ? 'text-pink-600' : 'text-teal-600' ,'h-5 w-5 mr-1']"/> -->
											<span class="truncate">{{ item.email }}</span>
										</p>
										<p class="mt-2 flex items-center text-sm text-gray-500">
											<!-- <UserIcon :class="[item.is_female ? 'text-pink-600' : 'text-teal-600' ,'h-5 w-5 mr-1']"/> -->
											<span class="truncate">{{ `${item.position || 'None'}, ${item.is_internal ? 'Internal RP' : 'External RP'}` }}</span>
										</p>
									</div>
									<div class="hidden md:block">
										<div>
											<p class="text-sm text-gray-900">
												{{ item.topic }}
											</p>
										</div>
									</div>
								</div>
							</div>
							<div v-else class="min-w-0 flex-1 items-center">
								<div class="md:flex md:gap-x-2">
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">Last Name</label>
										<div class="mt-1">
											<input v-model="form.lname" type="rp-last-name" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-first-name" class="block text-xs font-medium leading-6 text-gray-900">First Name</label>
										<div class="mt-1">
											<input v-model="form.fname" type="rp-first-name" name="rp-first-name" id="rp-first-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-middle-name" class="block text-xs font-medium leading-6 text-gray-900">Middle Name</label>
										<div class="mt-1">
											<input v-model="form.mname" type="rp-middle-name" name="rp-middle-name" id="rp-middle-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div class="w-full md:w-32">
										<label for="rp-ext-name" class="block text-xs font-medium leading-6 text-gray-900">Extension Name</label>
										<div class="mt-1">
											<input v-model="form.ext_name" type="rp-ext-name" name="rp-ext-name" id="rp-ext-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-email" class="block text-xs font-medium leading-6 text-gray-900">Email</label>
										<div class="mt-1">
											<input v-model="form.email" type="rp-email" name="rp-email" id="rp-email" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div class="w-full md:w-32">
										<label for="rp-position" class="block text-xs font-medium leading-6 text-gray-900">Position Title</label>
										<div class="mt-1">
											<input v-model="form.position" type="rp-position" name="rp-position" id="rp-position" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="gender" class="block text-xs font-medium leading-6 text-gray-900">Gender</label>
										<select v-model="form.is_female" id="gender" name="gender" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
											<option :value="0">Male</option>
											<option :value="1">Female</option>
										</select>
									</div>
									<div>
										<label for="location" class="block text-xs font-medium leading-6 text-gray-900">Type</label>
										<select v-model="form.is_internal" id="location" name="location" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
											<option :value="1">Internal</option>
											<option :value="0">External</option>
										</select>
									</div>
								</div>
								<div class="mt-2">
									<label for="about" class="block text-sm font-medium leading-6 text-gray-900">Topic</label>
									<div class="mt-1">
										<textarea v-model="form.topic" id="about" name="about" rows="3" class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
									</div>
								</div>
							</div>
							<div v-if="indexToEdit != index">
								<button type="button" @click="removeItemInit(item)" class="text-red-700 text-xs uppercase py-2 px-1 hover:bg-red-100 rounded-md">remove</button>
								<button @click="editItem(item, index)" type="button" class="text-yellow-700 text-xs uppercase py-2 px-1 hover:bg-yellow-100 rounded-md">edit</button>
							</div>
							<div v-else class="mt-5">
								<button @click="indexToEdit = null" type="button" class="text-gray-700 text-xs uppercase py-2 px-1 hover:bg-red-100 rounded-md">Cancel</button>
								<button type="button" @click="submitform" class="text-purple-700 text-xs uppercase py-2 px-2 hover:bg-purple-100 rounded-md">Save</button>
							</div>
						</div>
				</li>
			</ul>
			<div class="bg-gray-50 px-4 py-3 flex justify-end items-center sm:px-6">
				<button type="button" @click="toggleForm" class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
					Add Resource Person
				</button>
			</div>
		</div>

		<TransitionRoot as="template" :show="openForm">
			<Dialog as="div" class="relative z-10" @close="openForm = false">
				<div class="fixed inset-0" />

				<div class="fixed inset-0 overflow-hidden">
					<div class="absolute inset-0 overflow-hidden">
						<div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
							<TransitionChild as="template"
								enter="transform transition ease-in-out duration-500 sm:duration-700"
								enter-from="translate-x-full" enter-to="translate-x-0"
								leave="transform transition ease-in-out duration-500 sm:duration-700"
								leave-from="translate-x-0" leave-to="translate-x-full">
								<DialogPanel class="pointer-events-auto w-screen max-w-md">
									<form @submit.prevent="submitform" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
										<div class="flex min-h-0 flex-1 flex-col overflow-y-scroll">
											<div class="">
												<div class="bg-purple-700 py-6 px-4 sm:px-6">
													<div class="flex items-center justify-between">
														<DialogTitle class="text-base font-semibold leading-6 text-white">
															Add Resource Person</DialogTitle>
														<div class="ml-3 flex h-7 items-center">
															<button type="button" @click="openForm = false"
																class="rounded-md bg-purple-700 text-purple-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
																<span class="sr-only">Close panel</span>
																<XMarkIcon class="h-6 w-6" aria-hidden="true" />
															</button>
														</div>
													</div>
													<div class="mt-1">
														<p class="text-sm text-purple-300">Label with <span class="text-red-300 font-bold">*</span> is required.</p>
													</div>
												</div>
											</div>
											<div class="relative mt-3 flex-1 px-4 sm:px-6">
												<div class="grid grid-cols-1 gap-y-1 gap-x-4 sm:grid-cols-6">
													<div class="sm:col-span-6">
														<label for="create-item-topic"
															class="block text-sm font-medium leading-6 text-gray-900">Topic <span class="text-red-600">*</span></label>
														<div class="mt-2">
															<textarea v-model="form.topic" id="create-item-topic"
																name="create-item-topic" rows="3"
																class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.topic">{{
															form.errors.topic }}</p>
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

													<div class="sm:col-span-6">
														<label for="create-item-email"
															class="block text-sm font-medium leading-6 text-gray-900">Email
															<span class="text-red-600">*</span></label>
														<div class="mt-2">
															<input v-model="form.email" type="text"
																name="create-item-email" id="create-item-email"
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.email">{{
															form.errors.email }}</p>
													</div>

													<div class="sm:col-span-6 relative flex items-start pt-4">
														<fieldset class="pb-5">
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

													<div class="sm:col-span-6 relative flex items-start pt-4">
														<div class="flex h-6 items-center">
															<input id="create-item-is-internal" v-model="form.is_internal"
																name="create-item-is-internal" type="checkbox"
																class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
														</div>
														<div class="ml-3 text-sm leading-6">
															<label for="create-item-is-internal"
																class="font-medium text-gray-900">Is Internal?</label>
															<p class="text-gray-500">Either the Resource Person was internal or external.</p>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="flex flex-shrink-0 justify-between px-4 py-4">
											<button type="button"
												class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-yellow-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:ring-gray-400"
												@click="resetForm">Reset Form</button>
											<div>
												<button type="button"
													class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:ring-gray-400"
													@click="openForm = false">Close</button>
												<button type="submit"
													:class="[form.processing ? 'bg-gray-500' : 'bg-purple-600 hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-500' ,'ml-4 inline-flex justify-center rounded-md  py-2 px-3 text-sm font-semibold text-white shadow-sm']">
													{{ form.processing ? 'Processing...' : 'Save' }}
												</button>
											</div>
										</div>
									</form>
								</DialogPanel>
							</TransitionChild>
						</div>
					</div>
				</div>
			</Dialog>
		</TransitionRoot>

		<DangerAlertDialogVue v-model="showDangerAlert" title="Remove Item" :subtitle='`Are you sure you want to remove "${form.fname} ${form.lname}" as resource person?`' @approved="removeItem" />
	</div>
</template>

<script setup>
import DangerAlertDialogVue from '@/Components/DangerAlertDialog.vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UserIcon } from '@heroicons/vue/20/solid';
import {
	PencilIcon,
	TrashIcon,
	ExclamationTriangleIcon,
	XMarkIcon,
	CheckIcon,
	PlusIcon
} from '@heroicons/vue/24/outline'
	const applications = [
		{
			applicant: {
				name: 'Ricardo Cooper',
				email: 'ricardo.cooper@example.com',
				imageUrl:
					'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
			},
			date: '2020-01-07',
			dateFull: 'January 7, 2020',
			stage: 'Completed phone screening',
			href: '#',
		},
		{
			applicant: {
				name: 'Kristen Ramos',
				email: 'kristen.ramos@example.com',
				imageUrl:
					'https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
			},
			date: '2020-01-07',
			dateFull: 'January 7, 2020',
			stage: 'Completed phone screening',
			href: '#',
		},
	]
	</script>
	<script>
	import { useForm } from '@inertiajs/inertia-vue3'
		export default {
    props: [
        "training"
    ],
    computed: {
        items() {
            const { resource_persons } = this.training;
            return resource_persons;
        }
    },
    data() {
        return {
            indexToEdit: null,
            openForm: false,
            showDangerAlert: false,
            form: useForm({
                id: null,
                lname: "",
                fname: "",
                mname: "",
                ext_name: "",
				email: "",
                topic: "",
                training_id: "",
                is_internal: true,
                is_female: false,
                position: ""
            })
        };
    },
    methods: {
        toggleForm() {
            this.resetForm();
            this.openForm = true;
        },
        resetForm() {
            this.form.reset("id", "lname", "fname", "topic", "ext_name","email", "is_internal", "mname", "position", "is_female");
        },
        submitform() {
            this.form.put(route("training.resource-person"), {
                onSuccess: () => { this.resetForm(); this.indexToEdit = null; },
                preserveScroll: true
            });
        },
        editItem(item, index) {
            const { id, lname, fname, topic, ext_name,email, is_internal, mname, position, is_female } = item;
            this.indexToEdit = index;
            this.form = Object.assign({}, this.form, {
                id,
                lname,
                fname,
                topic,
                ext_name,
				email,
                is_internal,
                mname,
                position,
                is_female
            });
        },
        removeItemInit(item) {
            const { id, lname, fname, topic, ext_name,email, is_internal, mname } = item;
            this.form = Object.assign({}, this.form, {
                id,
                lname,
                fname,
				email,
                topic,
                ext_name,
                is_internal,
                mname
            });
            this.showDangerAlert = true;
        },
        removeItem() {
            this.form.delete(route("training.resource-person.destroy"), {
                preserveScroll: true,
                onSuccess: () => { this.resetForm(); }
            });
            this.showDangerAlert = false;
        }
    },
    mounted() {
        const { id } = this.training;
        this.form.training_id = id;
    },
    components: { UserIcon }
}
	</script>