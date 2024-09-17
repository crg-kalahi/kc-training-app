
<script>
import _ from 'lodash';
import moment from 'moment';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { LinkIcon  } from '@heroicons/vue/24/outline';
export default {
  props: ['training'],
  data(){
      return {
          form: useForm({
              id: null,
              lname: "",
              fname: "",
              mname: "",
              ext_name: "",
              email: "",
              training_id: "",
              is_internal: true,
              is_female: false,
              position: "",
              brgy: "",
          }),
      }
  },
  computed:{
    formattedDateRange() {
      const startDate = moment(this.training.date_from).format('MMMM D, YYYY');
      const endDate = moment(this.training.date_to).format('MMMM D, YYYY');
      return `${startDate} - ${endDate}`;
    }
  },
  methods:{
    submitForm(training_id){
        const { id, lname, fname, mname, ext_name, email,
        is_internal, is_female, position } = this.form
        axios({
            method: 'POST',
            url: route('training.participants.register'),
            data: {
                id, lname, fname, mname, ext_name, email, training_id, is_internal, is_female, position
            }
        })
        .then(response => {
            // Handle success
            console.log('Form submitted successfully:', response.data);
            this.message = 'Form submitted successfully!';
            this.error = ''; // Clear previous errors if any

            window.location.href = route('training.participants.registration.sent');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                // Handle validation errors
                this.form.errors = {};
                const errorMessages = error.response.data.errors;
                
                for (const [key, messages] of Object.entries(errorMessages)) {
                    this.form.errors[key] = messages.join(' '); // Combine messages into a single string
                }
                
                this.message = ''; // Clear previous success messages
                console.log('Validation errors:', this.form.errors);
            } else {
                // Handle other errors
                this.message = '';
                this.error = 'An unexpected error occurred.';
                console.error('Error:', error);
            }
        });
    },
    formReset(){
        this.form.reset('id','lname', 'fname', 'mname', 'ext_name', 'email', 'is_internal', 'is_female', 'position')
    },
    copyLink(){
        const url = window.location.href; // Or any specific URL you want to copy
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
            alert('Link copied to clipboard!');
            }).catch((err) => {
            console.error('Failed to copy link: ', err);
            });
        } else {
            // Fallback for browsers that do not support Clipboard API
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            try {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
            } catch (err) {
            console.error('Failed to copy link: ', err);
            }
            document.body.removeChild(textArea);
        }
    }
  }
}
</script>
<template>
    <Head title="Evaluation Report" />
    <div class="px-4 py-10 sm:px-6 lg:px-8 bg-blue-200 min-h-screen">
      <!-- Centering content inside the max-width container -->
      <div id="section-to-print" class="px-4 py-6 mx-auto rounded-md max-w-3xl bg-white shadow-lg">
        <!-- Header Section -->
        <div class="text-center mb-8 border-b border-gray-300 pb-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Participants Registration</h2>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ training.title }}</h1>
            <p class="text-lg text-gray-700 mb-1">{{ formattedDateRange }}</p>
            <p class="text-lg text-gray-700">{{ `@ ${training.venue}` }}</p>
            
            <!-- Copy Link Section -->
            <div class="mt-4 flex items-center justify-center space-x-2">
            <button @click="copyLink"  class="inline-flex items-center rounded-md bg-indigo-600 py-2 px-4 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition duration-150 ease-in-out">
                <LinkIcon class="h-5 w-5 mr-2"/>
                <span>Copy Link</span>
            </button>
            </div>
        </div>
  
        <!-- Form Section -->
        <form>
          <div class="space-y-4">
            <!-- Internal Participant Checkbox -->
            <div class="flex items-start pb-4">
              <div class="flex h-6 items-center">
                <input id="create-item-is-internal" v-model="form.is_internal"
                  name="create-item-is-internal" type="checkbox"
                  class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
              </div>
              <div class="ml-3 text-sm leading-6">
                <label for="create-item-is-internal" class="font-medium text-gray-900">Internal Participant?</label>
              </div>
            </div>
  
            <!-- Input Fields -->
            <div class="space-y-4">
              <!-- Last Name -->
              <div>
                <label for="create-item-lname" class="block text-sm font-medium text-gray-900">Last Name <span class="text-red-600">*</span></label>
                <input v-model="form.lname" type="text" id="create-item-lname"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                <p class="mt-1 text-sm text-red-500" v-if="form.errors.lname">{{ form.errors.lname }}</p>
              </div>
  
              <!-- First Name -->
              <div>
                <label for="create-item-fname" class="block text-sm font-medium text-gray-900">First Name <span class="text-red-600">*</span></label>
                <input v-model="form.fname" type="text" id="create-item-fname"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                <p class="mt-1 text-sm text-red-500" v-if="form.errors.fname">{{ form.errors.fname }}</p>
              </div>
  
              <!-- Middle Name -->
              <div>
                <label for="create-item-mname" class="block text-sm font-medium text-gray-900">Middle Name</label>
                <input v-model="form.mname" type="text" id="create-item-mname"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                <p class="mt-1 text-sm text-red-500" v-if="form.errors.mname">{{ form.errors.mname }}</p>
              </div>
  
              <!-- Extension Name -->
              <div>
                <label for="create-item-ext_name" class="block text-sm font-medium text-gray-900">Extension Name</label>
                <input v-model="form.ext_name" type="text" id="create-item-ext_name" placeholder="Jr., Sr., etc..."
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                <p class="mt-1 text-sm text-red-500" v-if="form.errors.ext_name">{{ form.errors.ext_name }}</p>
              </div>
  
              <!-- Gender -->
              <div>
                <fieldset class="space-y-4">
                  <legend class="block text-sm font-medium text-gray-900">Gender</legend>
                  <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                      <input name="gender" type="radio" @input="form.is_female = false" :checked="!form.is_female" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                      <label class="ml-3 text-sm font-medium text-gray-900">Male</label>
                    </div>
                    <div class="flex items-center">
                      <input name="gender" type="radio" @input="form.is_female = true" :checked="form.is_female" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                      <label class="ml-3 text-sm font-medium text-gray-900">Female</label>
                    </div>
                  </div>
                </fieldset>
              </div>
  
              <!-- Position -->
              <div>
                <label for="create-item-position" class="block text-sm font-medium text-gray-900">Position</label>
                <input v-model="form.position" type="text" id="create-item-position" placeholder="PDO III, PDO II, CDO IV, ETC..."
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                <p class="mt-1 text-sm text-red-500" v-if="form.errors.position">{{ form.errors.position }}</p>
              </div>
  
              <!-- Email -->
              <div>
                <label for="create-item-email" class="block text-sm font-medium text-gray-900">Email<span class="text-red-600">*</span></label>
                <input v-model="form.email" type="email" id="create-item-email" placeholder="Valid & Active email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                <p class="mt-1 text-sm text-red-500" v-if="form.errors.email">{{ form.errors.email }}</p>
              </div>



              <div class="flex justify-start space-x-4 mt-6">
                    <button 
                    @click="formReset" 
                    type="button" 
                    class="inline-flex items-center rounded-md bg-gray-500 py-2 px-4 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out"
                    >
                    Reset Form
                    </button>
                    <button 
                    @click="submitForm(training.id)" 
                    type="button" 
                    class="inline-flex items-center rounded-md bg-indigo-600 py-2 px-4 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition duration-150 ease-in-out"
                    >
                    Register
                    </button>
                </div>
              <!--  -->
            </div>
          </div>
        </form>
        
      </div>
      
    </div>
  </template>
  
  <script setup>
  import { Head } from '@inertiajs/inertia-vue3';
  </script>
  
  <style>
  @media print {
    body * {
      visibility: hidden;
    }
    #section-to-print, #section-to-print * {
      visibility: visible;
    }
    #section-to-print {
      position: absolute;
      left: 0;
      top: 0;
    }
  }
  </style>