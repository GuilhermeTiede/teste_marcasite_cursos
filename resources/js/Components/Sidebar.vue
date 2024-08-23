<template>
    <div class="relative">
        <!-- Sidebar -->
        <div
            :class="{
                'translate-x-0': isOpen,
                '-translate-x-full': !isOpen,
            }"
            class="w-64 h-screen fixed top-0 left-0 transform transition-transform duration-300 ease-in-out z-50"
        >
            <div class="p-4">
                <h2 class="text-xl font-semibold">Maraca Site Cursos</h2>
            </div>
            <nav class="flex-1">
                <ul>
                    <li class="p-4">
                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </NavLink>
                    </li>
                    <!-- Add more navigation links here -->
                </ul>
            </nav>
        </div>

        <!-- Hamburger Button -->
        <button
            @click="toggleSidebar"
            :class="{
                'left-64': isOpen,
                'left-4': !isOpen,
            }"
            class="absolute top-4 z-10 p-2 rounded-md focus:outline-none transition-all duration-300 ease-in-out"
        >
            <svg
                v-if="!isOpen"
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16m-7 6h7"
                />
            </svg>
            <svg
                v-else
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import NavLink from '@/Components/NavLink.vue';

const isOpen = ref(false);

const toggleSidebar = () => {
    isOpen.value = !isOpen.value;
};
</script>

<style scoped>
/* Add any additional styling for your sidebar here */
</style>
