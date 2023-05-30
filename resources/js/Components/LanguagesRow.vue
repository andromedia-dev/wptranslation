<script setup>
import _take from "lodash/take";
import _takeRight from "lodash/takeRight";
import _filter from "lodash/filter";
import { PlusIcon } from "@heroicons/vue/20/solid";
import { computed } from "vue";

const props = defineProps({
    languages: {
        type: Array,
        default: [],
    },
    activeLanguages: {
        type: [Array, Boolean],
        default: false,
    },
    shown: {
        type: Number,
        default: 5,
    },
});

const isActiveLanguage = (lang) => {
    return props.activeLanguages === false || props.activeLanguages.indexOf(lang) !== -1;
};

const tooltipLanguages = computed(() => {
    return _filter(_takeRight(props.languages, props.languages.length - props.shown), (l) => isActiveLanguage(l.slug));
});
</script>

<template>
    <div class="inline-flex flex-nowrap items-center space-x-2 whitespace-nowrap">
        <template v-for="language in _take(languages, shown)">
            <div v-if="isActiveLanguage(language.slug)" class="h-5 w-5 flex items-center justify-center">
                <img :src="language.flag" />
            </div>
            <div v-else class="h-5 w-5"></div>
        </template>
        <tippy v-if="tooltipLanguages.length > 0">
            <div class="h-5 w-5 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-50">
                <PlusIcon class="h-4 w-4" />
            </div>

            <template #content>
                <div class="bg-white border border-gray-300 rounded-md shadow-sm p-4 max-w-sm">
                    <div class="inline-flex flex-wrap items-center justify-center -mx-2">
                        <div v-for="language in tooltipLanguages" class="px-1 h-6 w-6 flex items-center justify-center">
                            <img :src="language.flag" />
                        </div>
                    </div>
                </div>
            </template>
        </tippy>
    </div>
</template>
