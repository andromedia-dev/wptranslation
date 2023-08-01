<script setup>
import { ref, inject, getCurrentInstance } from "vue";
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import AppLink from "@Components/AppLink.vue";
import NProgress from "nprogress";
import Notifications from "@Components/Notifications.vue";
const { __ } = wp.i18n;

const navigation = [
    { name: __("Bulk translation", "wptranslation"), href: { name: "dashboard" } },
    { name: __("Settings", "wptranslation"), href: { name: "settings" } },
    { name: __("Help", "wptranslation"), href: "https://wptranslation.net/documentation", target: "_blank" },
];

const pending = () => {
    NProgress.start();
};
const resolve = () => {
    NProgress.done();
};
const fallback = () => {
    NProgress.done();
};

const notifications = ref();
const setNotificationComponent = inject("setNotificationComponent");
setNotificationComponent(notifications);
</script>

<template>
    <div>
        <Disclosure as="nav" class="bg-white border-b border-gray-200" v-slot="{ open }">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex items-center flex-shrink-0">
                            <img class="block w-auto h-12" :src="getCurrentInstance().appContext.config.globalProperties.$asset('img/logo.png')" />
                        </div>
                        <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                            <AppLink
                                v-for="item in navigation"
                                :key="item.name"
                                :to="item.href"
                                :target="item.target"
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2"
                                active-class="text-gray-900 border-primary-500"
                                inactive-class="text-gray-500 border-transparent hover:border-gray-300 hover:text-gray-700"
                            >
                                {{ item.name }}
                            </AppLink>
                        </div>
                    </div>
                    <div class="flex items-center -mr-2 sm:hidden">
                        <!-- Mobile menu button -->
                        <DisclosureButton
                            class="inline-flex items-center justify-center p-2 text-gray-400 bg-white rounded-md hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block w-6 h-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block w-6 h-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>
                </div>
            </div>

            <DisclosurePanel class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <DisclosureButton as="template" v-for="item in navigation" :key="item.name">
                        <AppLink
                            :to="item.href"
                            :target="item.target"
                            class="block py-2 pl-3 pr-4 text-base font-medium border-l-4"
                            active-class="border-primary-500 bg-primary-50 text-primary-700"
                            inactive-class="text-gray-600 border-transparent hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800"
                        >
                            {{ item.name }}
                        </AppLink>
                    </DisclosureButton>
                </div>
            </DisclosurePanel>
        </Disclosure>

        <div class="py-10">
            <RouterView v-slot="{ Component }">
                <Transition name="fade-75" mode="out-in">
                    <Suspense @pending="pending" @resolve="resolve" @fallback="fallback">
                        <Component :is="Component" />
                    </Suspense>
                </Transition>
            </RouterView>
        </div>

        <Notifications ref="notifications" />
    </div>
</template>
