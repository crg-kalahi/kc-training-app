<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

const formEvent = useForm({
    title: '',
    venue: '',
    date_from: '',
    date_to: '',
})

const submit = () => {
    formEvent.post(route('training.store'), {
        onFinish: () => formEvent.reset(),
        onError: () => {
            // formEvent.setError({
            //     title: ''
            // })
        }
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <!-- Page title & actions -->
    <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div class="min-w-0 flex-1">
            <!-- <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1> -->
            <div class="text-lg font-medium breadcrumbs">
                <ul>
                    <li><Link :href="route('training.index')">Training</Link></li>
                    <li>Create Event</li>
                </ul>
            </div>
        </div>
    </div>

    <form class="space-y-8 divide-y divide-gray-200 px-8 py-4" @submit.prevent="submit">
        <div class="space-y-8 divide-y divide-gray-200">
            <div>
                <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">General Information</h3>
                <p class="mt-1 text-sm text-gray-500">This information will be displayed publicly to other users.</p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-2">
                        <label for="event-date-from" class="block text-sm font-medium leading-6 text-gray-900">Date Start</label>
                        <div class="mt-2">
                        <input v-model="formEvent.date_from" @change="formEvent.date_to = ''" type="date" name="event-date-from" id="event-date-from" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.date_from">{{ formEvent.errors.date_from }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="event-date-to" class="block text-sm font-medium leading-6 text-gray-900">Date End</label>
                        <div class="mt-2">
                        <input v-model="formEvent.date_to" :readonly="formEvent.date_from ? false : true" :min="formEvent.date_from" type="date" name="event-date-to" id="event-date-to" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.date_to">{{ formEvent.errors.date_to }}</p>
                    </div>
                    
                    <div class="sm:col-span-6">
                        <label for="event-title" class="block text-sm font-medium leading-6 text-gray-900">Title of the Event</label>
                        <div class="mt-2">
                        <input v-model="formEvent.title" type="text" name="event-title" id="event-title" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.title">{{ formEvent.errors.title }}</p>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="event-venue" class="block text-sm font-medium leading-6 text-gray-900">Venue / Area</label>
                        <div class="mt-2">
                            <textarea v-model="formEvent.venue" id="event-venue" name="event-venue" rows="3" class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
                        </div>
                        <p class="mt-2 text-sm text-red-500" v-if="formEvent.errors.venue">{{ formEvent.errors.venue }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-5">
        <div class="flex justify-end">
            <Link :href="route('training.index')" type="button" class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</Link>
            <button type="submit" class="ml-3 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
        </div>
    </form>
    </BreezeAuthenticatedLayout>
</template>
