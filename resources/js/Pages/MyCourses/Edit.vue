<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const { props } = usePage();

const course = props.course || {};

</script>

<template>
    <Head :title="`Detalhes do Curso: ${course.name}`"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalhes do Curso</h2>
                <Link :href="route('mycourses.list')"
                      class="text-white bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                    Voltar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <TextInput
                                id="name"
                                label="Nome do Curso"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="course.name"
                                disabled
                            />
                        </div>

                        <div>
                            <TextInput
                                id="category"
                                label="Categoria"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="course.category"
                                disabled
                            />
                        </div>

                        <div>
                            <TextInput
                                id="price"
                                label="Valor Pago"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="course.price_paid"
                                disabled
                            />
                        </div>

                        <div>
                            <TextInput
                                id="enrolled_at"
                                label="Data de Inscrição"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="course.enrolled_at"
                                disabled
                            />
                        </div>

                        <div>
                            <TextInput
                                id="status"
                                label="Status"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="course.status"
                                disabled
                            />
                        </div>

                        <div>
                            <TextInput
                                id="description"
                                label="Descrição"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="course.description"
                                disabled
                            />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
