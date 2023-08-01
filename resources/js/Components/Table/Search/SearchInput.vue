<script setup>
import { MagnifyingGlassIcon } from "@heroicons/vue/20/solid";
const { __ } = wp.i18n;

defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="w-full">
        <label for="search" class="sr-only">Search</label>
        <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" aria-hidden="true">
                <MagnifyingGlassIcon class="w-4 h-4 mr-3 text-gray-500" aria-hidden="true" />
            </div>
            <input
                @change="$emit('update:modelValue', $event.target.value)"
                :value="modelValue"
                type="text"
                class="block w-full text-sm text-gray-900 bg-white border-gray-300 rounded-md placeholder:text-gray-500 pl-9 focus:border-primary-500 focus:ring-primary-500"
                :placeholder="__('Search', 'wptranslation')"
            />
        </div>
    </div>
</template>
