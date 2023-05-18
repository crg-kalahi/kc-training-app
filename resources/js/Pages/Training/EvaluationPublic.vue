<template>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- We've used 3xl here, but feel free to try other max-widths based on your needs -->
    <div class="mx-auto max-w-3xl pt-8">
      <div class="sm:flex-auto text-center">
        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ `${training.title}` }}</h1>
        <p class="mt-2 text-sm text-gray-700">{{ `${trainingDate} @ ${training.venue}` }}</p>
      </div>
    </div>
  </div>
</template>
<script setup>
import moment from 'moment';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
</script>
<script>
export default {
  props: ["training", "officeRep", "keyTraining", "keyRP", "keyLearning", "resourcePerson"], 
  data(){
    return{
      form: useForm({
          key_training: [],
          key_RP: [],
          key_learning: [],
          sex: '0',
          training_id: null,
          office_rep: 1,
      }),
    }
  },
  computed:{
    trainingDate() {
        const { date_from, date_to } = this.training;
        return `${moment(date_from, "YYYY-MM-DD").format("MMM DD, YYYY")} - ${moment(date_to, "YYYY-MM-DD").format("MMM DD, YYYY")}`;
    },
  }
}
</script>