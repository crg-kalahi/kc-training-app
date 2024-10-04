<template>
    <div class="p-6 bg-white rounded-lg shadow-md">
      <h2 class="text-xl font-bold mb-4">My Trainings</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                Actions
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('fname')">
                Title
                <span class="ml-2">
                  <i :class="getSortIcon('fname')"></i>
                </span>
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('lname')">
                Venue
                <span class="ml-2">
                  <i :class="getSortIcon('lname')"></i>
                </span>
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('permissions')">
                Date From
                <span class="ml-2">
                  <i :class="getSortIcon('permissions')"></i>
                </span>
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('permissions')">
                Date To
                <span class="ml-2">
                  <i :class="getSortIcon('permissions')"></i>
                </span>
              </th>
             
            </tr>
            <tr class="bg-gray-50">
              <td class="px-6 py-2"></td>
              <td class="px-6 py-2">
                <input v-model="filters.title" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by Title" />
              </td>
              <td class="px-6 py-2">
                <input v-model="filters.venue" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by Venue" />
              </td>

              <td class="px-6 py-2">
                <input v-model="filters.date_from" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by Date From" />
              </td>

              <td class="px-6 py-2">
                <input v-model="filters.date_to" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by Date To" />
              </td>
              
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="t in paginatedTrainings" :key="t.id" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                <button  @click="requestCertificate(t)" 
                :disabled="t.is_request_not_allowed"
                :class="[
                  t.is_request_not_allowed ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500', 
                  'text-sm text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200'
                ]"
                >{{ t.is_request_not_allowed ? 'Waiting Response' : 'Request Certificate' }}</button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ t.training.title }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ t.training.venue }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ t.training.date_from }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ t.training.date_to }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div class="mt-4 flex justify-between items-center">
        <button @click="previousPage" :disabled="currentPage === 1" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 disabled:bg-gray-200">
          Previous
        </button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 disabled:bg-gray-200">
          Next
        </button>
      </div>
    </div>
  
  </template>
  
  <script setup>
  import { ref, computed, watch, defineProps } from 'vue';
  import { ChevronUpIcon, ChevronDownIcon, TrophyIcon } from '@heroicons/vue/20/solid';
  import { useForm } from '@inertiajs/inertia-vue3';
  import { Inertia } from '@inertiajs/inertia';
  
  const props = defineProps({
    training: Array,
  });
  const trainings = ref([...props.training]);

  // watch(() => props.training, (newTrainings) => {
  //   training.value = [...newTrainings];
  // }, { deep: true });
  
  const currentPage = ref(1);
  const perPage = ref(5);
  const totalPages = computed(() => Math.ceil(filteredTrainings.value.length / perPage.value));
  
  const sortBy = ref('');
  const sortDirection = ref('');
  const openForm = ref(false);
  const trainingData = ref({
    title: '',
    venue: '',
    date_from: '',
    date_to: '',
  });
  
  const filters = ref({
    title: '',
    venue: '',
    date_from: '',
    date_to: '',
  });

  const formData = useForm({
    'training_id': '',
    'training_participant_id': ''
  })
  
  const filteredTrainings = computed(() => {

    let filtered = trainings.value.filter(t => {
      const matchesTitle = filters.value.title === '' || t.training.title.includes(filters.value.title);
      const matchesVenue = filters.value.venue === '' || t.training.venue.includes(filters.value.venue);
      const matchesDateFrom = filters.value.date_from === '' || t.training.date_from.includes(filters.value.date_from);
      const matchesDateTo = filters.value.date_to === '' ||  t.training.date_to.includes(filters.value.date_to);
      return matchesTitle && matchesVenue && matchesDateFrom && matchesDateTo;
    });
  
    if (sortBy.value) {
      filtered.sort((a, b) => {
        let result = 0;
        if (a[sortBy.value] < b[sortBy.value]) {
          result = -1;
        } else if (a[sortBy.value] > b[sortBy.value]) {
          result = 1;
        }
        return sortDirection.value === 'asc' ? result : -result;
      });
    }
  
    return filtered;
  });
  
  const paginatedTrainings = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    
    return filteredTrainings.value.slice(start, start + perPage.value);
  });


  console.log(paginatedTrainings);

  const nextPage = () => {
    if (currentPage.value < totalPages.value) {
      currentPage.value++;
    }
  };
  
  const previousPage = () => {
    if (currentPage.value > 1) {
      currentPage.value--;
    }
  };
  
  const sortTable = (field) => {
    if (sortBy.value === field) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortBy.value = field;
      sortDirection.value = 'asc';
    }
  };
  
  const getSortIcon = (field) => {
    if (sortBy.value === field) {
      return sortDirection.value === 'asc' ? ChevronUpIcon : ChevronDownIcon;
    }
    return 'pi pi-sort';
  };

  const requestCertificate = (trainingParticipants) => {
    formData.training_id = trainingParticipants.training_id;
    formData.training_participant_id = trainingParticipants.id;
    const _c = confirm("Confirm Request?");
    if(_c){
      formData.post(route('training.store-certificate-request'), {
        onSuccess: () => {
          // Optionally reload or redirect after success

          // Inertia.visit()
          Inertia.visit(window.location.pathname, { 
            preserveState: true // Optional: keep component state
          });
          //location.reload(); // or Inertia.visit() if using Inertia.js
        },
        onError: (errors) => {
          // Handle validation or server errors
          console.error('Errors:', errors);
        }
      });
    }
    
  }
  

  </script>
  
  <style scoped>
  /* Custom styles can be added here */
  </style>
  