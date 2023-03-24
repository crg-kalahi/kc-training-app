<template>
	<div>
    <button type="button" @click="open = true" class="px-3 py-1 gap-3 rounded-md items-center justify-between inline-flex border border-gray-300 w-full">
        <div class="flex items-center gap-1">
					<img :src="selectedItem.avatar" alt="" class="h-6 w-6 flex-shrink-0 rounded-full" />
					<span class="ml-3 block truncate">{{ `${selectedItem.full_name}` }}</span>
				</div>
        <ChevronUpDownIcon
					class="h-5 w-5 text-gray-400"
					aria-hidden="true"
        />
    </button>
		<TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="open = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-3">
              <div>
                <div class="">
                  <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">User Selection</DialogTitle>
                  <ul role="list" class="divide-y divide-gray-200">
						<li v-for="person in selections" @click="listWasClicked(person.id)" :key="person.full_name" :class="[person.id == selectedItem.id ? 'bg-indigo-100' : 'hover:bg-indigo-50', 'flex py-4 px-2 cursor-pointer']">
							<img class="h-10 w-10 rounded-full" :src="person.avatar" alt="" />
							<div class="ml-3">
								<p class="text-sm font-medium text-gray-900">{{ person.full_name }}</p>
								<p class="text-sm text-gray-500">{{ person.id_number }}</p>
							</div>
						</li>
					</ul>
                </div>
              </div>
              <div class="mt-5 sm:mt-6 flex justify-between">
                <button type="button" class="inline-flex justify-center rounded-md px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" @click="open = false">Cancel</button>
                <button type="button" @click="proceedToEmit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Proceed</button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
	</div>
</template>

<script setup>
import { ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import {
	Dialog,
	DialogPanel,
	DialogTitle,
	DialogDescription,
	TransitionRoot,
	TransitionChild
} from '@headlessui/vue'
</script>

<script>
	export default {
		emits: ['update:id_number'],
		props: ['items', 'id_number'],
		computed: {
			selections(){
				let items = this.items
				// items.unshift({id: '', id_number: '00-0000', full_name: 'Please Select', avatar: 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=UK'})
				return items
			},
			selectedItem(){
				if(this.id_number){
					return this.items.find(x => x.id == this.id_number)
				}
				return { id: '', id_number: '', full_name: 'Please Select', avatar: 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=UK' }
			}
		},
		methods: {
			listWasClicked(id){
				this.open = false
				this.$emit('update:id_number', id)
			},
		},
		data(){
			return{
				open: false,
			}
		}
	}
</script>