<script setup>
import { ref, onMounted } from 'vue';
import { VTextField } from 'vuetify/components';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <VTextField
        v-model="model"
        ref="input"
        variant="outlined"
        :autofocus="true"
    />
</template>
