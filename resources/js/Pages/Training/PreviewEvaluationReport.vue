<template>
  <Head title="Evaluation Report" />
  <div class="px-4 py-10 sm:px-6 lg:px-8 bg-blue-200 min-h-screen">
    <!-- We've used 3xl here, but feel free to try other max-widths based on your needs -->
    <div class="mx-auto pt-5 rounded-md max-w-3xl bg-white shadow-md">
      <div class="flex items-center justify-between px-3 py-8 border-b-2 border-gray-700">
        <div class="flex gap-x-1 px-5">
          <img src="/storage/assets/dswd-logo.png" class=" w-40 h-auto"/>
          <img src="/storage/assets/fo-caraga-insignia.png" class=" w-40 h-auto"/>
        </div>
        <div class="text-center w-80">
          <h3 class="font-bold text-base">KALAHI-CIDSS</h3>
          <h3 class="font-bold text-base">DSWD / FIELD OFFICE CARAGA</h3>
          <input type="text" class="border-none h-4 text-xs w-full text-center overflow-visible px-0" :value="'DSWD-SWIDB-GF-016 | REV -- | DD MMMM YYYY'" />
        </div>
      </div>
      <div class="px-3 py-3 text-center">
        <h3 class="text-lg font-bold leading-6 text-gray-900">
          Training Evaluation Summary
        </h3>
        <h3 class="mt-4 text-base font-bold leading-6 text-gray-900">{{ training.title }}</h3>
        <p class="mt-3 max-w-4xl text-sm text-gray-800">{{ dateConvert(training) }}</p>
        <p class="max-w-4xl text-sm text-gray-800">{{ `Venue: ${training.venue}` }}</p>
      </div>
      <div class="px-3 py-3 border-b border-gray-200">
        <table class="mb-10 min-w-full border-collapse border border-slate-500">
          <thead>
            <tr class="bg-blue-50 border border-gray-400">
              <th colspan="2" scope="col" class="py-2 pl-2 pr-3 text-left text-sm font-semibold border border-slate-600 text-gray-900 sm:pl-2 capitalize">Training Content and Design</th>
              <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Rating</th>
              <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Adjective Rating</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in keyTraining" :key="`training-${item.id}`">
              <td scope="col" class="text-center px-2 text-sm font-semibold border border-slate-600 text-gray-900">{{ item.order }}</td>
              <td :class="[item.class, 'whitespace-nowrap border border-slate-700 py-1 pl-4 pr-3 text-sm text-gray-900 sm:pl-2']">{{ item.title }}</td>
              <td class="whitespace-nowrap border border-slate-700 text-center w-14 px-3 py-1 text-sm text-gray-900">{{ item.rating }}</td>
              <td class="whitespace-nowrap border border-slate-700 text-center px-3 py-1 text-sm text-gray-900">{{ adjRating(item.rating) }}</td>
            </tr>
          </tbody>
        </table>
        <table class="min-w-full mb-1 border-collapse border border-slate-500">
          <thead>
            <tr class="bg-blue-50 border border-gray-400">
              <th scope="col" class="text-left px-2 text-sm font-semibold border border-slate-600 text-gray-900">{{`Resource ${resourcePersons.length > 1 ? 'Persons' : 'Person'}:`}}</th>
            </tr>
          </thead>
        </table>
        <div v-for="item,index in resourcePersons" :key="item.id" class="mb-1">
          <table class="min-w-full border-collapse border border-slate-500">
              <thead>
                <tr class="bg-blue-50 border border-gray-400">
                  <th scope="col" class="text-center px-2 text-sm font-semibold border border-slate-600 text-gray-900">{{ index+1 }}</th>
                  <th scope="col" class="py-2 pl-4 pr-3 text-left text-sm font-semibold border border-slate-600 text-gray-900 sm:pl-2 capitalize">{{ item.full_name }}</th>
                  <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Rating</th>
                  <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Adjective Rating</th>
                </tr>
              </thead>
              <tbody class="">
                <tr v-for="rp_item in key_rp" :key="`${rp_item.id}-${item.id}`">
                  <td class="whitespace-nowrap border border-slate-700 text-center text-sm text-gray-900">{{ rp_item.order }}</td>
                  <td class="whitespace-nowrap border border-slate-700 py-1 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-2">{{ rp_item.title }}</td>
                  <td class="whitespace-nowrap border border-slate-700 text-center w-14 px-3 py-1 text-sm text-gray-900">{{ rp_per_key(item.rp_id, rp_item.id)[5] }}</td>
                  <td class="whitespace-nowrap border border-slate-700 text-center px-3 py-1 text-sm text-gray-900">{{ adjRating(rp_per_key(item.rp_id, rp_item.id)[5]) }}</td>
                </tr>
                <tr>
                  <td colspan="2" class="whitespace-nowrap border border-slate-700 py-1 pl-4 pr-3 text-right text-sm font-bold uppercase text-gray-500 sm:pl-2">Total Rating</td>
                  <td class="whitespace-nowrap border border-slate-700 font-semibold text-center w-14 px-3 py-1 text-sm text-gray-900">{{ rp_overall(item.rp_id)[5] }}</td>
                  <td class="whitespace-nowrap border border-slate-700 font-semibold text-center px-3 py-1 text-sm text-gray-900">{{ adjRating(rp_overall(item.rp_id)[5]) }}</td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="mt-6 flow-root">
          <ul role="list" class="-my-5 divide-y divide-gray-200">
            <li v-for="learning in key_learning" :key="learning.id" class="py-5">
              <div class="relative focus-within:ring-2 focus-within:ring-indigo-500">
                <h3 class="text-sm font-semibold text-gray-800">
                    <!-- Extend touch target to entire panel -->
                    <span class="absolute inset-0" aria-hidden="true" />
                    {{ `${learning.order}. ${learning.title}` }}
                </h3>
                <p v-for="item in filterLearning(learning.id)" class="mt-1 ml-2 line-clamp-2 text-sm text-gray-700">{{ `* ${item.answer}` }}</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- <div v-if="training.facilitators.length" class="px-4 py-3">
        <p class="mb-2 max-w-4xl text-sm text-gray-500 uppercase">Facilitated By:</p>
        <div class="flex gap-2">
          <section v-for="people in training.facilitators" :key="people.id" class="inline-flex items-center gap-x-3 pl-2">
            <img class="h-6 w-6 rounded-full" :src="people.avatar" alt="" />
            <p class="max-w-4xl text-sm text-gray-700 uppercase">{{ people.full_name }}</p>
          </section>
        </div>
      </div> -->
    </div>
  </div>
</template>
<script>
import _ from 'lodash';
import moment from 'moment';
export default {
  props: ['key_rp', 'key_learning', 'key_training', 'items_rp', 'items_learning', 'item_training', 'training'],
  computed:{
    resourcePersons(){
      const items = this.items_rp
      return (_.uniqBy(items, 'rp_id')).map(function({rp_id, area_rp_id, stat, total_count, is_female, is_internal, position, lname, fname, mname, ext_name}){
        return { rp_id, area_rp_id, stat, total_count, is_female, is_internal, position, full_name: `${lname}, ${fname}${mname ? ` ${mname[0]}.` : ''} ${ext_name}` }
      })
    },
    keyTraining(){
      const key = this.key_training
      const trainings = this.item_training
      let items = key.map(function({id, title, order}){
        const items = trainings.filter(x => x.area_training_id == id)
        let stats = [0, 0, 0, 0, 0]
        let total_count_participant = 0
        let total_count_participant_stat = 0
        stats = stats.map(function(v, index){
          const stat = index + 1
          const count = items.filter(x => x.stat == stat).map(x => x.total_count).reduce(function(t, c){ return t+c}, 0);
          total_count_participant += count;
          total_count_participant_stat += count * stat;
        })
        return { id, title, order, class: 'text-left font-semibold', rating: (total_count_participant_stat / total_count_participant).toFixed(2) }
      })
      items.push({
        title: 'Overall Evaluation', order: '*', rating: (_.mean(items.map(x=> parseFloat(x.rating)))).toFixed(2), class: 'text-left font-bold'
      })
      return items
    }
  },
  methods:{
    filterLearning(id){
      if(!this.items_learning.length) return []
      return this.items_learning.filter(function(x){
        return x.learning_id == id
      })
    },
    dateConvert({date_from, date_to}){
      return `${moment(date_from, 'YYYY-MM-DD').format('MMMM DD, YYYY')} - ${moment(date_to, 'YYYY-MM-DD').format('MMMM DD, YYYY')}`
    },
    adjRating(value){
      const v = parseFloat(value)
      if(v <= 1.49) return 'Poor'
      if(v <= 2.49) return 'Fair'
      if(v <= 3.49) return 'Very Satisfactory'
      if(v <= 5.00) return 'Excellent'
    },
    rp_per_key(id, key_id){
      let items = [0, 0, 0, 0, 0]
      let total_count_participant = 0
      let total_count_participant_stat = 0
      const rp_items = this.items_rp.filter(x => x.rp_id == id)
      items = items.map(function(obj, index){
        const f = rp_items.find(function(item){
          const stat = index + 1;
          return item.area_rp_id == key_id && item.stat == stat;
        })
        return f ? f.total_count : 0
      })
      // items.push((_.mean(items)).toFixed(2))
      items.map(function(item, index){
        const stat = index + 1
        total_count_participant += item;
        total_count_participant_stat += item * stat
      })
      items.push((total_count_participant_stat/total_count_participant).toFixed(2))
      return items
    },
    rp_overall(id){
      const rp_items = this.items_rp.filter(x => x.rp_id == id)
      let items = [0, 0, 0, 0, 0]
      let total_count_participant = 0
      let total_count_participant_stat = 0
      items = items.map(function(val, index){
        const stat = index + 1;
        const items = rp_items.filter(x => x.stat == stat).map(x => x.total_count);
        return items.length ? items.reduce(function(total, curr){ return total + curr }, 0) : 0
      })

      items.map(function(item, index){
        const stat = index + 1
        total_count_participant += item;
        total_count_participant_stat += item * stat
      })
      items.push((total_count_participant_stat/total_count_participant).toFixed(2))
      return items
    }
  }
}
</script>
<script setup>
import { Head } from '@inertiajs/inertia-vue3';
</script>