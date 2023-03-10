<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

const form = useForm({
    fname: '',
    mname: '',
    lname: '',
    ext_name: '',
    id_number: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Register" />

        <BreezeValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div class="flex gap-x-5 divide-x">
                <div class="shrink">
                    <div>
                        <BreezeLabel for="id_number" value="ID Number" />
                        <BreezeInput id="id_number" type="text" class="mt-1 block w-full" v-model="form.id_number" required autofocus autocomplete="name" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <BreezeLabel for="lname" value="Sure/Last Name" />
                            <BreezeInput id="lname" type="text" class="mt-1 block w-full" v-model="form.lname" required autocomplete="name" />
                        </div>
                        <div>
                            <BreezeLabel for="fname" value="First Name" />
                            <BreezeInput id="fname" type="text" class="mt-1 block w-full" v-model="form.fname" required autocomplete="name" />
                        </div>
                        <div>
                            <BreezeLabel for="mname" value="Middle Name" />
                            <BreezeInput id="mname" type="text" class="mt-1 block w-full" v-model="form.mname" autocomplete="name" />
                        </div>
                        <div>
                            <BreezeLabel for="ext_name" value="Extension Name" />
                            <BreezeInput id="ext_name" type="text" class="mt-1 block w-full" v-model="form.ext_name" autocomplete="name" />
                        </div>
                    </div>
                </div>
                <div class="grow pl-4">
                    <div class="">
                        <BreezeLabel for="email" value="Email" />
                        <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="password" value="Password" />
                        <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="password_confirmation" value="Confirm Password" />
                        <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
                    </div>
                </div>
            </div>


            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Already registered?
                </Link>

                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
