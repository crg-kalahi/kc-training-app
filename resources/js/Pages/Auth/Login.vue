<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { VueReCaptcha, useReCaptcha } from 'vue-recaptcha-v3'

const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
    recaptcha_token: '', 
});

// const submit = () => {
//     form.post(route('login'), {
//         onFinish: () => form.reset('password'),
//     });
// };


const submit = async () => {
    try {
        await recaptchaLoaded();
        form.recaptcha_token = await executeRecaptcha('login');

        if (!form.recaptcha_token) {
            alert('ReCAPTCHA verification failed. Please try again.');
            return;
        }

        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    } catch (error) {
        console.error('reCAPTCHA error:', error);
        alert('Failed to execute reCAPTCHA. Please check your network and try again.');
    }
};
</script>

<template>
    

    <section class="py-10 bg-gray-50 h-screen">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <div >
                <!-- <img src="/images/train-logo2.png" alt="T.R.A.I.N Logo" class="inline-block w-28 h-28 rounded-full" /> -->
                <img src="images/trainlogo1.png" alt="T.R.A.I.N Logo" class="h-28 w-28 inline-block">
            </div>
            <h2 class="text-3xl font-bold leading-tight text-black sm:text-1xl lg:text-2xl">Training Resources and Information Network </h2>
            <p class="max-w-xl mx-auto mt-4 text-base leading-relaxed text-gray-600">Login to your account</p>
        </div>

        <div class="relative max-w-md mx-auto mt-8">
            <div class="overflow-hidden bg-white rounded-md shadow-md">
                <BreezeValidationErrors class="px-6 pt-5" />
                <div class="px-4 py-6 sm:px-8 sm:py-7">
                    <form @submit.prevent="submit">
                        <div class="space-y-5">
                            <div>
                                <label for="" class="text-base font-medium text-gray-900"> Username </label>
                                <div class="mt-2.5 relative text-gray-400 focus-within:text-gray-600">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>

                                    <input
                                        type="username"
                                        name=""
                                        id=""
                                        v-model="form.username" required autofocus autocomplete="username"
                                        placeholder="Enter username to get started"
                                        class="block w-full py-4 pl-10 pr-4 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-200 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600"
                                    />
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="" class="text-base font-medium text-gray-900"> Password </label>

                                    <!-- <a href="#" title="" class="text-sm font-medium text-orange-500 transition-all duration-200 hover:text-orange-600 focus:text-orange-600 hover:underline"> Forgot password? </a> -->
                                </div>
                                <div class="mt-2.5 relative text-gray-400 focus-within:text-gray-600">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"
                                            />
                                        </svg>
                                    </div>

                                    <input
                                        type="password"
                                        name=""
                                        id=""
                                        v-model="form.password"
                                        placeholder="Enter your password"
                                        class="block w-full py-4 pl-10 pr-4 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-200 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600"
                                    />
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-4 text-base font-semibold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md focus:outline-none hover:bg-blue-700 focus:bg-blue-700">
                                    Log in
                                </button>
                            </div>

                             <!-- Register Button -->
                            <div class="text-center">
                                <p class="text-base text-gray-600">Don’t have an account? 
                                    <Link href="/register" class="font-medium text-blue-600 transition-all duration-200 hover:text-blue-700 hover:underline">
                                        Register
                                    </Link>
                                </p>
                            </div>

                            <!-- <div class="text-center">
                                <p class="text-base text-gray-600">Don’t have an account? <a href="#" title="" class="font-medium text-orange-500 transition-all duration-200 hover:text-orange-600 hover:underline">Create a free account</a></p>
                            </div> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


</template>
