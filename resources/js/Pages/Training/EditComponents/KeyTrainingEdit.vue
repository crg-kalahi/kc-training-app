<template>
    <form @submit.prevent="submitForm">
    <div class="overflow-hidden shadow sm:rounded-md">
				<div class="bg-white min-h-16 px-6 p-5 md:grid md:grid-cols-2 md:gap-x-6">
					<fieldset class="md:col-span-1">
						<div class="flex items-center">
							<legend class="text-base flex-1 font-semibold leading-6 text-gray-900">Key Area: Training</legend>
							<!-- <input @input="selectAllInput" type="checkbox" v-model="selectAll[0]" class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-600" /> -->
						</div>
						<div class="mt-4 divide-y divide-gray-200 border-t border-b border-gray-200">
						<div v-for="(item, itemIdx) in listTraining" :key="itemIdx" class="relative flex items-start py-2">
								<div class="min-w-0 flex-1 text-sm leading-6">
								<label :for="`item-${item.id}-training`" class="select-none font-medium text-gray-900 cursor-pointer">{{ item.title }}</label>
								</div>
								<div class="ml-3 flex h-6 items-center">
								<input :id="`item-${item.id}-training`" v-model="key_training[itemIdx]" :name="`item-${item.id}-training`" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600" />
								</div>
						</div>
						</div>
					</fieldset>
					<div class="md:col-span-1">
						<fieldset>
							<div class="flex items-center">
								<legend class="text-base flex-1 font-semibold leading-6 text-gray-900">Key Area: Resource Person</legend>
								<!-- <input @input="selectAllInput" type="checkbox" v-model="selectAll[1]" class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-600" /> -->
							</div>
							<div class="mt-4 divide-y divide-gray-200 border-t border-b border-gray-200">
							<div v-for="(item, itemIdx) in listKeyResourcePerson" :key="itemIdx" class="relative flex items-start py-2">
									<div class="min-w-0 flex-1 text-sm leading-6">
									<label :for="`item-${item.id}-rp`" class="select-none font-medium text-gray-900 cursor-pointer">{{ item.title }}</label>
									</div>
									<div class="ml-3 flex h-6 items-center">
									<input :id="`item-${item.id}-rp`" v-model="key_rp[itemIdx]" :name="`item-${item.id}-rp`" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600" />
									</div>
							</div>
							</div>
						</fieldset>
						<fieldset class="mt-10">
							<div class="flex items-center">
								<legend class="text-base font-semibold leading-6 text-gray-900 flex-1">Key Area: Learning</legend>
								<!-- <input @input="selectAllInput" type="checkbox" v-model="selectAll[2]" class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-600" /> -->
							</div>
							<div class="mt-4 divide-y divide-gray-200 border-t border-b border-gray-200">
							<div v-for="(item, itemIdx) in listLearning" :key="itemIdx" class="relative flex items-start py-2">
									<div class="min-w-0 flex-1 text-sm leading-6">
									<label :for="`item-${item.id}-learning`" class="select-none font-medium text-gray-900 cursor-pointer">{{ item.title }}</label>
									</div>
									<div class="ml-3 flex h-6 items-center">
									<input :id="`item-${item.id}-learning`" v-model="key_learning[itemIdx]" :name="`item-${item.id}-learning`" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600" />
									</div>
							</div>
							</div>
						</fieldset>
					</div>
        </div>
        <div class="bg-gray-50 px-4 py-3 flex justify-end items-center sm:px-6">
					<Transition name="fade">
						<p v-if="form.recentlySuccessful" class="text-green-700 font-bold mr-5">Successfully updated!</p>
					</Transition>
					<button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
					</div>
				</div>
    </form>
</template>
<script>
		import { useForm } from '@inertiajs/inertia-vue3';
    export default {
        props: ["listTraining", "listLearning", "listKeyResourcePerson", "training"],
        methods: {
            submitForm(){
							const { id } = this.training
							const learnings = this.key_learning
							const rp = this.key_rp
							const training = this.key_training
							this.form.key_learning = this.listLearning.filter(function(obj, index){
								return learnings[index]
							}).map(x => x.id)
							this.form.key_rp = this.listKeyResourcePerson.filter(function(obj, index){
								return rp[index]
							}).map(x => x.id)
							this.form.key_training = this.listTraining.filter(function(obj, index){
								return training[index]
							}).map(x => x.id)

							this.form.put(route('training.key_factors', {id}), {
								preserveScroll: true
							})
						},
						// selectAllInput(){
						// 	const val = this.selectAll
						// 	if(val[0]){
						// 		this.key_training = this.listTraining.map(x => true)
						// 	}else{ this.key_training = [] }
						// 	if(val[1]){
						// 		this.key_rp = this.listKeyResourcePerson.map(x => true)
						// 	}else{ this.key_rp = [] }
						// 	if(val[2]){
						// 		this.key_learning = this.listLearning.map(x => true)
						// 	}else{ this.key_learning = [] }
						// }
        },
				mounted(){
					const { key_trainings, key_learnings, key_rp } = this.training
					this.key_training = this.listTraining.map(function(obj){
						return key_trainings ? key_trainings.includes(obj.id) ? true : null : null
					})
					this.key_learning = this.listLearning.map(function(obj){
						return key_learnings ? key_learnings.includes(obj.id) ? true : null : null
					})
					this.key_rp = this.listKeyResourcePerson.map(function(obj){
						return key_rp ? key_rp.includes(obj.id) ? true : null : null
					})
				},
				data(){
					return{
						form: useForm({
							key_training: [],
							key_learning: [],
							key_rp: []
						}),
						key_training: [],
						key_learning: [],
						key_rp: [],
						selectAll: [false, false, false]
					}
				},
    }
</script>