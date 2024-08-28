<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatCurrency } from '@/utils/currency';
import { formatDate } from "@/Utils/formateDate.js";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import {loadStripe} from '@stripe/stripe-js';

const { props } = usePage();

defineProps({
    courses: {
        type: Array,
        required: true,
    },
    enrolledCourses: {
        type: Array,
        required: true,
    },
});

const enrollCourse = async (course) => {
    const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
    const response = await axios.post(route('purchase.course'), {
        course_name: course.name,
        price: course.price,
    });

    const sessionId = response.data.id;

    stripe.redirectToCheckout({ sessionId });
};

const isEnrolled = (courseId) => {
    // Verifique se enrolledCourses está definido e é um array antes de usar includes
    return props.enrolledCourses && Array.isArray(props.enrolledCourses)
        ? props.enrolledCourses.includes(courseId)
        : false;
};
</script>

<template>
    <Head title="Vitrine de Cursos"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vitrine de Cursos</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nome
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Período de Inscrição
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vagas restantes
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="course in courses" :key="course.id" class="bg-white border-b border-gray-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ course.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatCurrency(course.price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(course.registration_start) }} - {{ formatDate(course.registration_end) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ course.seats }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex items-center space-x-2">
                                <button
                                    @click="enrollCourse(course)"
                                    :disabled="isEnrolled(course.id)"
                                    class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm"
                                >
                                    <span v-if="isEnrolled(course.id)">Já Matriculado</span>
                                    <span v-else>Comprar</span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
