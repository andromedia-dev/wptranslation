<script setup>
import { computed } from "vue";
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/20/solid";
import _head from "lodash/head";
import _last from "lodash/last";
import _slice from "lodash/slice";

const props = defineProps({
    modelValue: {
        type: Number,
        default: undefined,
    },
    items: {
        type: Object,
        default: [],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const firstLink = computed(() => {
    return _head(props.items.links);
});
const lastLink = computed(() => {
    return _last(props.items.links);
});
const otherLinks = computed(() => {
    return _slice(props.items.links, 1, props.items.links.length - 1);
});

const submit = (value) => {
    if (props.disabled) return;

    emit("update:modelValue", value);
};
</script>

<template>
    <div class="md:border-gray-300 md:shadow md:py-2 md:border-y md:border-x md:rounded-lg md:px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-between flex-1">
                <div class="flex-none hidden xl:block">
                    <p class="text-sm text-gray-500">
                        <span class="font-medium">{{ items.from }}</span>
                        -
                        <span class="font-medium">{{ items.to }}</span>
                        of
                        <span class="font-medium">{{ items.total }}</span>
                    </p>
                </div>
                <nav class="inline-flex items-center justify-between flex-grow space-x-1 xl:flex-none xl:justify-start" aria-label="Pagination">
                    <div
                        v-if="firstLink.url === null"
                        class="relative inline-flex items-center p-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-md"
                    >
                        <span class="sr-only">Previous</span>
                        <ChevronLeftIcon class="w-5 h-5" aria-hidden="true" />
                    </div>
                    <div
                        v-else
                        @click="submit(items.current_page - 1)"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50"
                    >
                        <span class="sr-only">Previous</span>
                        <ChevronLeftIcon class="w-5 h-5" aria-hidden="true" />
                    </div>
                    <div class="inline-flex items-center space-x-1">
                        <template v-for="(link, index) in otherLinks" :key="index">
                            <div
                                v-if="link.url === null"
                                class="relative z-10 inline-flex items-center px-2 py-0.5 text-sm font-medium text-gray-500 border border-transparent"
                            >
                                {{ link.label }}
                            </div>
                            <div
                                v-else-if="link.active"
                                class="relative z-10 inline-flex items-center px-2 py-0.5 text-sm font-medium text-gray-900 border border-gray-300 rounded-md"
                            >
                                {{ link.label }}
                            </div>
                            <div
                                v-else
                                @click="submit(parseInt(link.label))"
                                class="relative inline-flex items-center px-2 py-0.5 text-sm font-medium text-gray-500 border border-transparent rounded-md cursor-pointer hover:bg-gray-50"
                            >
                                {{ link.label }}
                            </div>
                        </template>
                    </div>
                    <div
                        v-if="lastLink.url === null"
                        class="relative inline-flex items-center p-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-md"
                    >
                        <span class="sr-only">Next</span>
                        <ChevronRightIcon class="w-5 h-5" aria-hidden="true" />
                    </div>
                    <div
                        v-else
                        @click="submit(items.current_page + 1)"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50"
                    >
                        <span class="sr-only">Next</span>
                        <ChevronRightIcon class="w-5 h-5" aria-hidden="true" />
                    </div>
                </nav>
            </div>
        </div>
    </div>
</template>
