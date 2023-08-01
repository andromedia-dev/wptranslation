<script setup>
import { inject, getCurrentInstance } from "vue";
import Switch from "@Components/Form/Switch.vue";
import FormErrors from "@Components/Form/Errors.vue";
import { LockOpenIcon } from "@heroicons/vue/20/solid";
const { __ } = wp.i18n;

const $form = inject("$form");
const $notify = inject("$notify");

const services = [
    {
        key: "google-translate-free",
        name: __("Google Translate", "wptranslation"),
    },
    {
        key: "deepl",
        name: __("DeepL (Premium only)", "wptranslation"),
        available: false,
    },
];

const form = $form(await $form().submit("settings_index"));

const submit = async () => {
    form.submit("settings_update", {
        onSuccess: () => {
            $notify({
                title: __("Your settings have been saved.", "wptranslation"),
                type: "success",
            });
        },
    });
};
</script>

<template>
    <form class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8" @submit.prevent="submit">
        <div class="space-y-12">
            <div class="pb-12 border-b border-gray-900/10">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="service" class="block text-sm font-medium leading-6 text-gray-900">
                            {{ __("Translation service", "wptranslation") }}
                        </label>
                        <div class="mt-2">
                            <select
                                v-model="form.service"
                                id="service"
                                name="service"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:max-w-xs sm:text-sm sm:leading-6"
                            >
                                <option v-for="service in services" :key="service.key" :value="service.key" :disabled="service.available === false">
                                    {{ service.name }}
                                </option>
                            </select>
                            <FormErrors class="mt-2" :errors="form.errors['service']" />
                        </div>
                    </div>

                    <div class="sm:col-span-full">
                        <div class="flex items-center">
                            <Switch disabled :modelValue="false">{{ __("Rewrite title with Chat GPT-4", "wptranslation") }}</Switch>
                            <a
                                :href="getCurrentInstance().appContext.config.globalProperties.$admin('admin.php?page=wptranslation-pricing')"
                                type="button"
                                class="ml-6 inline-flex items-center gap-x-1.5 rounded-md bg-primary-500 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
                            >
                                <LockOpenIcon class="-ml-0.5 h-5 w-5" aria-hidden="true" />
                                {{ __("Unlock with premium plan", "wptranslation") }}
                            </a>
                        </div>
                        <FormErrors class="mt-2" :errors="form.errors['rewrite.title.enabled']" />
                    </div>

                    <div class="sm:col-span-full">
                        <Switch v-model="form.translation.post_meta.enabled">{{ __("Translate custom fields", "wptranslation") }}</Switch>
                        <FormErrors class="mt-2" :errors="form.errors['translation.post_meta.enabled']" />
                    </div>

                    <div class="sm:col-span-full">
                        <Switch v-model="form.translation.yoast.enabled">{{ __("Translate Yoast SEO", "wptranslation") }}</Switch>
                        <FormErrors class="mt-2" :errors="form.errors['translation.yoast.enabled']" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-6 gap-x-6">
            <button @click="form.reset()" type="button" class="text-sm font-semibold leading-6 text-gray-900">
                {{ __("Discard changes", "wptranslation") }}
            </button>
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
                {{ __("Save changes", "wptranslation") }}
            </button>
        </div>
    </form>
</template>
