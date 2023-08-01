<script setup>
import { getCurrentInstance } from "vue";
const { __ } = wp.i18n;
</script>

<template>
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    {{ __("Polylang not detected", "wptranslation") }}
                </h3>
                <div class="mt-2 text-sm text-gray-500">
                    {{ __("WPTranslation works with Polylang. You must have the Polylang plugin activated in order to use WPTranslation.", "wptranslation") }}
                    <p></p>
                </div>
                <div class="mt-5">
                    <a
                        :href="getCurrentInstance().appContext.config.globalProperties.$admin('plugins.php')"
                        type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white rounded-md shadow-sm bg-primary-500 hover:bg-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400"
                    >
                        {{ __("Plugins", "wptranslation") }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
