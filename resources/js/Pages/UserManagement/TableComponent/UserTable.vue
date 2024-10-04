<template>
  <div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">List of Users</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('fname')">
              First Name
              <span class="ml-2">
                <i :class="getSortIcon('fname')"></i>
              </span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('lname')">
              Last Name
              <span class="ml-2">
                <i :class="getSortIcon('lname')"></i>
              </span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortTable('permissions')">
              Permissions
              <span class="ml-2">
                <i :class="getSortIcon('permissions')"></i>
              </span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
              Actions
            </th>
          </tr>
          <tr class="bg-gray-50">
            <td class="px-6 py-2">
              <input v-model="filters.fname" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by First Name" />
            </td>
            <td class="px-6 py-2">
              <input v-model="filters.lname" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by Last Name" />
            </td>
            <td class="px-6 py-2">
              <input v-model="filters.permissions" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Filter by Permissions" />
            </td>
            <td class="px-6 py-2"></td>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200">
            <td class="px-6 py-4 whitespace-nowrap">{{ user.fname }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.lname }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
    v-for="permission in user.permissions" 
    :key="permission" 
    class="block bg-gray-200 text-gray-800 text-sm font-medium px-2.5 py-0.5 rounded-full mb-2 last:mb-0"
  >
    {{ permission }}
  </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
              <button @click="toggleForm(user)" class="bg-blue-500 text-sm text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">Edit</button>
              <!-- <button @click="deactivateUser(user.id)" class="bg-red-500 text-sm text-white px-4 py-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">Deactivate</button> -->
            </td>
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

  <RightPanel v-model="openForm" title="Edit User" subtitle="" @submit="submitForm">
    <div class="p-6">
      <div class="bg-gray-100 p-4 rounded-lg shadow-md mb-4">
        <h2 class="text-lg font-semibold">User Details</h2>
        <p class="mt-2 text-gray-700"><strong>ID:</strong> {{ userData.id }}</p>
        <p class="mt-2 text-gray-700"><strong>First Name:</strong> {{ userData.fname }}</p>
        <p class="mt-2 text-gray-700"><strong>Last Name:</strong> {{ userData.lname }}</p>
      </div>

      <div class="bg-white p-4 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold mb-2">Edit Permissions</h2>
        <label for="permissions" class="block text-gray-700 font-medium mb-1">Select Permission:</label>
        <AutoComplete :suggestions="permissions" @select="handleSelect"/>
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
  permissionsList: Array
});


console.log(props.users);

const users = ref([...props.users]);
const permissions = ref(props.permissionsList);

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
  permission: '',
});

const filters = ref({
  id: '',
  fname: '',
  lname: '',
  permissions: '',
});

const dataForSubmission = useForm({
  id: '',
  permissions: []
});

const filteredUsers = computed(() => {
  let filtered = users.value.filter(user => {
    const matchesId = filters.value.id === '' || user.id.toString().includes(filters.value.id);
    const matchesFname = filters.value.fname === '' || user.fname.toLowerCase().includes(filters.value.fname.toLowerCase());
    const matchesLname = filters.value.lname === '' || user.lname.toLowerCase().includes(filters.value.lname.toLowerCase());
    const matchesPermissions = filters.value.permissions === '' || user.permissions.join(' ').toLowerCase().includes(filters.value.permissions.toLowerCase());
    return matchesId && matchesFname && matchesLname && matchesPermissions;
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

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredUsers.value.slice(start, start + perPage.value);
});

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

const toggleForm = (data) => {
  openForm.value = !openForm.value;
  userData.value = { ...data };
  dataForSubmission.id = data.id;
};

const handleSelect = (selectedTags) => {
  dataForSubmission.permissions = selectedTags;
};

const submitForm = () => {
 
  dataForSubmission.post(route("user-management.permissions"), {
    preserveScroll: true,
    onSuccess: () => {
      const index = users.value.findIndex(user => user.id === dataForSubmission.id);
      users.value[index].permissions = dataForSubmission.permissions;
    }
  });
};
</script>

<style scoped>
/* Custom styles can be added here */
</style>
