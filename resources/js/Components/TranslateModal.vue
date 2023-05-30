<script setup>
import { ref, inject, getCurrentInstance } from "vue";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from "@headlessui/vue";
import _findIndex from "lodash/findIndex";
import Switch from "@Components/Form/Switch.vue";
import { LanguageIcon, LockOpenIcon } from "@heroicons/vue/20/solid";
import _cloneDeep from "lodash/cloneDeep";
import _forEach from "lodash/forEach";
const { __ } = wp.i18n;

const props = defineProps({
    languages: {
        type: Array,
        default: [],
    },
    filters: {
        type: Object,
    },
    selected: {
        type: Object,
    },
});

const $form = inject("$form");

const open = ref(false);
const step = ref("settings");
const processing = ref(false);

const form = $form({
    languages: props.languages.map((l) => l.slug),
    refresh: false,
    filters: props.filters,
    selected: props.selected,
});

const isSelected = (language) => {
    return form.languages.includes(language);
};

const toggleLanguage = (language) => {
    if (isSelected(language)) {
        form.languages.splice(form.languages.indexOf(language), 1);
    } else {
        form.languages.push(language);
    }
};

const selectAll = () => {
    form.languages = props.languages.map((l) => l.slug);
};

const deselectAll = () => {
    form.languages = [];
};

const show = () => {
    step.value = "pending";
    open.value = true;
};

const close = () => {
    if (processing.value) return;

    open.value = false;
};

const submit = async () => {
    step.value = "no_premium";
};

defineExpose({ show });
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-[100000]" @close="close">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" />
            </TransitionChild>

            <div class="fixed inset-0 z-[100000] overflow-y-auto">
                <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            class="relative w-full px-4 pt-5 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:max-w-3xl sm:p-6"
                        >
                            <Transition name="fade-500" mode="out-in">
                                <div v-if="step === 'pending'" key="pending">
                                    <div class="space-y-6">
                                        <div>
                                            <div class="block text-sm font-medium leading-6 text-gray-900">
                                                {{ __("Languages", "polytranslate") }}
                                            </div>
                                            <div class="flex items-center justify-end mt-2 space-x-2">
                                                <button
                                                    @click="selectAll"
                                                    type="button"
                                                    class="px-2 py-1 text-xs font-semibold text-gray-900 bg-white rounded shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                >
                                                    {{ __("Select all", "polytranslate") }}
                                                </button>
                                                <button
                                                    @click="deselectAll"
                                                    type="button"
                                                    class="px-2 py-1 text-xs font-semibold text-gray-900 bg-white rounded shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                >
                                                    {{ __("Deselect all", "polytranslate") }}
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-3 gap-4 mt-2 sm:grid-cols-4 lg:grid-cols-6">
                                                <div
                                                    v-for="language in languages"
                                                    @click="toggleLanguage(language.slug)"
                                                    :class="[
                                                        isSelected(language.slug)
                                                            ? 'border-primary-500 ring-2 ring-primary-500'
                                                            : 'border-gray-300 hover:bg-gray-50',
                                                        'border cursor-pointer rounded-md flex flex-col p-3 items-center justify-center focus:outline-none',
                                                    ]"
                                                >
                                                    <img :src="language.flag" />
                                                    <span class="mt-1">{{ language.name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <Switch v-model="form.refresh">{{ __("Refresh content", "polytranslate") }}</Switch>
                                            <div class="mt-1 text-xs text-gray-600 ml-14">
                                                {{ __("Your existing content will be overwritten", "polytranslate") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-center mt-5 sm:mt-6">
                                        <button
                                            @click="submit"
                                            :disabled="processing || form.languages.length === 0"
                                            :class="[
                                                processing || form.languages.length === 0 ? 'opacity-25' : 'hover:bg-primary-400',
                                                'text-white bg-primary-500 inline-flex items-center px-4 py-2 text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
                                            ]"
                                        >
                                            <LanguageIcon class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" />
                                            {{ __("Translate", "polytranslate") }}
                                        </button>
                                    </div>
                                </div>
                                <div v-else-if="step === 'no_premium'" key="no_premium">
                                    <div class="max-w-md mx-auto text-lg text-center">
                                        {{ __("Sorry, the", "polytranslate") }} <span class="text-primary-500">{{ __("bulk mode", "polytranslate") }}</span>
                                        {{ __("is not available in free version.", "polytranslate") }}
                                    </div>
                                    <div class="flex justify-center mt-6">
                                        <a
                                            :href="getCurrentInstance().appContext.config.globalProperties.$admin('admin.php?page=polytranslate-pricing')"
                                            type="button"
                                            class="inline-flex items-center gap-x-1.5 rounded-md bg-primary-500 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
                                        >
                                            <LockOpenIcon class="-ml-0.5 h-5 w-5" aria-hidden="true" />
                                            {{ __("Unlock with premium plan", "polytranslate") }}
                                        </a>
                                    </div>

                                    <div class="w-full h-px my-6 bg-gray-300"></div>

                                    <div class="text-center">{{ __("With the", "polytranslate") }}</div>
                                    <div class="text-lg text-center">{{ __("Free version", "polytranslate") }}</div>

                                    <div class="max-w-md mx-auto mt-3 text-center">
                                        {{ __("You can automatically translate your publications from Polylang.", "polytranslate") }}
                                    </div>

                                    <img class="mx-auto mt-6" :src="getCurrentInstance().appContext.config.globalProperties.$asset('img/polylang_plus.gif')" />
                                </div>
                            </Transition>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
