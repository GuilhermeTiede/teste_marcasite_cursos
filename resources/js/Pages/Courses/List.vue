<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import DangerButton from "@/Components/DangerButton.vue";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatCurrency } from '@/utils/currency';
import { formatDate } from "@/Utils/formateDate.js";

const form = useForm({
    course_id: null,
});

const { props } = usePage();

defineProps({
    courses: {
        type: Array,
        required: true,
    },
});
const deleteCourse = (course) => {
    form.course_id = course.id;
    form.delete(route('courses.destroy', course.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Curso excluído com sucesso!');
        },
        onError: (errors) => {
            if (errors?.message) {
                toast.error(errors.message);
            } else {
                toast.error('Ocorreu um erro ao tentar excluir o curso.');
            }
        },
        onFinish: () => form.reset(),
    });
};

onMounted(() => {
    if (props.flash?.success) {
        toast.success(props.flash.success);
    } else if (props.flash?.error) {
        toast.error(props.flash.error);
    }
});
</script>

<template>
    <Head title="Cursos"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administrar Cursos</h2>
                <Link :href="route('courses.create')"
                      class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm">
                    Cadastrar Cursos
                </Link>
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
                                Ativo
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
                                <span v-if="course.is_active" class="text-green-600">Sim</span>
                                <span v-else class="text-red-600">Não</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(course.registration_start) }} - {{ formatDate(course.registration_end) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ course.seats }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <Link :href="route('courses.edit', course.id)"
                                      class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </Link>
                                <DangerButton
                                    class="ml-3"
                                    @click="() => deleteCourse(course)"
                                >
                                    <i class="fas fa-trash"></i>
                                </DangerButton>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
