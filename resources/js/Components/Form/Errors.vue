<script setup>
const props = defineProps({
    errors: {
        type: Array,
        default: [],
    },
});
</script>

<template>
    <div class="space-y-1 text-xs text-red-500" v-if="errors.length > 0">
        <div v-for="(error, index) in errors" :key="index">{{ error }}</div>
    </div>
</template>
