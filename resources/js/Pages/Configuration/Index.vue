<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';

import {
  AcademicCapIcon,
  BanknotesIcon,
  BuildingOfficeIcon,
  CheckBadgeIcon,
  ClockIcon,
  ReceiptRefundIcon,
  UsersIcon,
} from '@heroicons/vue/24/outline'

const actions = [
  {
    title: 'Key Area: Training',
    href: route('conf.key-training'),
    icon: ClockIcon,
    iconForeground: 'text-teal-700',
    iconBackground: 'bg-teal-50',
		text: "A configuration for the training's evaluations questionare"
  },
  {
    title: 'Key Area: Resource Person',
    href: route('conf.key-resource-person'),
    icon: UsersIcon,
    iconForeground: 'text-purple-700',
    iconBackground: 'bg-purple-50',
		text: 'Training evaluation questionare for Resource Person'
  },
  {
    title: 'Key Area: Learning',
    href: route('conf.key-learning'),
    icon: AcademicCapIcon,
    iconForeground: 'text-blue-700',
    iconBackground: 'bg-blue-50',
		text: "Lists of question for each participant's evaluation"
  },
  {
    title: 'Lists: Office Representative',
    href: route('conf.lists-office-representative'),
    icon: BuildingOfficeIcon,
    iconForeground: 'text-yellow-700',
    iconBackground: 'bg-yellow-50',
		text: 'List of the office of representative'
  },
]
</script>

<template>
    <Head title="Configuration"/>
    <BreezeAuthenticatedLayout>
      <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
          <div class="min-w-0 flex-1">
              <!-- <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">Trainings</h1> -->
              <div class="text-xs lg:text-lg font-medium breadcrumbs">
                  <ul>
                      <li><Link :href="route('conf.index')">Configuration</Link></li>
                      <!-- <li>{{ title }}</li> -->
                  </ul>
              </div>
          </div>
      </div>
		<div class="p-3">
			<div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-gray-200 shadow sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0">
				<div v-for="(action, actionIdx) in actions" :key="action.title" :class="[actionIdx === 0 ? 'rounded-tl-lg rounded-tr-lg sm:rounded-tr-none' : '', actionIdx === 1 ? 'sm:rounded-tr-lg' : '', actionIdx === actions.length - 2 ? 'sm:rounded-bl-lg' : '', actionIdx === actions.length - 1 ? 'rounded-bl-lg rounded-br-lg sm:rounded-bl-none' : '', 'group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500']">
					<div>
						<span :class="[action.iconBackground, action.iconForeground, 'inline-flex rounded-lg p-3 ring-4 ring-white']">
							<component :is="action.icon" class="h-6 w-6" aria-hidden="true" />
						</span>
					</div>
					<div class="mt-8">
						<h3 class="text-base font-semibold leading-6 text-gray-900">
							<Link :href="action.href" class="focus:outline-none">
								<!-- Extend touch target to entire panel -->
								<span class="absolute inset-0" aria-hidden="true" />
								{{ action.title }}
							</Link>
						</h3>
						<p class="mt-2 text-sm text-gray-500">{{ action.text }}</p>
					</div>
					<span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
						<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
						</svg>
					</span>
				</div>
			</div>
		</div>
    </BreezeAuthenticatedLayout>
</template>