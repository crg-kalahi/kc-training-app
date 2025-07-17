<template>
  <div class="mx-auto max-w-7xl relative">
    <div class="mx-auto max-w-3xl pt-8 px-3">
      <div class="sm:flex-auto text-center">
        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ `${training.title}` }}</h1>
        <p class="mt-2 text-sm text-gray-700">{{ `${trainingDate} @ ${training.venue}` }}</p>
      </div>
    </div>

    <div class="px-3 pb-10 pt-3 mt-5 bg-yellow-100">
      <div class="grid grid-cols-1 gap-y-2 pb-5">
        <div>
          <label for="office-rep" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Office Representative</label>
          <select v-model="form.office_rep" id="office-rep" name="office-rep" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option v-for="item in officeRep" :key="item.title" :value="item.id">{{ item.title }}</option>
          </select>
        </div>
        <div>
          <label for="Sex" class="pl-1 block text-sm font-bold leading-6 text-gray-900">Sex</label>
          <select v-model="form.sex" id="Sex" name="Sex" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option :value="'0'">Prefer not to say</option>
            <option :value="'male'">Male</option>
            <option :value="'female'">Female</option>
          </select>
        </div>
      </div>

      <div class="pt-5 mb-3 border-t-2 border-dotted border-gray-500">
        <section>
          <h3 class="font-bold">Training Evaluation</h3>
          <div class="grid grid-cols-5 rounded-md">
            <div v-for="item in 5" :key="`${item}-key-wa`" class="text-center font-bold">{{ item }}</div>
          </div>
          <div v-for="area,index in form.key_training" :key="area.title" class="mt-1">
            <h3 class="mx-1">{{ area.title }}</h3>
            <div class="grid grid-cols-5 bg-white rounded-md">
              <div v-for="item in 5" :key="`${item}-key`" class="text-center">
                <input :id="index" @input="form.key_training[index].stat = item" type="radio" :checked="area.stat == item" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" />
              </div>
            </div>
          </div>
        </section>

        <section class="pt-3 mt-5 border-t-2 border-dotted border-gray-500">
          <h3 class="font-bold mb-3">Resource Person Evaluation</h3>
          <div class="border-b-2 border-double border-gray-500 py-3" v-for="rp,indexRp in form.key_RP" :key="rp.id">
            <div class="rounded-t-md py-1">
              <h3 class="text-base font-semibold leading-6 text-gray-900">{{ `"${rp.topic}"` }}</h3>
              <p class="max-w-4xl text-sm text-gray-500">{{ `By: ${rp.full_name}` }}</p>
            </div>
            <div class="grid grid-cols-5 rounded-md">
              <div v-for="item in 5" :key="`${item}-key-wa`" class="text-center font-bold">{{ item }}</div>
            </div>

            <div v-for="area,indexKey in rp.key_rp" :key="area.title" class="mt-1">
              <h3 class="mx-1">{{ `"${area.title}"` }}</h3>
              <div class="grid grid-cols-5 bg-white rounded-md">
                <div v-for="item in 5" :key="`${rp.id}-${area.title}-${item}`" class="text-center">
                  <button type="button" @click="updateRpArea(indexRp, indexKey, item)">
                    <StarIcon :class="['h-6 w-6', area.stat == item ? 'text-yellow-500' : 'text-gray-200']"/>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <div class="mt-10">
          <div class="mt-3" v-for="item, index in form.key_learning" :key="item.title">
            <label :for="`${item.id}-key-learning`" class="block text-sm font-medium leading-6 text-gray-900">{{ `${index+1}.) ${item.title}` }}</label>
            <div class="mt-2">
              <textarea :id="`${item.id}-key-learning`" :name="`${item.id}-key-learning`" v-model="item.answer" rows="2" class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6" />
            </div>
          </div>
        </div>

        <div class="flex justify-between mt-5">
          <button @click="toggleGetName = true" type="button" class="tracking-wider inline-flex uppercase rounded-md bg-yellow-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
            Preview Credential
          </button>
          <button @click="toggleAlert = true" type="button" class="tracking-wider inline-flex uppercase rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Submit Evaluation
          </button>
        </div>
      </div>
    </div>

    <Modal v-model="form.recentlySuccessful">
      <div class="px-3 py-2 bg-green-500">
        <h2 class="text-base font-semibold text-white leading-6 tracking-wider">System Reminder</h2>
      </div>
      <div class="px-3 py-2">
        <p class="text-sm text-gray-700 font-bold">Successfully Added!</p>
      </div>
    </Modal>

    <Modal v-model="toggleAlert">
      <div class="px-3 py-2 bg-yellow-500">
        <h2 class="text-base font-semibold text-white leading-6 tracking-wider">System Reminder</h2>
      </div>
      <div class="px-3 py-2">
        <p class="text-sm text-gray-700 font-bold">Once the evaluation was submitted, it can't be undone. Proceed?</p>
      </div>
      <div class="border-t border-gray-300 px-3 py-2 flex justify-between items-center">
        <button @click="toggleAlert = false" type="button" class="inline-flex underline rounded-md py-1 px-3 text-center text-sm font-semibold text-gray-700 hover:bg-green-100">
          Review Again
        </button>
        <button @click="submitForm" :disabled="form.processing" type="button" :class="[form.processing ? 'opacity-60' : '', 'inline-flex rounded-md bg-indigo-600 py-1 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600']">
          {{ form.processing ? 'Processing...' : 'Proceed' }}
        </button>
      </div>
    </Modal>

    <Modal v-model="toggleGetName" :closable="false">
      <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
        <div class="-ml-4 -mt-4 flex flex-wrap items-center justify-between sm:flex-nowrap">
          <div class="ml-4 mt-4">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Participant's Name</h3>
            <p class="mt-1 text-sm text-gray-500">This credentials will be used to send the certificate via Email</p>
          </div>
        </div>
      </div>
      <div class="px-3 py-2 grid grid-cols-2 gap-x-1 border-b border-gray-200">
        <div class="mb-2 col-span-2">
          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
          <div class="mt-1">
            <input type="email" v-model="form.email" name="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="your@email.com" />
          </div>
          <p v-if="participantError" class="mt-1 text-sm text-red-600">{{ participantError }}</p>
        </div>
        <div class="mb-2">
          <label for="l_name"  class="block text-sm font-medium leading-6 text-gray-900">Last Name</label>
          <div class="mt-1">
            <input type="text" readonly v-model="form.l_name" name="l_name" id="l_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div class="mb-2">
          <label for="f_name"  class="block text-sm font-medium leading-6 text-gray-900">First Name</label>
          <div class="mt-1">
            <input type="text" readonly v-model="form.f_name" name="f_name" id="f_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div class="mb-2">
          <label for="m_name"  class="block text-sm font-medium leading-6 text-gray-900">Middle Initial</label>
          <div class="mt-1">
            <input type="text" readonly v-model="form.m_name" name="m_name" id="m_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div class="mb-2">
          <label for="ext_name"  class="block text-sm font-medium leading-6 text-gray-900">Name Extension</label>
          <div class="mt-1">
            <input type="text" readonly v-model="form.ext_name" name="ext_name" id="ext_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Jr., Sr..." />
          </div>
        </div>
      </div>
      <div class="my-3 mx-2">
        <button type="button" :disabled="!canProceed" :class="[canProceed ? 'bg-yellow-600 focus:bg-yellow-500' : 'bg-gray-400', 'inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm']" @click="toggleGetName = false">
          Proceed Evaluating
        </button>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import moment from 'moment';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { StarIcon } from '@heroicons/vue/20/solid';

const props = defineProps([
  "training", "officeRep", "keyTraining", "keyRP", "keyLearning", "resourcePerson", "participants"
]);


const toggleAlert = ref(false);
const toggleGetName = ref(true);
const participantError = ref(null);

const form = useForm({
  key_training: [],
  key_RP: [],
  key_learning: [],
  sex: '0',
  training_id: null,
  office_rep: 1,
  l_name: "",
  f_name: "",
  m_name: "",
  ext_name: "",
  email: "",
   participant_id: "",
});

const canProceed = computed(() => {
  const { l_name, f_name, email } = form;
  return l_name && f_name && email && !participantError.value;
});

const trainingDate = computed(() => {
  const { date_from, date_to } = props.training;
  return `${moment(date_from, "YYYY-MM-DD").format("MMM DD, YYYY")} - ${moment(date_to, "YYYY-MM-DD").format("MMM DD, YYYY")}`;
});

const resetForm = () => {
  form.key_training = props.keyTraining.map(({ id, title }) => ({ id, title, stat: 3 }));
  form.key_learning = props.keyLearning.map(({ id, title }) => ({ id, title, answer: "" }));
  const key_rps = props.keyRP.map(({ id, title }) => ({ id, title, stat: 3 }));
  form.key_RP = props.resourcePerson.map(({ id, full_name, topic }) => ({
    id, full_name, topic,
    key_rp: key_rps.map(obj => ({ ...obj }))
  }));
  form.sex = '0';
  form.office_rep = props.officeRep.length ? 1 : 1;
};

const updateRpArea = (indexRp, indexKey, item) => {
  const { key_rp } = form.key_RP[indexRp];
  form.key_RP[indexRp].key_rp = key_rp.map((obj, index) => ({
    ...obj,
    stat: index === indexKey ? item : obj.stat
  }));
};

const submitForm = () => {
  form.post(route("public.training.evaluation.post"), {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      toggleAlert.value = false;
    }
  });
};

onMounted(() => {
  form.training_id = props.training.id;
  resetForm();
});

watch(() => form.email, (newEmail) => {
  const participant = props.participants.find(p => p.email === newEmail);

  if (participant) {
    form.l_name = participant.lname;
    form.f_name = participant.fname;
    form.m_name = participant.mname;
    form.ext_name = participant.ext_name;
    form.sex = participant.is_female == 1 ? 'female' : 'male';
    form.participant_id = participant.id; // ✅ Set participant_id
    participantError.value = ""
  } else {
    form.l_name = "";
    form.f_name = "";
    form.m_name = "";
    form.ext_name = "";
    form.sex = "0";
    form.participant_id = ""; // Clear if not found
    participantError.value = "Email not registered"
  }
});

</script>
