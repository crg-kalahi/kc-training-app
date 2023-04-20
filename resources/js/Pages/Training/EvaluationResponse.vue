<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { ArrowLeftIcon, DocumentArrowDownIcon, DocumentTextIcon, UserIcon } from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import moment from 'moment'
import { StarIcon  } from '@heroicons/vue/20/solid';
import Button from '@/Components/Button.vue';
</script>
<script>
  import _ from 'lodash'
  import DangerAlertDialog from '@/Components/DangerAlertDialog.vue';
  import InfoAlertDialog from '@/Components/InfoAlertDialog.vue';
  export default {
    props: ["training", "officeRep", "keyTraining", "keyRP", "keyLearning", "resourcePerson", "evaluations"],
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
        k_training(){},
        k_rp(){},
        k_learning(){
          const items = this.keyLearning
          if(!this.form.e_key_learning.length) return []
          const { e_key_learning } = this.form
          const ids = _.uniq(e_key_learning.map(x => x.learning_id))
          return items.filter( x => ids.includes(x.id) )
        }
    },
    data() {
        return {
            isFormProcessing: false,
            isProcessing: false,
            toggleDangerAlert: false,
            toggleInfoAlert: false,
            selectedRespondent: null,
            form: {
              e_key_areas: [],
              e_key_rp: [],
              e_key_learning: []
            },
        };
    },
    mounted() {
        // this.form.training_id = this.training.id;
    },
    methods: {
      async proceedUpdate(){
        try {
          
          const evaluation = this.selectedRespondent
          const { e_key_areas, e_key_rp, e_key_learning } = this.form
          //areas
          const areas = e_key_areas.map(function({id, stat}){
            return { id, stat }
          })
          //learning
          const learnings = e_key_learning.map(function({id, answer}){
            return { id, answer }
          })
  
          let rp = []
          e_key_rp.forEach(function(obj){
            obj.items.map(function({id, stat}){
              rp.push({id, stat})
            })
          })
          this.isFormProcessing = true
          await axios({
            method: 'PUT',
            url: route('training.evaluation-response.update', {id: evaluation.id}),
            data: {
              is_female: evaluation.is_female, office_rep_id: evaluation.office_rep_id, areas, learnings, rp
            }
          })
          this.toggleInfoAlert = false
          this.isFormProcessing = false
        } catch (error) {
          this.toggleInfoAlert = false
          this.isFormProcessing = false
          console.log(error)
        }
      },
      async proceedRemove(){
        try {
          const { id } = this.selectedRespondent
          const training = this.training
          this.isFormProcessing = true
          await axios.delete(route('training.evaluation-response.delete', {id}))
          this.selectedRespondent = false
          this.isFormProcessing = false
          this.toggleDangerAlert = false
          this.$inertia.get(route('training.evaluation-response', {id: training.id}))
        } catch (error) {
          this.toggleDangerAlert = false
          this.isFormProcessing = false
          console.log(error)
        }
      },
      clearForm(){
        this.selectedRespondent = null
        this.form = {
          e_key_areas: [],
          e_key_rp: [],
          e_key_learning: []
        }
      },
      respondentWasSelected(item){
        if(this.selectedRespondent == item){
          this.clearForm()
          return
        }
        this.selectedRespondent = item
        this.getItem(item.id)
      },
      async getItem(id){
        try {
          this.isProcessing = true
          const res = await axios.get(route('training.evaluation-response.show', {id}))
          const { e_key_areas, e_key_rp, e_key_learning } = res.data

          //training
          let t_areas = []
          if(e_key_areas.length){
            const ids = _.uniq(e_key_areas.map(x => x.area_training_id))
            t_areas = this.keyTraining.filter( x => ids.includes(x.id) ).map(function({id, title}){
              const item = e_key_areas.find(x => x.area_training_id == id)
              return { ...item, title }
            })
          }

          //rp
          let rps = []
          if(e_key_rp.length){
            const ids = _.uniq(e_key_rp.map(x => x.area_rp_id))
            let keys = this.keyRP.filter(x => ids.includes(x.id)).map(function({id, title}){return { id, title }})
            const rps_id = _.uniq(e_key_rp.map(x => x.rp_id))
            rps = this.resourcePerson.filter(x => rps_id.includes(x.id))
              .map(function({ id, full_name, topic }){
                const rp_id = id
                return { full_name, topic, items: keys.map(function({id, title}){
                  const item = e_key_rp.find(function(key){
                    return key.area_rp_id == id && rp_id == key.rp_id
                  })
                  return { ...item, title }
                }) }
              })
          }
          
          //learning
          let learnings = []
          if(e_key_learning.length){
            const ids = _.uniq(e_key_learning.map(x => x.learning_id))
            learnings = this.keyLearning.filter( x => ids.includes(x.id) ).map(function({id, title}){
              const item = e_key_learning.find(x => x.learning_id == id)
              return { ...item, title }
            })
          }
          this.form = {e_key_areas: t_areas, e_key_rp: rps, e_key_learning: learnings}
          this.isProcessing = false
        } catch (error) {
          console.log(error)
          this.clearForm()
        }
      }
    },
    components: { StarIcon, DocumentArrowDownIcon, DocumentTextIcon, UserIcon, Button, ArrowLeftIcon, DangerAlertDialog, InfoAlertDialog }
}
</script>

<template>
    <Head title="Evaluation Response" />

    <BreezeAuthenticatedLayout>
        <!-- Page title & actions -->
    <div class="mx-auto flex items-start">
      <aside class="sticky top-0 w-64 shrink-0 lg:block max-h-screen overflow-auto">
        <!-- Left column area -->
        <div class="sticky top-0 px-3 py-5 bg-indigo-500 w-full">
          <p class="text-white font-bold">{{`Respondents: ${evaluations.length}`}}</p>
        </div>
        <ul role="list" class="divide-y divide-gray-200">
          <li v-for="person in evaluations" :key="person.id" :class="['flex py-4 cursor-pointer px-2', person.id == selectedRespondent?.id ? 'bg-yellow-300 hover:bg-yellow-300': 'hover:bg-blue-50']" @click="respondentWasSelected(person)">
            <UserIcon :class="['h-10 w-10', person.is_female ? 'text-pink-500' : 'text-blue-500']"/>
            <!-- <img class="h-10 w-10 rounded-full" :src="person.id" alt="" /> -->
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">{{ person.is_female ? 'Female' : 'Male' }}</p>
              <p class="text-xs text-gray-500">{{ moment(person.created_at).format('MMM-DD-YYYY, hh:mm A') }}</p>
            </div>
          </li>
        </ul>
      </aside>

      <section class="flex-1">
        <!-- Main area -->
        <header>
          <div class="px-3 pt-4 sm:flex sm:items-center sm:justify-between sm:px-6">
              <div class="min-w-0 flex-1">
                  <!-- <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1> -->
                <div class="text-xs md:text-sm font-medium breadcrumbs">
                    <ul>
                        <li><Link :href="route('training.index')">Training</Link></li>
                        <li><Link :href="route('training.edit', {id: training.id})">{{ training.id }}</Link></li>
                        <li><Link :href="route('training.evaluations', {id: training.id})">Evaluation</Link></li>
                        <li><span class="text-teal-500 tracking-wider font-bold px-3 py-2 rounded-md">Responses</span></li>
                    </ul>
                </div>
              </div>
          </div>
          <div class="px-3 sm:px-6 sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-base font-semibold leading-6 text-gray-900">{{ `${training.title}` }}</h1>
              <p class="mt-2 text-sm text-gray-700">{{ `${trainingDate} @ ${training.venue}` }}</p>
            </div>
          </div>
        </header>
        <div v-if="selectedRespondent == null" class="mx-6 mt-10 gap-x-8 bg-gray-100 border-2 rounded-md flex items-center justify-center border-gray-500 border-dashed h-40">
          <ArrowLeftIcon class="h-14 w-14"/>
          <p class="uppercase text-lg">Please select respondent</p>
        </div>
        <div v-if="selectedRespondent" class="relative w-full pb-10 mt-5 border-t-4 border-dashed border-indigo-500">
          <div v-if="isProcessing" :class="['absolute inset-0 bg-indigo-500 opacity-95 animate-pulse']"></div>
          <div class="px-6 pb-10 pt-5 bg-yellow-100">
            <div class="flex gap-x-3">
              <div>
                <label for="gender" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Gender</label>
                <select v-model="selectedRespondent.is_female" id="gender" name="gender" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                  <option :value="0">Male</option>
                  <option :value="1">Female</option>
                </select>
              </div>
              <div>
                <label for="office-rep" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Office Representative</label>
                <select v-model="selectedRespondent.office_rep_id" id="office-rep" name="office-rep" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                  <tr v-for="area, index in form.e_key_areas" :key="area.title" :class="[index % 2 === 0 ? 'bg-gray-50' : '', 'divide-x divide-gray-200']">
                    <td class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-2">{{ `${index + 1}.) ${area.title}` }}</td>
                    <td v-for="item in 5" class="whitespace-nowrap px-2 text-center text-gray-500">
                      <input :id="index" @input="area.stat = item" type="radio" :checked="area.stat == item" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-5" v-for="rp in form.e_key_rp" :key="rp.topic">
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
                  <tr v-for="area, indexKey in rp.items" :key="area.title" :class="[indexKey % 2 === 0 ? 'bg-gray-50' : '', 'divide-x divide-gray-200']">
                    <td class="whitespace-nowrap py-1 pl-4 pr-4 text-sm font-medium text-gray-900 sm:pl-2">{{ `${indexKey + 1}.) ${area.title}` }}</td>
                    <td v-for="item in 5" :key="`${rp.id}-${area.title}-${item}`" class="whitespace-nowrap px-2 text-center text-gray-500">
                      <input v-model="area.stat" type="radio" :value="item" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                      <!-- <button type="button" @click="updateRpArea(indexRp, indexKey, item)">
                        <StarIcon :class="['h-6 w-6', area.stat == item ? 'text-yellow-500' : 'text-gray-200']"/>
                      </button> -->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- {{ form.e_key_rp }} -->
            <div class="mt-10">
              <div class="mt-3" v-for="item, index in form.e_key_learning" :key="item.title">
                <label :for="`${item.id}-key-learning`" class="block text-sm font-medium leading-6 text-gray-900">{{ `${index+1}.) ${item.title}` }}</label>
                <div class="mt-2">
                  <textarea :id="`${item.id}-key-learning`" :name="`${item.id}-key-learning`" v-model="item.answer" rows="2" class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
                </div>
                <!-- <p class="mt-2 text-sm text-gray-500">Write a few sentences about yourself.</p> -->
              </div>
            </div>
          </div>
          <div class="fixed bottom-0 bg-white w-full border-t border-gray-300 px-3 py-4">
            <p v-if="isProcessing" class="text-lg">
              Processing...
            </p>
            <div v-else class="flex gap-x-2">
              <Button @click="toggleDangerAlert = true" type="button" :disabled="isFormProcessing" :class="['bg-red-500 hover:bg-red-700', isFormProcessing ? 'animate-pulse' :'']">DELETE</Button>
              <Button @click="toggleInfoAlert = true" type="button" class="bg-blue-500 hover:bg-blue-700">UPDATE</Button>
            </div>
          </div>
        </div>
      </section>
    </div>
    <DangerAlertDialog v-model="toggleDangerAlert" :title="'System Alert'" :subtitle="`The system is about to remove one(1) evaluation response, Proceed?`" @approved="proceedRemove"></DangerAlertDialog>
    <InfoAlertDialog v-model="toggleInfoAlert" :title="'System Information'" :loading="isFormProcessing" :subtitle="`The system is about to update one(1) evaluation response, Proceed?`" @approved="proceedUpdate"></InfoAlertDialog>
    <!-- end -->
    </BreezeAuthenticatedLayout>
</template>
