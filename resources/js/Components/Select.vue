<script setup>
import {ref} from 'vue';
import {VSelect} from 'vuetify/components';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});

const select = ref(null);

defineExpose({focus: () => select.value.focus()});

const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
    emit('update:modelValue', value);
};
</script>

<template>
    <VSelect
        v-model="select"
        :items="props.items"
        :label="props.label"
        variant="outlined"
        item-text="text"
    item-value="value"
    @input="updateValue"
    />
</template>
