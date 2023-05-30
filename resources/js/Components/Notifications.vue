<script setup>
import { ref } from "vue";
import { CheckCircleIcon, ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { XMarkIcon } from "@heroicons/vue/20/solid";
import _findIndex from "lodash/findIndex";
import _defaults from "lodash/defaults";
import _uniqueId from "lodash/uniqueId";

const defaults = {
    duration: 3000,
    dismisable: true,
    type: "success",
};

const items = ref([]);

const notify = (item) => {
    item.id = _uniqueId("notification");
    item = _defaults(item, defaults);
    items.value.push(item);

    window.setTimeout(() => close(item), item.duration);
};

const close = (item) => {
    const index = _findIndex(items.value, ["id", item.id]);
    if (index !== -1) {
        items.value.splice(index, 1);
    }
};

defineExpose({ notify });
</script>

<template>
    <div class="pointer-events-none fixed right-0 left-0 bottom-0 top-wpcontent flex items-end px-4 py-6 sm:items-start sm:p-6 z-[100001]">
        <div class="flex flex-col items-center w-full space-y-4 sm:items-end">
            <TransitionGroup
                enter-active-class="transition duration-300 ease-out transform"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-lg pointer-events-auto ring-1 ring-black ring-opacity-5"
                >
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <ExclamationTriangleIcon v-if="item.type === 'error'" class="w-6 h-6 text-red-400" aria-hidden="true" />
                                <CheckCircleIcon v-else class="w-6 h-6 text-green-400" aria-hidden="true" />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">{{ item.title }}</p>
                                <p v-if="item.body" class="mt-1 text-sm text-gray-500">{{ item.body }}</p>
                            </div>
                            <div class="flex flex-shrink-0 ml-4" v-if="item.dismisable">
                                <button
                                    type="button"
                                    @click="close(item)"
                                    class="inline-flex text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                >
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="w-5 h-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </div>
</template>
