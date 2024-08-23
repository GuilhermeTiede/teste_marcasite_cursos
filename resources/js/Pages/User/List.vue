<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import DangerButton from "@/Components/DangerButton.vue";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    user_id: null,
});

const isUserActive = (user) => {
    return user.active;
};

const userType = (user) => {
    return user.role === 'admin' ? 'Administrador' : 'Aluno';
};
const deleteUser = (user) => {
    form.user_id = user.id;
    form.delete(route('users.destroy', user.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.success('Usuário excluído com sucesso.');
        },
        onError: (response) => {
            const errorMessage = response.message || 'Ocorreu um erro ao excluir o usuário.';
            toast.error(errorMessage);
        },
        onFinish: () => form.reset(),
    });
};


</script>

<template>
    <Head title="Perfis"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administrar Perfis</h2>
                <Link :href="route('users.create')"
                      class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm">
                    Criar Usuário
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
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ativo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id" class="bg-white border-b border-gray-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{
                                    user.name
                                }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ userType(user) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span v-if="isUserActive(user)" class="text-green-500">Sim</span>
                                <span v-else class="text-red-500">Não</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <Link :href="route('users.edit', user.id)"
                                      class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </Link>
                                <Link :href="route('users.show', user.id)"
                                      class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </Link>
                                <DangerButton
                                    class="ms-3"
                                    @click="() => deleteUser(user)"
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
