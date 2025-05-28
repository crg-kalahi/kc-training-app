<template>
  <div class="p-8 bg-white rounded-2xl shadow-lg border border-gray-100">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">List of Users</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-gray-700">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider hover:text-indigo-600 transition-colors duration-150 cursor-pointer" @click="sortTable('fname')">
              First Name
              <span class="ml-1">
                <i :class="getSortIcon('fname')"></i>
              </span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider hover:text-indigo-600 transition-colors duration-150 cursor-pointer" @click="sortTable('lname')">
              Last Name
              <span class="ml-1">
                <i :class="getSortIcon('lname')"></i>
              </span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider hover:text-indigo-600 transition-colors duration-150 cursor-pointer" @click="sortTable('roles')">
              Roles
              <span class="ml-1">
                <i :class="getSortIcon('roles')"></i>
              </span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
          </tr>
          <tr class="bg-white">
            <td class="px-6 py-2">
              <input v-model="filters.fname" class="w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Filter by First Name" />
            </td>
            <td class="px-6 py-2">
              <input v-model="filters.lname" class="w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Filter by Last Name" />
            </td>
            <td class="px-6 py-2">
              <input v-model="filters.roles" class="w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Filter by Roles" />
            </td>
            <td class="px-6 py-2"></td>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200">
            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ user.fname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ user.lname }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                v-for="role in user.roles" 
                :key="role" 
                class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full mr-1 mb-1"
              >
                {{ role }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
              <button @click="toggleForm(user)" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-between items-center">
      <button @click="previousPage" :disabled="currentPage === 1"
        class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed shadow">
        Previous
      </button>
      <span class="text-sm text-gray-700 font-medium">Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages"
        class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed shadow">
        Next
      </button>
    </div>
  </div>

  <RightPanel v-model="openForm" title="Edit User" subtitle="" @submit="submitForm">
    <div class="p-6 space-y-6">
      <div class="bg-gray-100 p-5 rounded-xl shadow-inner">
        <h2 class="text-lg font-semibold text-gray-800">User Details</h2>
        <p class="mt-2 text-gray-600"><strong>ID:</strong> {{ userData.id }}</p>
        <p class="mt-2 text-gray-600"><strong>First Name:</strong> {{ userData.fname }}</p>
        <p class="mt-2 text-gray-600"><strong>Last Name:</strong> {{ userData.lname }}</p>
      </div>

      <div class="bg-white p-5 rounded-xl shadow">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Edit Roles</h2>
        <label for="roles" class="block text-sm font-medium text-gray-700 mb-2">Select Role:</label>
        <AutoComplete :suggestions="roles" @select="handleSelect" />
      </div>
    </div>
  </RightPanel>
</template>

<script setup>
import { ref, computed, watch, defineProps } from 'vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/20/solid';
import RightPanel from '@/Components/RightPanel.vue';
import AutoComplete from '@/Components/AutoComplete.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  users: Array,
  rolesList: Array
});

const users = ref([...props.users]);
const roles = ref(props.rolesList);

watch(() => props.users, (newUsers) => {
  users.value = [...newUsers];
}, { deep: true });

const currentPage = ref(1);
const perPage = ref(5);
const totalPages = computed(() => Math.ceil(filteredUsers.value.length / perPage.value));

const sortBy = ref('');
const sortDirection = ref('');
const openForm = ref(false);
const userData = ref({
  fname: '',
  lname: '',
  id: '',
  role: '',
});

const filters = ref({
  id: '',
  fname: '',
  lname: '',
  roles: '',
});

const dataForSubmission = useForm({
  id: '',
  roles: []
});

const filteredUsers = computed(() => {
  let filtered = users.value.filter(user => {
    const matchesId = filters.value.id === '' || user.id.toString().includes(filters.value.id);
    const matchesFname = filters.value.fname === '' || user.fname.toLowerCase().includes(filters.value.fname.toLowerCase());
    const matchesLname = filters.value.lname === '' || user.lname.toLowerCase().includes(filters.value.lname.toLowerCase());
    const matchesRoles = filters.value.roles === '' || user.roles.join(' ').toLowerCase().includes(filters.value.roles.toLowerCase());
    return matchesId && matchesFname && matchesLname && matchesRoles;
  });

  if (sortBy.value) {
    filtered.sort((a, b) => {
      let result = 0;
      if (a[sortBy.value] < b[sortBy.value]) result = -1;
      else if (a[sortBy.value] > b[sortBy.value]) result = 1;
      return sortDirection.value === 'asc' ? result : -result;
    });
  }

  return filtered;
});

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredUsers.value.slice(start, start + perPage.value);
});

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const previousPage = () => {
  if (currentPage.value > 1) currentPage.value--;
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

const toggleForm = (data) => {
  openForm.value = !openForm.value;
  userData.value = { ...data };
  dataForSubmission.id = data.id;
};

const handleSelect = (selectedTags) => {
  dataForSubmission.roles = selectedTags;
};

const submitForm = () => {
  dataForSubmission.post(route("user-management.roles"), {
    preserveScroll: true,
    onSuccess: () => {
      const index = users.value.findIndex(user => user.id === dataForSubmission.id);
      users.value[index].roles = dataForSubmission.roles;
    }
  });
};
</script>

<style scoped>
/* You may add custom animations or override Tailwind here if needed */
</style>
