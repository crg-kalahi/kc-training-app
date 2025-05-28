<template>
  <div class="p-4 bg-white rounded-md shadow-lg text-sm">
    <h2 class="text-lg font-semibold mb-3">My Trainings</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            
            <th class="p-2 text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('fname')">
              Title <i :class="getSortIcon('fname')" class="ml-1"></i>
            </th>
            <th class="p-2 text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('lname')">
              Venue <i :class="getSortIcon('lname')" class="ml-1"></i>
            </th>
            <th class="p-2 text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('permissions')">
              Date From <i :class="getSortIcon('permissions')" class="ml-1"></i>
            </th>
            <th class="p-2 text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('permissions')">
              Date To <i :class="getSortIcon('permissions')" class="ml-1"></i>
            </th>
          </tr>
          <tr class="bg-gray-100">
            <td class="p-2">
              <input v-model="filters.title" class="w-full p-1 border border-gray-300 rounded focus:ring-indigo-400 focus:outline-none" placeholder="Filter Title" />
            </td>
            <td class="p-2">
              <input v-model="filters.venue" class="w-full p-1 border border-gray-300 rounded focus:ring-indigo-400 focus:outline-none" placeholder="Filter Venue" />
            </td>
            <td class="p-2">
              <input v-model="filters.date_from" class="w-full p-1 border border-gray-300 rounded focus:ring-indigo-400 focus:outline-none" placeholder="Filter Date From" />
            </td>
            <td class="p-2">
              <input v-model="filters.date_to" class="w-full p-1 border border-gray-300 rounded focus:ring-indigo-400 focus:outline-none" placeholder="Filter Date To" />
            </td>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="t in paginatedTrainings" :key="t.id" class="hover:bg-gray-50">
            <td class="p-2 whitespace-nowrap">{{ t.training.title }}</td>
            <td class="p-2 whitespace-nowrap">{{ t.training.venue }}</td>
            <td class="p-2 whitespace-nowrap">{{ t.training.date_from }}</td>
            <td class="p-2 whitespace-nowrap">{{ t.training.date_to }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3 flex justify-between items-center text-sm">
      <button @click="previousPage" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:bg-gray-200">
        Previous
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:bg-gray-200">
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
  