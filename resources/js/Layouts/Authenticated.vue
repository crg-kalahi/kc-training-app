<template>
    <div class="min-h-full relative">
      <TransitionRoot as="template" :show="sidebarOpen">
        <Dialog as="div" class="relative z-40 lg:hidden" @close="sidebarOpen = false">
          <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" />
          </TransitionChild>
  
          <div class="fixed inset-0 z-40 flex">
            <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
              <DialogPanel class="relative flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4">
                <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                  <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                      <span class="sr-only">Close sidebar</span>
                      <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                    </button>
                  </div>
                </TransitionChild>
                <div class="flex flex-shrink-0 items-center px-4">
                  <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=purple&shade=500" alt="Your Company" />
                </div>
                <div class="mt-5 h-0 flex-1 overflow-y-auto">
                  <nav class="px-2">
                    <div class="space-y-1">
                      <Link preserve-scroll v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center rounded-md px-2 py-2 text-base font-medium leading-5']" :aria-current="item.current ? 'page' : undefined">
                        <component :is="item.icon" :class="[item.current ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-6 w-6 flex-shrink-0']" aria-hidden="true" />
                        {{ item.name }}
                      </Link>
                    </div>
                    <div class="mt-8">
                      <h3 class="px-3 text-sm font-medium text-gray-500" id="mobile-teams-headline">Teams</h3>
                      <div class="mt-1 space-y-1" role="group" aria-labelledby="mobile-teams-headline">
                        <a v-for="team in teams" :key="team.name" :href="team.href" class="group flex items-center rounded-md px-3 py-2 text-base font-medium leading-5 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                          <span :class="[team.bgColorClass, 'mr-4 h-2.5 w-2.5 rounded-full']" aria-hidden="true" />
                          <span class="truncate">{{ team.name }}</span>
                        </a>
                      </div>
                    </div>
                  </nav>
                </div>
              </DialogPanel>
            </TransitionChild>
            <div class="w-14 flex-shrink-0" aria-hidden="true">
              <!-- Dummy element to force sidebar to shrink to fit close icon -->
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
  
      <!-- Static sidebar for desktop -->
      <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col lg:border-r lg:border-gray-200 lg:bg-purple-800 lg:pt-5 lg:pb-4">
        <!-- <div class="mb-5 flex flex-shrink-0 items-center px-6">
          <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=purple&shade=500" alt="Your Company" />
        </div> -->
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex h-0 flex-1 flex-col overflow-y-auto pt-1">
          <!-- User account dropdown -->
          <Menu as="div" class="relative inline-block px-3 text-left">
            <div>
              <MenuButton class="group w-full rounded-md bg-white px-3.5 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-100">
                <span class="flex w-full items-center justify-between">
                  <span class="flex min-w-0 items-center justify-between space-x-3">
                    <img class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-300" :src="user.avatar" alt="" />
                    <span class="flex min-w-0 flex-1 flex-col">
                      <span class="truncate text-sm font-medium text-gray-900">{{ user.full_name }}</span>
                      <span class="truncate text-sm text-gray-500">{{ user.id_number }}</span>
                    </span>
                  </span>
                  <ChevronUpDownIcon class="h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                </span>
              </MenuButton>
            </div>
            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
              <MenuItems class="absolute right-0 left-0 z-10 mx-3 mt-1 origin-top divide-y divide-gray-200 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div class="py-1">
                  <MenuItem v-slot="{ active }">
                    <a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">View profile</a>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Settings</a>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Notifications</a>
                  </MenuItem>
                </div>
                <div class="py-1">
                  <MenuItem v-slot="{ active }">
                    <a href="#" @click="logoutWasClicked" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Logout</a>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
          <!-- Sidebar Search -->
          <div class="mt-5 px-3">
            <label for="search" class="sr-only">Search</label>
            <div class="relative mt-1 rounded-md shadow-sm">
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3" aria-hidden="true">
                <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" aria-hidden="true" />
              </div>
              <input type="text" name="search" id="search" class="block w-full rounded-md border-0 py-1.5 pl-9 ring-1 ring-inset ring-white placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6" placeholder="Search Training" />
            </div>
          </div>
          <!-- Navigation -->
          <nav class="mt-6 px-3">
            <div class="space-y-1">
              <Link preserve-scroll v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-yellow-400 text-gray-900' : 'text-white hover:bg-yellow-100 hover:text-gray-900', 'group flex items-center rounded-md px-2 py-2 text-sm font-bold tracking-wider']" :aria-current="item.current ? 'page' : undefined">
                <component :is="item.icon" :class="[item.current ? 'text-gray-500' : 'text-white group-hover:text-gray-500', 'mr-3 h-6 w-6 flex-shrink-0']" aria-hidden="true" />
                {{ item.name }}
              </Link>
            </div>
            <div class="mt-8">
              <!-- Secondary navigation -->
              <!-- <h3 class="px-3 text-sm font-medium text-gray-500" id="desktop-teams-headline">My Event Today</h3>
              <div class="mt-1 space-y-1" role="group" aria-labelledby="desktop-teams-headline">
                <a v-for="person in currentEvent" :key="person.name" :href="person.href" class="group flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                  <span :class="[person.bgColorClass, 'mr-4 h-2.5 w-2.5 rounded-full']" aria-hidden="true" />
                  <span class="truncate">{{ person.name }}</span>
                </a>
              </div> -->
            </div>
          </nav>
        </div>
      </div>
      <!-- Main column -->
      <div class="flex flex-col lg:pl-64">
        <!-- Search header -->
        <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 border-b border-gray-200 bg-white lg:hidden">
          <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 lg:hidden" @click="sidebarOpen = true">
            <span class="sr-only">Open sidebar</span>
            <Bars3CenterLeftIcon class="h-6 w-6" aria-hidden="true" />
          </button>
          <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex flex-1">
              <form class="flex w-full md:ml-0" action="#" method="GET">
                <label for="search-field" class="sr-only">Search</label>
                <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
                    <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true" />
                  </div>
                  <input id="search-field" name="search-field" class="block h-full w-full border-transparent py-2 pl-8 pr-3 text-gray-900 focus:border-transparent focus:outline-none focus:ring-0 focus:placeholder:text-gray-400 sm:text-sm" placeholder="Search" type="search" />
                </div>
              </form>
            </div>
            <div class="flex items-center">
              <!-- Profile dropdown -->
              <Menu as="div" class="relative ml-3">
                <div>
                  <MenuButton class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" :src="user.avatar" alt="" />
                  </MenuButton>
                </div>
                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                  <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right divide-y divide-gray-200 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="py-1">
                      <MenuItem v-slot="{ active }">
                        <a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">View profile</a>
                      </MenuItem>
                      <MenuItem v-slot="{ active }">
                        <a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Settings</a>
                      </MenuItem>
                      <MenuItem v-slot="{ active }">
                        <a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Notifications</a>
                      </MenuItem>
                    </div>
                    
                    <div class="py-1">
                      <MenuItem v-slot="{ active }">
                        <button @click="logoutWasClicked" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Logout</button>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
        </div>
        <main class="flex-1">
            <slot></slot>
        </main>
      </div>
    </div>
</template>
  
<script setup>
  import { ref } from 'vue'
  import { Link } from '@inertiajs/inertia-vue3'
  import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
  } from '@headlessui/vue'
  import { Bars3CenterLeftIcon, Bars4Icon, Cog6ToothIcon, HomeIcon, XMarkIcon } from '@heroicons/vue/24/outline'
  import { ChevronUpDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
  const currentEvent = [
    { name: 'Crisis Communication Workshop - Batch 1', href: '#', bgColorClass: 'bg-green-500' },
    { name: 'Crisis Communication Workshop - Batch 2', href: '#', bgColorClass: 'bg-green-500' },
    { name: 'Crisis Communication Workshop - Batch 3', href: '#', bgColorClass: 'bg-green-500' },
  ]
  const sidebarOpen = ref(false)

</script>
<script>
  export default{
      computed:{
          user(){
              return this.$page.props.auth.user
          },
          navigation(){
            const page = this.$page.url
            return [
              { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: page.startsWith('/dashboard') },
              { name: 'Trainings', href: route('training.index'), icon: Bars4Icon, current: page.startsWith('/training') },
              { name: 'Configurations', href: route('conf.index'), icon: Cog6ToothIcon, current: page.startsWith('/configuration') },
            ]
          }
      },
      methods:{
        logoutWasClicked(){
          this.$inertia.visit(route('logout'), {
            method: 'post'
          })
        }
      }
  }
</script>