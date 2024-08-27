<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatCurrency } from '@/utils/currency';
import { formatDate } from "@/Utils/formateDate.js";

const { props } = usePage();

defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

onMounted(() => {
    if (props.flash?.success) {
        toast.success(props.flash.success);
    } else if (props.flash?.error) {
        toast.error(props.flash.error);
    }
});
</script>

<template>
    <Head title="Meus Cursos"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Meus Cursos</h2>
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
                                Data de Inscrição
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor Pago
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
                                {{ formatDate(course.enrolled_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span v-if="course.status === 'active'" class="text-green-600">Ativo</span>
                                <span v-else class="text-red-600">Inativo</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatCurrency(course.price_paid) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <Link :href="route('mycourses.edit', course.id)"
                                      class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
