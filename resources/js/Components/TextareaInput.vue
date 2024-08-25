<script setup>
import { ref } from 'vue';
import { VTextarea } from 'vuetify/components';

const model = defineModel({
    type: String,
    required: true,
});

const textarea = ref(null);

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <VTextarea
        v-model="model"
        ref="textarea"
        rows="4"
        variant="outlined"
        label="Descrição"
    ></VTextarea>
</template>
