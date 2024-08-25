<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import { VSelect } from 'vuetify/components';
import { formatCurrency } from '@/utils/currency';

const course = usePage().props.course || {};

const form = useForm({
    name: course.name || '',
    category: course.category || '',
    price: course.price || '',
    seats: course.seats || 0,
    registration_start: course.registration_start || '',
    registration_end: course.registration_end || '',
    description: course.description || '',
    thumbnail: course.thumbnail || '',
    is_active: course.is_active ?? '',
});

const isActiveItems = ['Ativo', 'Inativo'];
const categorysItems = ['Graduação', 'Pós-Graduação', 'Extensão', 'Técnico'];

const handlePriceInput = (e) => {
    form.price = formatCurrency(e.target.value);
};

const submit = () => {
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
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <TextInput
                                id="name"
                                label="Nome"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError :message="form.errors.name" class="mt-2"/>
                        </div>

                        <div>
                            <v-select
                                id="category"
                                label="Categoria"
                                type="text"
                                v-model="form.category"
                                :items="categorysItems"
                                variant="outlined"
                            />
                            <InputError :message="form.errors.category" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="price"
                                label="Preço"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.price"
                                required
                                autocomplete="price"
                                @input="handlePriceInput"
                            />
                            <InputError :message="form.errors.price" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="seats"
                                label="Vagas"
                                type="number"
                                v-model="form.seats"
                                required
                            />
                        </div>

                        <div>
                            <TextInput
                                id="registration_start"
                                label="Início das Inscrições"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.registration_start"
                                required
                                autocomplete="registration_start"
                            />
                            <InputError :message="form.errors.registration_start" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="registration_end"
                                label="Fim das Inscrições"
                                type="date"
                                v-model="form.registration_end"
                                required
                                autocomplete="registration_end"
                            />
                            <InputError :message="form.errors.registration_end" class="mt-2"/>
                        </div>

                        <div>
                            <v-select v-model="form.is_active"
                                      :items="isActiveItems"
                                      variant="outlined"
                                      label="Ativo?"
                            />
                            <InputError :message="form.errors.is_active" class="mt-2"/>
                        </div>

                        <div>
                            <TextInput
                                id="thumbnail"
                                label="Thumbnail"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.thumbnail"
                                autofocus
                                autocomplete="thumbnail"
                            />
                            <InputError :message="form.errors.thumbnail" class="mt-2"/>
                        </div>

                        <div class="md:col-span-2">
                            <TextareaInput
                                id="description"
                                label="Descrição"
                                class="mt-1 block w-full"
                                v-model="form.description"
                                required
                            />
                            <InputError :message="form.errors.description" class="mt-2"/>
                        </div>

                        <div class="flex justify-end md:col-span-2">
                            <button type="submit"
                                    class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm">
                                {{ course.id ? 'Salvar Alterações' : 'Criar Curso' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
