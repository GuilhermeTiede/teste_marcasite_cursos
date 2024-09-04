<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import {VSelect, VAutocomplete} from 'vuetify/components';
import {formatCurrency} from '@/utils/currency';
import vueFilePond, {setOptions} from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

// Register the plugins in vueFilePond
const FilePond = vueFilePond(FilePondPluginImagePreview, FilePondPluginFileValidateType);

setOptions({
    server: {
        process: {
            url: '/courses/upload-thumbnail',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            withCredentials: false,
            onload: (response) => {
                try {
                    const parsedResponse = JSON.parse(response);
                    return parsedResponse.key;
                } catch (e) {
                    console.error('Erro ao processar JSON:', response);
                    return null;
                }
            },
            onerror: (response) => {
                console.error('Erro no upload:', response);
                return response.data;
            }
        },
    },
});


const course = usePage().props.course || {};
const enrolledUsers = usePage().props.enrolledUsers || [];
const users = usePage().props.users || [];

const form = useForm({
    name: course.name ?? '',
    category: course.category ?? '',
    price: course.price ?? '',
    seats: course.seats ?? null,
    registration_start: course.registration_start ?? '',
    registration_end: course.registration_end ?? '',
    description: course.description ?? '',
    thumbnail_path: course.thumbnail_path ?? '',
    is_active: course.is_active ?? '',
    user_id: null,
});
console.log(form);
const isActiveItems = ['Ativo', 'Inativo'];
const categorysItems = ['Graduação', 'Pós-Graduação', 'Extensão', 'Técnico'];

const handlePriceInput = (e) => {
    form.price = formatCurrency(e.target.value);
};
console.log('Thumbnail Path:', form.thumbnail_path);
const submit = () => {
    console.log('Thumbnail Path:', form.thumbnail_path);
    if (course.id) {
        form.put(route('courses.update', course.id), {
            onSuccess: () => form.reset(),
        });
    } else {
        form.post(route('courses.store'), {
            onSuccess: () => form.reset(),
        });
    }
};
const enrollUser = () => {
    form.post(route('courses.enroll', course.id), {
        onSuccess: () => {
            form.reset('user_id');
        },
    });
};

const unenrollUser = (userId) => {
    form.delete(route('courses.unenroll', {course: course.id, user: userId}), {
        onSuccess: () => form.reset(),
    });
};

</script>

<template>
    <Head :title="course.id ? 'Editar Curso' : 'Criar Curso'"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ course.id ? 'Editar Curso' : 'Criar Curso' }}
                </h2>
                <Link :href="route('courses.index')"
                      class="text-white bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                    Voltar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6" @submit.prevent="submit">
                        <div>
                            <TextInput
                                id="name"
                                v-model="form.name"
                                autocomplete="name"
                                autofocus
                                class="mt-1 block w-full"
                                label="Nome"
                                required
                                type="text"
                            />
                            <InputError :message="form.errors.name" class="mt-2"/>
                        </div>

                        <div>
                            <v-select
                                id="category"
                                v-model="form.category"
                                :items="categorysItems"
                                label="Categoria"
                                type="text"
                                variant="outlined"
                            />
                            <InputError :message="form.errors.category" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="price"
                                v-model="form.price"
                                autocomplete="price"
                                class="mt-1 block w-full"
                                label="Preço"
                                required
                                type="text"
                                @input="handlePriceInput"
                            />
                            <InputError :message="form.errors.price" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="seats"
                                v-model="form.seats"
                                label="Vagas"
                                required
                                type="number"
                            />
                        </div>

                        <div>
                            <TextInput
                                id="registration_start"
                                v-model="form.registration_start"
                                autocomplete="registration_start"
                                class="mt-1 block w-full"
                                label="Início das Inscrições"
                                required
                                type="date"
                            />
                            <InputError :message="form.errors.registration_start" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="registration_end"
                                v-model="form.registration_end"
                                autocomplete="registration_end"
                                label="Fim das Inscrições"
                                required
                                type="date"
                            />
                            <InputError :message="form.errors.registration_end" class="mt-2"/>
                        </div>

                        <div>
                            <v-select v-model="form.is_active"
                                      :items="isActiveItems"
                                      label="Ativo?"
                                      variant="outlined"
                            />
                            <InputError :message="form.errors.is_active" class="mt-2"/>
                        </div>

                        <div>
                            <div v-if="form.thumbnail_path">
                                <img :src="form.thumbnail_path.startsWith('/storage/') ? form.thumbnail_path : `/storage/${form.thumbnail_path}`" alt="Thumbnail atual" style="max-width: 300px;" />
                            </div>

                            <div>
                                <FilePond
                                    name="thumbnail"
                                    label-idle="Arraste ou clique para enviar a imagem"
                                    accepted-file-types="image/jpeg, image/png"
                                    allow-multiple="false"
                                    @processfile="(error, file) => {
        if (!error) {
            // Captura o 'serverId' que é o 'key' retornado pelo back-end
            console.log('Server ID (Thumbnail Path):', file.serverId);
            form.thumbnail_path = file.serverId;  // Armazena no form
        } else {
            console.error('Erro no upload do arquivo', error);
            alert('Falha ao fazer upload da imagem.');
        }
    }"
                                />
                                <InputError :message="form.errors.thumbnail_path" class="mt-2"/>
                            </div>
                            <InputError :message="form.errors.thumbnail_path" class="mt-2"/>
                        </div>

                        <div class="md:col-span-2">
                            <TextareaInput
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                                label="Descrição"
                                required
                            />
                            <InputError :message="form.errors.description" class="mt-2"/>
                        </div>

                        <div class="flex justify-end md:col-span-2">
                            <button class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm"
                                    type="submit">
                                {{ course.id ? 'Salvar Alterações' : 'Criar Curso' }}
                            </button>
                        </div>
                    </form>

                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900">Alunos Inscritos</h3>
                        <ul class="list-disc list-inside">
                            <li v-for="user in enrolledUsers" :key="user.id" class="flex items-center">
                                    <span class="flex items-center">
                                        {{ user.name }}
                                        <button class="ml-2 text-red-600 hover:text-red-800"
                                                @click="unenrollUser(user.id)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </span>
                            </li>
                        </ul>
                    </div>


                    <!-- Formulário de Inscrição de Usuário -->
                    <div class="md:col-span-2">
                        <v-autocomplete
                            v-model="form.user_id"
                            :items="users"
                            item-title="name"
                            item-value="id"
                            label="Inscrever Aluno"
                            variant="outlined"
                        />

                        <InputError :message="form.errors.user_id" class="mt-2"/>
                        <button
                            class="mt-4 text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-sm"
                            type="button"
                            @click="enrollUser"
                        >
                            Inscrever Aluno
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
