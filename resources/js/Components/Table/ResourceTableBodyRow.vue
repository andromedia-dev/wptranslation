<script setup>
import { computed, inject } from "vue";
import ResourceTableBodyColumn from "./ResourceTableBodyColumn.vue";

const props = defineProps(["item"]);

const isSelectable = inject("isSelectable");

const selected = inject("selected");

const items = inject("items");

const isSelected = computed(() => {
    return selected.value.selected.includes(props.item.id);
});

const toggleSelect = () => {
    if (isSelected.value) {
        selected.value.selected.splice(selected.value.selected.indexOf(props.item.id), 1);
        selected.value.all = false;
    } else {
        selected.value.selected.push(props.item.id);
    }

    selected.value.current = selected.value.selected.length === items.value.data.length;
};
</script>

<template>
    <tr>
        <ResourceTableBodyColumn role="selection" v-if="isSelectable">
            <div v-if="isSelected" class="absolute inset-y-0 left-0 w-0.5 bg-primary-500"></div>
            <input
                type="checkbox"
                class="absolute w-4 h-4 -mt-2 bg-white border-gray-300 rounded text-primary-500 left-4 top-1/2 focus:ring-primary-500 sm:left-6"
                @click.stop
                @change="toggleSelect"
                :checked="isSelected"
            />
        </ResourceTableBodyColumn>
        <slot :selected="isSelected"></slot>
    </tr>
</template>
