<script setup>
import { inject, computed, ref, watch } from "vue";
import ResourceTableHeader from "./ResourceTableHeader.vue";
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
const { __ } = wp.i18n;

const isSelectable = inject("isSelectable");

const selected = inject("selected");
const items = inject("items");

const updateSelections = (key) => {
    if (key === "all") {
        selected.value.current = !selected.value.all ? true : selected.value.current;
        selected.value.all = !selected.value.all;
    } else if (key === "current") {
        selected.value.current = !selected.value.current;
        selected.value.all = selected.value.current ? false : selected.value.all;
    }

    if (selected.value.current || selected.value.all) {
        selected.value.selected = [];
        for (let i = 0; i < items.value.data.length; i++) {
            selected.value.selected.push(items.value.data[i].id);
        }
    } else if (!selected.value.current) {
        selected.value.selected = [];
    }
};
</script>

<template>
    <thead class="bg-white">
        <tr>
            <ResourceTableHeader role="selection" v-if="isSelectable">
                <div class="-ml-4">
                    <Popover class="relative" v-slot="{ open }">
                        <PopoverButton
                            :class="[
                                open ? 'ring-2' : '',
                                'px-2 bg-white py-1 group focus:outline-none rounded-md inline-flex items-center text-gray-900 ring-offset-2 ring-primary-500 focus:ring-2',
                            ]"
                        >
                            <input
                                type="checkbox"
                                disabled
                                class="w-4 h-4 bg-white border-gray-300 rounded pointer-events-none disabled:opacity-100 text-primary-500 disabled:text-primary-500"
                                :checked="selected.current || selected.all"
                            />
                            <ChevronDownIcon class="w-5 h-5 ml-2" aria-hidden="true" />
                        </PopoverButton>

                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="translate-y-1 opacity-0"
                            enter-to-class="translate-y-0 opacity-100"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="translate-y-0 opacity-100"
                            leave-to-class="translate-y-1 opacity-0"
                        >
                            <PopoverPanel class="absolute z-10 w-screen max-w-sm px-2 mt-3 transform sm:px-0">
                                <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                    <div class="relative px-4 py-3 space-y-3 bg-white">
                                        <label class="flex items-center">
                                            <input
                                                :checked="selected.current"
                                                @click="updateSelections('current')"
                                                type="checkbox"
                                                class="w-4 h-4 bg-white border-gray-300 rounded text-primary-500 focus:ring-primary-500"
                                            />
                                            <div class="ml-3 text-sm font-normal">
                                                {{ __("Select current page", "wptranslation") }} ({{ items.data.length }})
                                            </div>
                                        </label>
                                        <label class="flex items-center">
                                            <input
                                                :checked="selected.all"
                                                @click="updateSelections('all')"
                                                type="checkbox"
                                                class="w-4 h-4 bg-white border-gray-300 rounded text-primary-500 focus:ring-primary-500"
                                            />
                                            <div class="ml-3 text-sm font-normal">{{ __("Select all", "wptranslation") }} ({{ items.total }})</div>
                                        </label>
                                    </div>
                                </div>
                            </PopoverPanel>
                        </transition>
                    </Popover>
                </div>
            </ResourceTableHeader>
            <slot></slot>
        </tr>
    </thead>
</template>
