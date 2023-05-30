<script setup>
import { computed, inject } from "vue";
import { ChevronDownIcon, ChevronUpIcon } from "@heroicons/vue/20/solid";

const props = defineProps(["role", "sortable", "column"]);

const isSelectable = inject("isSelectable");
const sort = inject("sort");

const roleClasses = computed(() => {
    switch (props.role) {
        case "selection":
            return "relative w-12 px-6 sm:w-16 sm:px-8";
        case "actions":
            return "relative py-3.5 pl-3 pr-4 sm:pr-6";
        case "title":
            return "text-sm font-semibold text-left text-gray-900" + (isSelectable ? " py-3.5 pr-3" : " px-3 py-3.5");
        default:
            return "px-3 py-3.5 text-left text-sm font-semibold text-gray-900";
    }
});

const isColumnSorted = computed(() => {
    if (sort.order === undefined) {
        return false;
    }

    return sort.order === props.column;
});

const updateSort = () => {
    if (!props.sortable) {
        return;
    }

    if (isColumnSorted.value) {
        if (sort.direction === "asc") {
            sort.order = undefined;
            sort.direction = undefined;
        } else if (sort.direction === "desc") {
            sort.direction = "asc";
        } else {
            sort.direction = "desc";
        }
    } else {
        sort.order = props.column;
        sort.direction = "desc";
    }
};
</script>

<template>
    <th scope="col" :class="roleClasses">
        <div :class="['inline-flex items-center group', sortable ? 'cursor-pointer' : '']" @click="updateSort">
            <slot></slot>
            <template v-if="sortable">
                <span v-if="isColumnSorted" class="flex-none ml-2 text-gray-900 bg-white rounded group-hover:bg-gray-50">
                    <ChevronUpIcon v-if="sort.direction === 'asc'" class="w-5 h-5" aria-hidden="true" />
                    <ChevronDownIcon v-else class="w-5 h-5" aria-hidden="true" />
                </span>
                <span v-else class="flex-none invisible ml-2 text-gray-900 rounded group-hover:visible group-focus:visible">
                    <ChevronUpIcon v-if="sort.direction === 'asc'" class="w-5 h-5" aria-hidden="true" />
                    <ChevronDownIcon v-else class="w-5 h-5" aria-hidden="true" />
                </span>
            </template>
        </div>
    </th>
</template>
