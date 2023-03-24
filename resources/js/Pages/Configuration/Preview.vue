<template>
	<Head title="Configuration" />
	<BreezeAuthenticatedLayout>
		<div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
			<div class="min-w-0 flex justify-between w-full">
				<div class="text-xs lg:text-lg font-medium breadcrumbs">
					<ul>
						<li>
							<Link :href="route('conf.index')">Configuration</Link>
						</li>
						<li>{{ title }}</li>
					</ul>
				</div>
				<div class="flex sm:mt-0 sm:ml-4">
					<button type="button" @click="openForm = true" v-if="items.length"
						class="order-0 inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 sm:order-1 sm:ml-3">Create</button>
				</div>
			</div>
		</div>

		<div class="text-center mt-5" v-if="!items.length">
			<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
			<path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
			</svg>
			<h3 class="mt-2 text-sm font-semibold text-gray-900">{{ `No ${title} item` }}</h3>
			<p class="mt-1 text-sm text-gray-500">Get started by creating a new item.</p>
			<div class="mt-6">
			<button @click="openForm = true" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
				<PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
				New Item
			</button>
			</div>
		</div>

		<div v-else>
			<div class="mx-6 mt-3 mb-5">
				<label for="search-title" class="block text-sm font-medium leading-6 text-gray-900">Search Title</label>
				<div class="relative mt-2 w-full">
					<input type="text" name="search-title" id="search-title" v-model="search"
						class="peer block w-full border-0 bg-gray-50 py-1.5 text-gray-900 focus:ring-0 sm:text-sm sm:leading-6"
						placeholder=". . ." />
					<div class="absolute inset-x-0 bottom-0 border-t border-gray-300 peer-focus:border-t-2 peer-focus:border-purple-600"
						aria-hidden="true" />
				</div>
			</div>
			
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 px-6 py-4">
				<div v-for="item in computedItems" :key="item.id"
					:class="[item.index == itemEdit.index ? 'bg-blue-50' : 'bg-white', 'relative flex items-center space-x-3 rounded-lg border border-gray-300 px-6 py-5 shadow-sm']">
					<div v-if="isEdit && !isSelected(item.index)" class="min-w-0 flex-1">
						<div class="relative rounded-md shadow-sm">
							<input v-model="itemEdit.title" type="text" name="edit-title" id="edit-title"
								class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6"
								placeholder="TITLE" />
							<div
								class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs uppercase">
								Title
							</div>
						</div>
						<div class="relative mt-2 rounded-md shadow-sm">
							<input v-model="itemEdit.order" type="text" name="edit-order" id="edit-order"
								class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6"
								placeholder="TITLE" />
							<div
								class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs uppercase">
								order
							</div>
						</div>
						<div class="relative flex items-start mt-2">
							<div class="flex h-6 items-center">
								<input id="create-item-is-default" v-model="itemEdit.is_default"
									name="create-item-is-default" type="checkbox"
									class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
							</div>
							<div class="ml-3 text-sm leading-6">
								<label for="create-item-is-default" class="font-medium text-gray-900">Is Default?</label>
							</div>
						</div>
					</div>
					<div v-else class="min-w-0 flex-1">
						<p class="text-sm font-medium text-gray-900">{{ item.title }}</p>
						<p class="truncate text-sm text-gray-500">{{ `Order: ${item.order}` }}</p>
						<p class="truncate text-sm text-gray-500">{{ `Default: ${item.is_default ? 'True' : 'False'}` }}</p>
					</div>
					<div v-if="isEdit && !isSelected(item.index)" class="flex gap-2">
						<button type="button" @click="resetEditForm"
							class="btn btn-sm btn-circle btn-outline hover:bg-gray-200">
							<XMarkIcon :class="['h-5 w-5 hover:text-gray-400']" />
						</button>
						<button type="button" @click="confirmedUpdate"
							:class="['btn btn-sm btn-success btn-circle btn-outline hover:bg-green-200']">
							<CheckIcon :class="['h-5 w-5 text-green-600']" />
						</button>
					</div>
					<div v-else class="flex gap-2">
						<button type="button" :disabled="isSelected(item.index)" @click="editRow(item.index)"
							class="btn btn-sm btn-circle btn-outline btn-warning hover:bg-yellow-200">
							<PencilIcon
								:class="[isSelected(item.index) ? 'text-white' : 'text-yellow-700', 'h-5 w-5 hover:text-yellow-900']" />
						</button>
						<button type="button" :disabled="isSelected(item.index)" @click="removeRow(item.index)"
							:class="['btn btn-sm btn-error btn-circle btn-outline hover:bg-red-200']">
							<TrashIcon :class="[isSelected(item.index) ? 'text-white' : 'text-red-700', 'h-5 w-5']" />
						</button>
					</div>
				</div>
			</div>
		</div>

		<TransitionRoot as="template" :show="open">
			<Dialog as="div" class="relative z-10" @close="resetEditForm">
				<TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
					leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
					<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
				</TransitionChild>

				<div class="fixed inset-0 z-10 overflow-y-auto">
					<div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
						<TransitionChild as="template" enter="ease-out duration-300"
							enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
							enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
							leave-from="opacity-100 translate-y-0 sm:scale-100"
							leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
							<DialogPanel
								class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
								<div class="sm:flex sm:items-start">
									<div
										class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
										<ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
									</div>
									<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
										<DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Remove
											Item</DialogTitle>
										<div class="mt-2">
											<p class="text-sm text-gray-500">{{ `Item title: "${itemEdit.title}"` }}</p>
										</div>
									</div>
								</div>
								<div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
									<button type="button"
										class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
										@click="confirmedDelete()">Remove</button>
									<button type="button"
										class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
										@click="resetEditForm()" ref="cancelButtonRef">Cancel</button>
								</div>
							</DialogPanel>
						</TransitionChild>
					</div>
				</div>
			</Dialog>
		</TransitionRoot>
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
									<form @submit.prevent="submitNewItem" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
										<div class="flex min-h-0 flex-1 flex-col overflow-y-scroll">
											<div class="">
												<div class="bg-purple-700 py-6 px-4 sm:px-6">
													<div class="flex items-center justify-between">
														<DialogTitle class="text-base font-semibold leading-6 text-white">
															Create Item</DialogTitle>
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
												<div class="mt-2 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
													<div class="sm:col-span-6">
														<label for="create-item-title"
															class="block text-sm font-medium leading-6 text-gray-900">Title <span class="text-red-600">*</span></label>
														<div class="mt-2">
															<textarea v-model="newItem.title" id="create-item-title"
																name="create-item-title" rows="3"
																class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="newItem.errors.title">{{
															newItem.errors.title }}</p>
													</div>
													<div class="sm:col-span-6">
														<label for="create-item-order"
															class="block text-sm font-medium leading-6 text-gray-900">Order
															By <span class="text-red-600">*</span></label>
														<div class="mt-2">
															<input v-model="newItem.order" type="text"
																name="create-item-order" id="create-item-order"
																placeholder="A, B, C, 1, 2, 3..."
																class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
														</div>
														<p class="mt-2 text-sm text-red-500" v-if="newItem.errors.order">{{
															newItem.errors.order }}</p>
													</div>
													<div class="sm:col-span-6 relative flex items-start">
														<div class="flex h-6 items-center">
															<input id="create-item-is-default" v-model="newItem.is_default"
																name="create-item-is-default" type="checkbox"
																class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
														</div>
														<div class="ml-3 text-sm leading-6">
															<label for="create-item-is-default"
																class="font-medium text-gray-900">Is Default?</label>
															<p class="text-gray-500">Ticking this will automatically add the
																item in creating new training instances.</p>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="flex flex-shrink-0 justify-between px-4 py-4">
											<button type="button"
												class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-yellow-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:ring-gray-400"
												@click="newItem.reset()">Reset Form</button>
											<div>
												<button type="button"
													class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:ring-gray-400"
													@click="openForm = false">Close</button>
												<button type="submit"
													:class="[newItem.processing ? 'bg-gray-500' : 'bg-purple-600 hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-500' ,'ml-4 inline-flex justify-center rounded-md  py-2 px-3 text-sm font-semibold text-white shadow-sm']">
													{{ newItem.processing ? 'Processing...' : 'Save' }}
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
	</BreezeAuthenticatedLayout>
</template>
<script>
export default {
	props: [
		"link",
		"title",
		"items"
	],
	methods: {
		editItemInit(index_) {
			const obj = this.items.find((x, index) => index_ == index);
			if (obj) {
				const { id, title, order, is_default } = obj;
				this.itemEdit = Object.assign({}, this.itemEdit, {
					id,
					title,
					order,
					is_default: is_default ? true : false,
					index: index_
				});
			}
		},
		editRow(index) {
			this.editItemInit(index);
			this.isEdit = true;
		},
		removeRow(index) {
			this.editItemInit(index);
			this.open = true;
		},
		isSelected(index) {
			const index_ = this.itemEdit.index;
			return index_ != null && index_ != index ? true : false;
		},
		resetEditForm() {
			this.open = false;
			this.isEdit = false;
			this.itemEdit.reset();
		},
		confirmedDelete() {
			const link = this.link
			const { id } = this.itemEdit
			this.itemEdit.delete(route(`${link}.destroy`, id), {
				preserveScroll: true,
				onSuccess: () => { this.open = false; this.itemEdit.reset() }
			})
		},
		confirmedUpdate() {
			const link = this.link
			const { id } = this.itemEdit
			// console.log(route(`${link}.update`, id))
			this.itemEdit.put(route(`${link}.update`, {id}), {
				preserveScroll: true,
				onSuccess: () => { this.itemEdit.reset(); this.isEdit = false; }
			})
		},
		submitNewItem(){
			const link = this.link
			this.newItem.post(route(`${link}.store`), {
				onSuccess: () => { this.newItem.reset() }
			})
		}
	},
	computed: {
		computedItems() {
			const items = this.items.map(function (obj, index) {
				return { ...obj, index };
			});
			if(this.search){
				return items.filter(x => x.title.toLowerCase().includes(this.search.toLowerCase()))
			}
			return items
		}
	},
	data() {
		return {
			itemEdit: useForm({
				id: "",
				title: "",
				order: "",
				is_default: false,
				index: null,
			}),
			newItem: useForm({
				title: "",
				order: "",
				is_default: false,
			}),
			search: '',
			isEdit: false,
			open: false,
			openForm: false,
		};
	},
	components: { XMarkIcon, CheckIcon }
}
</script>
<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
	PencilIcon,
	TrashIcon,
	ExclamationTriangleIcon,
	XMarkIcon,
	CheckIcon,
	PlusIcon
} from '@heroicons/vue/24/outline'
</script>