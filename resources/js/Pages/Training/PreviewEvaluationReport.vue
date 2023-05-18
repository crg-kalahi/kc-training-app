<template>
  <Head title="Evaluation Report" />
  <div class="px-4 py-10 sm:px-6 lg:px-8 bg-blue-200 min-h-screen">
    <!-- We've used 3xl here, but feel free to try other max-widths based on your needs -->
    <div id="section-to-print" class="px-1 mx-auto pt-5 rounded-md max-w-3xl bg-white">
      <table class="min-w-full border-collapse">
        <tbody>
          <tr class="border-b-4 border-gray-600">
            <td colspan="2">
              <div class="flex gap-x-1 px-5">
                <img src="/storage/assets/dswd-logo.png" class=" w-40 h-auto"/>
                <img src="/storage/assets/fo-caraga-insignia.png" class=" w-40 h-auto"/>
              </div>
            </td>
            <td colspan="2" class="">
              <div class="text-center w-80">
                <h3 class="font-bold text-base">KALAHI-CIDSS</h3>
                <h3 class="font-bold text-base">DSWD / FIELD OFFICE CARAGA</h3>
                <input type="text" autofocus class="border-none rounded-md h-4 text-xs w-full text-center overflow-visible px-0 focus:bg-green-300" :value="'DSWD-SWIDB-GF-016 | REV -- | DD MMMM YYYY'" />
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="4">
              <h3 class="py-3 text-center text-lg font-bold leading-6 text-gray-900">
                Training Evaluation Summary
              </h3>
            </td>
          </tr>
          <tr>
            <td colspan="4" class="text-center">
              <h3 class="text-base font-bold leading-6 text-gray-900">{{ training.title }}</h3>
              <p class="mt-2 max-w-4xl text-sm text-gray-800">{{ dateConvert(training) }}</p>
              <p class="max-w-4xl text-sm text-gray-800">{{ `Venue: ${training.venue}` }}</p>
            </td>
          </tr>
          <tr class="bg-blue-50 border border-gray-400">
            <th colspan="2" scope="col" class="py-2 pl-2 pr-3 text-left text-sm font-semibold border border-slate-600 text-gray-900 sm:pl-2 capitalize">Training Content and Design</th>
            <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Rating</th>
            <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Adjective Rating</th>
          </tr>
          <tr v-for="item, index in keyTraining" :key="`training-${item.id}`">
            <td scope="col" class="text-center w-10 px-2 text-sm font-semibold border border-slate-600 text-gray-900">{{ item.order != '*' ? index + 1 : '*'   }}</td>
            <td :class="[item.class, 'whitespace-nowrap border border-slate-700 py-1 pl-4 pr-3 text-sm text-gray-900 sm:pl-2']">{{ item.title }}</td>
            <td class="whitespace-nowrap border border-slate-700 text-center w-14 px-3 py-1 text-sm text-gray-900">{{ item.rating }}</td>
            <td class="whitespace-nowrap border border-slate-700 text-center px-3 py-1 text-sm text-gray-900">{{ adjRating(item.rating) }}</td>
          </tr>
          <tr class="bg-blue-50 border border-gray-400">
            <th colspan="4" scope="col" class="text-left px-2 text-sm font-semibold border border-slate-600 text-gray-900">{{`Resource ${resourcePersons.length > 1 ? 'Persons' : 'Person'}:`}}</th>
          </tr>
        </tbody>
        <tbody v-for="item, index in resourcePersons" :key="item.id">
          <tr class="bg-blue-50 border border-gray-400">
            <th scope="col" class="text-center px-2 text-sm font-semibold border border-slate-600 text-gray-900">{{ index+1 }}</th>
            <th scope="col" class="py-2 pl-4 pr-3 text-left text-sm font-semibold border border-slate-600 text-gray-900 sm:pl-2 capitalize">{{ item.full_name }}</th>
            <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Rating</th>
            <th scope="col" class="px-3 py-2 text-center text-sm font-semibold border border-slate-600 text-gray-900">Adjective Rating</th>
          </tr>
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
        <tbody>
          <tr>
            <td colspan="3"></td>
            <td class="flex justify-end pt-3">
              <div class="border border-gray-600 rounded-sm px-2 py-1">
                <h5 class="italic text-xs font-bold">Rating</h5>
                <p class="text-xs font-semibold">1.00 - 1.49 - Poor</p>
                <p class="text-xs font-semibold">1.50 - 2.49 - Fair</p>
                <p class="text-xs font-semibold">2.50 - 3.49 - Satisfactory</p>
                <p class="text-xs font-semibold">3.50 - 4.49 - Very Satisfactory</p>
                <p class="text-xs font-semibold">4.50 - 5.00 - Outstanding</p>
              </div>
            </td>
          </tr>
        </tbody>
        <tbody v-for="learning, index in key_learning" :key="learning.id">
          <tr>
            <td colspan="4">
              <h3 class="text-sm font-semibold text-gray-800">
                  <!-- Extend touch target to entire panel -->
                  <!-- <span class="absolute inset-0" aria-hidden="true" /> -->
                  {{ `${index + 1}. ${learning.title}` }}
              </h3>
            </td>
          </tr>
          <tr v-for="item in filterLearning(learning.id)">
            <td colspan="4">
              <p class="ml-2 line-clamp-2 text-sm text-gray-700">{{ `* ${item.answer}` }}</p>
            </td>
          </tr>
        </tbody>
        <tfoot class="border-none">
          <tr>
            <td colspan="4" class="text-center pt-4">
              <!-- <h4 class="text-xs">Page ___ of ___</h4> -->
              <div class="flex items-center">
                <div class="border-t-4 border-gray-600 grow">
                  <h4 class="text-xs">DSWD Field Office Caraga, R. Palma St. Butuan City, Philippines 8600</h4>
                  <h4 class="text-xs">Website: <span class="text-blue-700 underline">http://www.dswd.gov.ph</span> Tel Nos.: (085) 342-5619 to 20  Telefax: (085) 815-9173</h4>
                </div>
                <img src="/storage/assets/iso-logo.jpg" class=" w-24 h-auto"/>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
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
      return (_.uniqBy(items, 'rp_id')).map(function({rp_id, area_rp_id, stat, total_count, sex, is_internal, position, lname, fname, mname, ext_name}){
        return { rp_id, area_rp_id, stat, total_count, sex, is_internal, position, full_name: `${lname}, ${fname}${mname ? ` ${mname[0]}.` : ''} ${ext_name}` }
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
        title: 'Total Rating', order: '*', rating: (_.mean(items.map(x=> parseFloat(x.rating)))).toFixed(2), class: 'text-right font-bold'
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
      if(v <= 5.00) return 'Outstanding'
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

<style>
@media print {
  body * {
    visibility: hidden;
  }
  #section-to-print, #section-to-print * {
    visibility: visible;
  }
  #section-to-print {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>