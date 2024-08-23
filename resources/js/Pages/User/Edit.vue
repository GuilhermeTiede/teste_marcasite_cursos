<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";

const user = usePage().props.user;

const form = useForm({
    name: user.name || '',
    email: user.email || '',
    password: '',
    password_confirmation: '',
    role: user.role || 'user',
    active: user.active ?? 1,
});

const submit = () => {
    if (user.id) {
        form.put(route('users.update', user.id), {
            onSuccess: () => form.reset('password', 'password_confirmation'),
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => form.reset(),
        });
    }
};
</script>

<template>
    <Head title="Editar Perfil"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Perfil</h2>
                <Link :href="route('users.index')" class="text-white bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                    Voltar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <InputLabel for="name" value="Nome" />

                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="email"
                            />
                            <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                        </div>

                        <div>
                            <InputLabel for="password" value="Senha" />

                            <TextInput
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />

                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirma Senha" />

                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />

                            <InputError :message="form.errors.password_confirmation" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="role" value="Tipo de Usuário" />

                            <select v-model="form.role" id="role" class="mt-1 block w-full">
                                <option value="user">Aluno</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <span v-if="form.errors.role" class="text-red-500 text-sm">{{ form.errors.role }}</span>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="role" value="Ativo?" />

                            <select v-model="form.active" id="role" class="mt-1 block w-full">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                            <span v-if="form.errors.active" class="text-red-500 text-sm">{{ form.errors.active }}</span>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
