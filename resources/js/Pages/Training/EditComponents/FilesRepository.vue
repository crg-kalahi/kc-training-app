<template>
	<div>
		<div class="border rounded-md shadow-sm">
			<div class="max-h-[400px] overflow-y-auto">
				<ul role="list" class="divide-y divide-gray-200">
					<li v-for="item, index in items" :class="[index % 2 == 0 ? '' : 'bg-gray-50' ,'block']">
						<div :class="[indexToEdit != index ? 'flex items-center' : 'bg-green-50','px-4 py-4 sm:px-6']">
							<div v-if="indexToEdit != index" class="flex min-w-0 flex-1 items-center">
								<div class="flex-shrink-0">
									<DocumentIcon  :class="'text-teal-600 h-12 w-12 mr-1'"/>
								</div>
								<div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
									<div>
										<p class="truncate text-sm font-bold tracking-wide text-indigo-600">{{ item.name }} </p>
									
										<p class="mt-2 flex items-center text-sm text-gray-500">
											<span class="truncate">{{ item.description }}</span>
										</p>
										<p class="mt-2 flex items-center text-sm text-gray-500">
											<span class="truncate">{{ `${item.training_id || 'None'}` }}</span>
										</p>
									</div>
									<div class="hidden md:block">
										<div>
											<!-- Clickable download link -->
											<a :href="item.file_path" download class="flex items-center text-sm text-blue-600 hover:text-blue-800" :title="'Download ' + item.name">
												<ArrowDownIcon class="h-6 w-6 text-gray-600 hover:text-gray-800" />
												Download
											</a>
										</div>
									</div>
								</div>
							</div>
							<div v-else class="min-w-0 flex-1 items-center">
								<div class="md:flex md:gap-x-2">
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">ID</label>
										<div class="mt-1">
											<input disabled v-model="form.id" type="rp-last-name" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">Training ID</label>
										<div class="mt-1">
											<input disabled v-model="form.training_id" type="rp-last-name" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">File Name</label>
										<div class="mt-1">
											<input v-model="form.name" type="rp-last-name" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">Description</label>
										<div class="mt-1">
											<input v-model="form.description" type="rp-last-name" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">File</label>
										<div class="mt-1">
											<input @change="handleFileUpload" type="file" ref="fileInput" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
									</div>
									<div>
										<label for="rp-last-name" class="block text-xs font-medium leading-6 text-gray-900">File Type</label>
										<div class="mt-1">
											<input v-model="form.type" type="rp-last-name" name="rp-last-name" id="rp-last-name" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
										</div>
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
			</div>
			<div class="bg-gray-50 px-4 py-3 flex justify-end items-center sm:px-6">
				<button type="button" @click="toggleForm" class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
					Add Attachment
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
															Add Attachments</DialogTitle>
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
													<div class="sm:col-span-6 hidden">
														<label for="create-item-training_id"
															class="block text-sm font-medium leading-6 text-gray-900">Training ID
															<span class="text-red-600">*</span></label>
														<div class="mt-2">
														
															<input v-model="form.training_id" type="text"
																name="create-item-training_id" id="create-item-training_id"
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.training_id">{{
															form.errors.training_id }}</p>
													</div>
													<div class="sm:col-span-6">
														<label for="create-item-name"
															class="block text-sm font-medium leading-6 text-gray-900">File Name
															<span class="text-red-600">*</span></label>
														<div class="mt-2">
															<input v-model="form.name" type="text"
																name="create-item-name" id="create-item-name"
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.name">{{
															form.errors.name }}</p>
													</div>
													<div class="sm:col-span-6">
														<label for="create-item-description"
															class="block text-sm font-medium leading-6 text-gray-900">Description</label>
														<div class="mt-2">
															<input v-model="form.description" type="text"
																name="create-item-description" id="create-item-description"
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.description">{{
															form.errors.description }}</p>
													</div>
													<div class="sm:col-span-6">
														<label for="create-item-file"
															class="block text-sm font-medium leading-6 text-gray-900">Upload File</label>
															
														<div class="mt-2">
															<input @change="handleFileUpload" type="file"
																ref="fileInput"
																name="create-item-file" id="create-item-file"
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.file">{{
															form.errors.file }}</p>
													</div>
													<div class="sm:col-span-6">
														<label for="create-item-type"
															class="block text-sm font-medium leading-6 text-gray-900">File Type</label>
														<div class="mt-2">
															<input v-model="form.type" type="text"
																name="create-item-type" id="create-item-type"
																placeholder=""
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="form.errors.type">{{
															form.errors.type }}</p>
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

		<DangerAlertDialogVue v-model="showDangerAlert" title="Remove Item" :subtitle='`Are you sure you want to remove "${form.name} ${form.file_path}" file?`' @approved="removeItem" />
	</div>
</template>

<script setup>
import DangerAlertDialogVue from '@/Components/DangerAlertDialog.vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UserIcon, DocumentIcon, ArrowDownIcon   } from '@heroicons/vue/20/solid';
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
        "attachments",
		"training_id",
    ],
    computed: {
        items() {
			console.log('Attachments in computed:', this.attachments);
			return this.attachments;
		}

    },
    data() {
        return {
            indexToEdit: null,
            openForm: false,
            showDangerAlert: false,
            form: useForm({
                id: null,
                training_id: null,
                name: "",
                description: "",
                type: "",
				file: null,
            })
        };
    },
    methods: {
		handleFileUpload(e) {
			const file = e.target.files[0];
			if (file) {
				this.form.file = file;
			}
		},
        toggleForm() {
            this.resetForm();
            this.openForm = true;
        },
        resetForm() {
            this.form.reset("id","name", "description", "type");
			this.form.file = null;

			if (this.$refs.fileInput) {
				this.$refs.fileInput.value = null;
			}
        },
        submitform() {
            this.form.post(route("attachments.store-update"), {
                onSuccess: () => { this.resetForm(); this.indexToEdit = null; },
                preserveScroll: true,
				forceFormData: true
            });
        },
		editItem(item, index) {
			console.log('Editing item:', item); // Check the item data
			const { id, training_id, name, description, type, file_path } = item;
			this.indexToEdit = index;

			// Assign values to form object
			this.form.id = id;
			this.form.training_id = training_id;
			this.form.name = name;
			this.form.description = description;
			this.form.type = type;
			this.form.file_path = file_path;

			this.form.reset({ training_id, name, description, type, file_path });
		},


        removeItemInit(item) {
			console.log('remove item:', item);
            const { id, training_id, name, description, type, file_path } = item;

			
			this.form.id = id;
			this.form.training_id = training_id;
			this.form.name = name;
			this.form.description = description;
			this.form.type = type;
			this.form.file_path = file_path;

            this.showDangerAlert = true;
        },
        removeItem() {
            this.form.delete(route("attachments.desctroy"), {
                preserveScroll: true,
                onSuccess: () => { this.resetForm(); }
            });
            this.showDangerAlert = false;
        }
    },
    mounted() {
		console.log('Attachments:', this.attachments); 
		this.form.training_id = this.training_id;
        const { id } = this.attachments;
        this.form.attachment_id = id;
    },
    components: { UserIcon }
}
	</script>