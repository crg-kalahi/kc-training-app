<template>
    <div class="flex items-center justify-between bg-white px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
        <a href="#" class="relative inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
        <a href="#" class="relative ml-3 inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
            Showing
            {{ ' ' }}
            <span class="font-medium">{{ from }}</span>
            {{ ' ' }}
            to
            {{ ' ' }}
            <span class="font-medium">{{ to }}</span>
            {{ ' ' }}
            of
            {{ ' ' }}
            <span class="font-medium">{{ total }}</span>
            {{ ' ' }}
            results
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-md " aria-label="Pagination">
            <Link v-for="link, index in links" :href="link.url" aria-current="page"
                :class="[index == links.length-1 ? 'rounded-r-md':'' ,index == 0 ? 'rounded-l-md' : '', link.active ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50','relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600']">
                <div v-if="index == 0 || index == links.length-1">
                    <span class="sr-only">{{ index == 0 ? 'Previous' : 'Next' }}</span>
                    <ChevronLeftIcon v-if="index == 0" class="h-5 w-5" aria-hidden="true" />
                    <ChevronRightIcon v-else class="h-5 w-5" aria-hidden="true" />
                </div>
                <div v-else>
                    {{ link.label }}
                </div>
            </Link>
            </nav>
        </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['links', 'from', 'to', 'total']
    }
</script>
<script setup>
    import { Link } from '@inertiajs/inertia-vue3'
    import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
</script>
