<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import {toast} from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import {ref, computed, onMounted, watch} from 'vue';
import {usePage} from '@inertiajs/vue3';
import {formatCurrency} from '@/utils/currency';
import {formatDate} from "@/Utils/formateDate.js";
import * as XLSX from 'xlsx';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import axios from 'axios';
import * as jspdf from "jspdf";

const form = useForm({
    course_id: null,
});

const {props} = usePage();

defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const selectedCourse = ref(null);
const confirmingUserDeletion = ref(false);
const searchQuery = ref('');

const openModal = (course) => {
    selectedCourse.value = course;
    confirmingUserDeletion.value = true;
    searchStudents();
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    selectedCourse.value = null;
    searchQuery.value = '';
    filteredUsers.value = [];
};

const filteredUsers = ref([]);

const searchStudents = async () => {
    if (!selectedCourse.value) return;

    try {
        const response = await axios.get(route('courses.students.search', selectedCourse.value.id), {
            params: {
                query: searchQuery.value
            }
        });
        filteredUsers.value = response.data;
    } catch (error) {
        toast.error('Erro ao buscar alunos.');
    }
};

watch(searchQuery, () => {
    if (searchQuery.value.length > 2 || searchQuery.value.length === 0) {
        searchStudents();
    }
});

const deleteCourse = (course) => {
    form.set('course_id', course.id);
    form.delete(route('courses.destroy', course.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Curso excluído com sucesso!');
        },
        onError: (errors) => {
            toast.error(errors?.message || 'Ocorreu um erro ao tentar excluir o curso.');
        },
        onFinish: () => form.reset(),
    });
};

const exportToExcel = () => {
    if (filteredUsers.value.length === 0) {
        toast.error('Nenhum aluno encontrado para exportação.');
        return;
    }

    const worksheet = XLSX.utils.json_to_sheet(filteredUsers.value);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Alunos');
    XLSX.writeFile(workbook, `alunos_${selectedCourse.value.name}.xlsx`);
};

const exportToPDF = () => {
    if (filteredUsers.value.length === 0) {
        toast.error('Nenhum aluno encontrado para exportação.');
        return;
    }

    const doc = new jsPDF();
    doc.autoTable({
        head: [['Nome']],
        body: filteredUsers.value.map(user => [user.name]),
    });
    doc.save(`alunos_${selectedCourse.value.name}.pdf`);
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
                                Miniatura
                            </th>
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
                                <img v-if="course.thumbnail_path" :src="`/storage/${course.thumbnail_path}`" alt="Miniatura do curso"
                                     class="w-16 h-16 object-cover" />
                                <span v-else class="text-gray-500">Sem miniatura</span>
                            </td>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex items-center space-x-2">
                                <Link :href="route('courses.edit', course.id)"
                                      class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </Link>
                                <button @click="openModal(course)" class="text-blue-600 hover:text-blue-800 ml-3">
                                    <i class="fa-solid fa-users"></i>
                                </button>
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

    <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">
                Alunos Matriculados no Curso: {{ selectedCourse?.name }}
            </h3>
            <input
                type="text"
                placeholder="Buscar aluno..."
                v-model="searchQuery"
                class="mt-4 p-2 border rounded w-full"
            />
            <ul class="list-disc list-inside mt-4">
                <li v-for="user in filteredUsers" :key="user.id">
                    {{ user.name }}
                </li>
            </ul>

            <div class="mt-6 flex justify-end space-x-4">
                <SecondaryButton @click="exportToExcel">Exportar Excel</SecondaryButton>
                <SecondaryButton @click="exportToPDF">Exportar PDF</SecondaryButton>
                <SecondaryButton @click="closeModal">Fechar</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
