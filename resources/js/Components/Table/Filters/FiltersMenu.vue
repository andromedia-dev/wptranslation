<script setup>
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { FunnelIcon } from "@heroicons/vue/20/solid";
const { __ } = wp.i18n;

defineEmits(["reset"]);
</script>

<template>
    <Popover class="relative" v-slot="{ open }">
        <PopoverButton
            :class="[
                open ? 'ring-2' : '',
                'inline-flex text-gray-900 bg-white items-center px-4 py-2 text-sm font-medium border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 outline-none focus:ring-2 ring-primary-500 ring-offset-2',
            ]"
        >
            <FunnelIcon class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" />
            {{ __("Filters", "polytranslate") }}
        </PopoverButton>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <PopoverPanel class="absolute right-0 z-20 w-screen max-w-xs px-2 mt-3 transform sm:px-0">
                <div class="overflow-hidden bg-white border border-gray-300 rounded-lg shadow-lg">
                    <div class="p-4 space-y-3">
                        <slot></slot>
                    </div>
                    <div class="flex justify-end px-4 py-2 border-t border-gray-300">
                        <button @click="$emit('reset')" class="text-xs text-right text-primary-500 hover:underline">
                            {{ __("Reset filters", "polytranslate") }}
                        </button>
                    </div>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>
