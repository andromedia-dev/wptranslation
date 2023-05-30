<script setup>
import { computed, inject } from "vue";

const props = defineProps(["item", "role"]);

const isSelectable = inject("isSelectable");

const roleClasses = computed(() => {
    switch (props.role) {
        case "selection":
            return "relative w-12 px-6 sm:w-16 sm:px-8";
        case "actions":
            return "py-4 pl-3 pr-4 font-medium text-right sm:pr-6";
        case "title":
            return "font-medium" + (isSelectable ? " py-4 pr-3" : " px-3 py-4");
        default:
            return "px-3 py-4";
    }
});
</script>

<template>
    <td :class="['text-sm text-gray-900 whitespace-nowrap', roleClasses]">
        <slot></slot>
    </td>
</template>
