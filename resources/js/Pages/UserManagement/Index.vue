<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>

<script>
import UserTable from './TableComponent/UserTable.vue';

export default {
    props: ['users', 'roles', 'canEditUsername'],
    components: { UserTable },
    data() {
        return {
            usersData: this.$props.users,
            rolesList: this.$props.roles,
        };
    },
    watch: {
        users: {
            handler(val) {
                this.usersData = val;
            },
            deep: true,
        },
        roles: {
            handler(val) {
                this.rolesList = val;
            },
            deep: true,
        },
    },
};
</script>

<template>
    <Head title="User management" />

    <BreezeAuthenticatedLayout>
        <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">User management</h1>
                <p class="mt-1 text-sm text-gray-500">Add users, edit details, and roles.</p>
            </div>
        </div>
        <div class="mt-2 p-4">
            <UserTable :users="usersData" :roles-list="rolesList" :can-edit-username="canEditUsername" />
        </div>
    </BreezeAuthenticatedLayout>
</template>
