<script setup>
import _uniqueId from "lodash/uniqueId";

defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    items: {
        type: Array,
        default: [],
    },
    propTitle: {
        type: String,
        default: "id",
    },
    propKey: {
        type: String,
        default: "id",
    },
});

const emit = defineEmits(["update:modelValue"]);

const uid = _uniqueId("filters-select");

const change = (event) => {
    if (event.target.value.length > 0) {
        emit("update:modelValue", event.target.value);
    } else {
        emit("update:modelValue", undefined);
    }
};
</script>

<template>
    <div v-if="items.length > 0">
        <label :for="uid" class="block text-sm font-medium text-gray-600">
            <slot></slot>
        </label>
        <select
            :value="modelValue"
            @change="change"
            :id="uid"
            :name="uid"
            class="block w-full py-2 pl-3 pr-10 mt-1 text-base text-gray-900 bg-white border-gray-300 rounded-md focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
        >
            <option value=""></option>
            <option v-for="item in items" :key="item[propKey]" :value="item[propKey]">
                {{ item[propTitle] }}
            </option>
        </select>
    </div>
</template>
