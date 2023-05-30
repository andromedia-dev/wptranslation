<script setup>
import { computed } from "@vue/reactivity";
import _isNaN from "lodash/isNaN";

const props = defineProps({
    max: {
        type: Number,
        default: 100,
    },
    value: {
        type: Number,
        default: 50,
    },
});

const width = computed(() => {
    if (_isNaN(props.max) || _isNaN(props.value) || props.max <= 0) {
        return 0;
    }

    let v = (props.value * 100) / props.max;
    return Math.max(0, Math.min(100, v));
});
</script>

<template>
    <div class="w-full rounded-full h-2 relative bg-gray-200 overflow-hidden">
        <div class="absolute h-full bg-primary-500 transition-all duration-100" :style="'width:' + width + '%'"></div>
    </div>
</template>
