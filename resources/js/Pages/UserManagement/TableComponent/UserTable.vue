<template>
  <div class="p-8 bg-white rounded-2xl shadow-lg border border-gray-100">
    <div
      v-if="deleteError"
      class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
      role="alert"
    >
      {{ deleteError }}
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b pb-3">
      <h2 class="text-2xl font-semibold text-gray-800">Users</h2>
      <button
        type="button"
        class="inline-flex justify-center items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        @click="openCreate"
      >
        Add user
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-gray-700">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer hover:text-indigo-600" @click="sortTable('fname')">
              First name <span class="ml-1"><i :class="getSortIcon('fname')"></i></span>
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer hover:text-indigo-600" @click="sortTable('lname')">
              Last name <span class="ml-1"><i :class="getSortIcon('lname')"></i></span>
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
              Email (login)
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer hover:text-indigo-600" @click="sortTable('roles')">
              Roles <span class="ml-1"><i :class="getSortIcon('roles')"></i></span>
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
          </tr>
          <tr class="bg-white">
            <td class="px-4 py-2">
              <input v-model="filters.fname" class="w-full min-w-[8rem] px-2 py-2 border rounded-lg text-sm" placeholder="First name" />
            </td>
            <td class="px-4 py-2">
              <input v-model="filters.lname" class="w-full min-w-[8rem] px-2 py-2 border rounded-lg text-sm" placeholder="Last name" />
            </td>
            <td class="px-4 py-2">
              <input v-model="filters.login" class="w-full min-w-[10rem] px-2 py-2 border rounded-lg text-sm" placeholder="Email or username" />
            </td>
            <td class="px-4 py-2">
              <input v-model="filters.roles" class="w-full min-w-[8rem] px-2 py-2 border rounded-lg text-sm" placeholder="Roles" />
            </td>
            <td class="px-4 py-2"></td>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 whitespace-nowrap text-gray-800 text-sm">{{ user.fname }}</td>
            <td class="px-4 py-3 whitespace-nowrap text-gray-800 text-sm">{{ user.lname }}</td>
            <td class="px-4 py-3 whitespace-nowrap text-gray-700 text-sm">{{ user.email || user.username || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span
                v-for="role in user.roles"
                :key="role"
                class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-0.5 rounded-full mr-1 mb-1"
              >
                {{ role }}
              </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <div class="flex flex-wrap items-center gap-2">
                <button
                  type="button"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-3 py-1.5 rounded-md shadow"
                  @click="openEdit(user)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  :disabled="user.id === currentUserId"
                  :title="user.id === currentUserId ? 'You cannot delete your own account' : ''"
                  class="bg-white text-red-700 ring-1 ring-red-200 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50 text-sm font-medium px-3 py-1.5 rounded-md shadow-sm"
                  @click="confirmDelete(user)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-6 flex justify-between items-center">
      <button
        type="button"
        class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200 disabled:opacity-50"
        :disabled="currentPage === 1"
        @click="previousPage"
      >
        Previous
      </button>
      <span class="text-sm text-gray-700">Page {{ currentPage }} of {{ totalPages || 1 }}</span>
      <button
        type="button"
        class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200 disabled:opacity-50"
        :disabled="currentPage === totalPages || totalPages === 0"
        @click="nextPage"
      >
        Next
      </button>
    </div>
  </div>

  <RightPanel v-model="openForm" :title="panelTitle" subtitle="" @submit="submitForm">
    <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">First name</label>
          <input v-model="form.fname" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Last name</label>
          <input v-model="form.lname" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Middle name</label>
          <input v-model="form.mname" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Extension name</label>
          <input v-model="form.ext_name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Jr., III" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">ID number</label>
          <input v-model="form.id_number" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" autocomplete="email" />
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
        <div v-if="canEditUsername" class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Username (login)</label>
          <input
            v-model="form.username"
            type="text"
            class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm"
            autocomplete="username"
            :placeholder="isCreate ? 'Leave blank to use email as login name' : ''"
          />
          <p class="mt-1 text-xs text-gray-500">Used to sign in. Only staff administrators can change this.</p>
          <p v-if="form.errors.username" class="mt-1 text-sm text-red-600">{{ form.errors.username }}</p>
        </div>
        <div v-else class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Username (login)</label>
          <input :value="form.username || form.email" type="text" readonly class="mt-1 w-full cursor-not-allowed rounded-md border-gray-200 bg-gray-50 text-sm text-gray-600" />
          <p class="mt-1 text-xs text-gray-500">Matches email. Contact a staff administrator to change the login username.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Division</label>
          <input v-model="form.division" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Section</label>
          <input v-model="form.section" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Mobile no.</label>
          <input v-model="form.mobile_no" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" />
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700">{{ isCreate ? 'Password' : 'New password (optional)' }}</label>
          <input
            v-model="form.password"
            type="password"
            class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm"
            autocomplete="new-password"
            :placeholder="isCreate ? 'Minimum 8 characters' : 'Leave blank to keep current'"
          />
          <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
        </div>
      </div>

      <div class="border-t pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
        <AutoComplete :suggestions="roles" :initial-selected="form.roles" @select="handleSelect" />
        <p v-if="form.errors.roles" class="mt-1 text-sm text-red-600">{{ form.errors.roles }}</p>
      </div>
    </div>
  </RightPanel>
</template>

<script setup>
import { ref, computed, watch, defineProps } from 'vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/20/solid';
import RightPanel from '@/Components/RightPanel.vue';
import AutoComplete from '@/Components/AutoComplete.vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  users: { type: Array, default: () => [] },
  rolesList: { type: Array, default: () => [] },
  canEditUsername: { type: Boolean, default: false },
});

const page = usePage();
const currentUserId = computed(() => page.props.value?.auth?.user?.id ?? null);
const deleteError = computed(() => {
  const raw = page.props.value?.errors?.delete;
  if (raw == null || raw === '') return '';
  return Array.isArray(raw) ? raw[0] : String(raw);
});

const users = ref([...props.users]);
const roles = ref(props.rolesList);

watch(
  () => props.users,
  (newUsers) => {
    users.value = [...newUsers];
  },
  { deep: true }
);

const currentPage = ref(1);
const perPage = ref(8);
const sortBy = ref('');
const sortDirection = ref('');
const openForm = ref(false);
const isCreate = ref(false);

const form = useForm({
  id: null,
  fname: '',
  lname: '',
  mname: '',
  ext_name: '',
  id_number: '',
  email: '',
  username: '',
  division: '',
  section: '',
  mobile_no: '',
  password: '',
  roles: [],
});

const filters = ref({
  fname: '',
  lname: '',
  login: '',
  roles: '',
});

const panelTitle = computed(() => (isCreate.value ? 'Add user' : 'Edit user'));

const filteredUsers = computed(() => {
  let filtered = users.value.filter((user) => {
    const matchesFname = !filters.value.fname || (user.fname && user.fname.toLowerCase().includes(filters.value.fname.toLowerCase()));
    const matchesLname = !filters.value.lname || (user.lname && user.lname.toLowerCase().includes(filters.value.lname.toLowerCase()));
    const loginHay = `${user.email || ''} ${user.username || ''}`.toLowerCase();
    const matchesLogin =
      !filters.value.login || loginHay.includes(filters.value.login.toLowerCase());
    const matchesRoles =
      !filters.value.roles ||
      (user.roles && user.roles.join(' ').toLowerCase().includes(filters.value.roles.toLowerCase()));
    return matchesFname && matchesLname && matchesLogin && matchesRoles;
  });

  if (sortBy.value) {
    filtered = [...filtered].sort((a, b) => {
      let av;
      let bv;
      if (sortBy.value === 'roles') {
        av = (a.roles || []).join(',');
        bv = (b.roles || []).join(',');
      } else {
        av = a[sortBy.value] ?? '';
        bv = b[sortBy.value] ?? '';
      }
      if (av < bv) return sortDirection.value === 'asc' ? -1 : 1;
      if (av > bv) return sortDirection.value === 'asc' ? 1 : -1;
      return 0;
    });
  }

  return filtered;
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredUsers.value.length / perPage.value)));

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredUsers.value.slice(start, start + perPage.value);
});

watch(filteredUsers, () => {
  if (currentPage.value > totalPages.value) currentPage.value = totalPages.value;
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

function resetForm() {
  form.reset();
  form.clearErrors();
  form.id = null;
  form.roles = [];
  form.password = '';
}

function openCreate() {
  isCreate.value = true;
  resetForm();
  form.roles = props.rolesList.includes('guest') ? ['guest'] : props.rolesList.length ? [props.rolesList[0]] : [];
  openForm.value = true;
}

function openEdit(user) {
  isCreate.value = false;
  form.clearErrors();
  form.id = user.id;
  form.fname = user.fname || '';
  form.lname = user.lname || '';
  form.mname = user.mname || '';
  form.ext_name = user.ext_name || '';
  form.id_number = user.id_number || '';
  form.email = user.email || '';
  form.username = user.username || '';
  form.division = user.division || '';
  form.section = user.section || '';
  form.mobile_no = user.mobile_no || '';
  form.password = '';
  form.roles = [...(user.roles || [])];
  openForm.value = true;
}

const handleSelect = (selectedTags) => {
  form.roles = selectedTags;
};

function closePanel() {
  openForm.value = false;
}

function confirmDelete(user) {
  if (user.id === currentUserId.value) {
    return;
  }
  const label = [user.fname, user.lname].filter(Boolean).join(' ').trim() || user.email || user.username || 'this user';
  const message =
    `Remove this user from the system?\n\n` +
    `${label}\n\n` +
    `This is a soft delete: they will not be able to sign in. ` +
    `To fully remove the record or reuse email/username, a database restore or admin action may be needed.`;
  if (!window.confirm(message)) {
    return;
  }
  Inertia.delete(route('user-management.destroy', user.id), {
    preserveScroll: true,
  });
}

const submitForm = () => {
  const normalizeEmail = (data) => {
    const out = { ...data };
    out.email = String(out.email || '').trim().toLowerCase();
    return out;
  };

  const applyUsername = (out) => {
    if (props.canEditUsername) {
      const u = String(out.username || '').trim().toLowerCase();
      if (isCreate.value && !u) {
        out.username = out.email;
      } else {
        out.username = u;
      }
    } else {
      out.username = out.email;
    }
    return out;
  };

  if (isCreate.value) {
    form
      .transform((data) => {
        const out = normalizeEmail(data);
        return applyUsername(out);
      })
      .post(route('user-management.store'), {
        preserveScroll: true,
        onSuccess: () => {
          form.transform((d) => d);
          closePanel();
        },
      });
    return;
  }

  form
    .transform((data) => {
      const out = normalizeEmail(data);
      applyUsername(out);
      delete out.id;
      if (!out.password) delete out.password;
      return out;
    })
    .put(route('user-management.update', form.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.transform((d) => d);
        closePanel();
      },
    });
};
</script>
