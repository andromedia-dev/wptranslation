<script setup>
import { ref, watch } from "vue";
import { Switch, SwitchGroup, SwitchLabel } from "@headlessui/vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    sr: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const enabled = ref(props.modelValue);

const update = (val) => {
    if (!props.disabled) {
        enabled.value = val;
    }
};

watch(enabled, () => {
    emit("update:modelValue", enabled.value);
});

watch(
    () => props.modelValue,
    (val) => {
        enabled.value = val;
    }
);
</script>

<template>
    <SwitchGroup as="div" class="flex items-center">
        <Switch
            :modelValue="enabled"
            @update:modelValue="update"
            :class="[
                enabled ? 'bg-primary-500' : 'bg-gray-200',
                !disabled ? 'focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 cursor-pointer' : ' cursor-default',
                'relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
            ]"
        >
            <span class="sr-only">{{ sr }}</span>
            <span
                :class="[
                    enabled ? 'translate-x-5' : 'translate-x-0',
                    'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                ]"
            >
                <span
                    :class="[
                        enabled ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in',
                        'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity',
                    ]"
                    aria-hidden="true"
                >
                    <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                        <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span
                    :class="[
                        enabled ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out',
                        'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity',
                    ]"
                    aria-hidden="true"
                >
                    <svg class="w-3 h-3 text-primary-500" fill="currentColor" viewBox="0 0 12 12">
                        <path
                            d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"
                        />
                    </svg>
                </span>
            </span>
        </Switch>

        <SwitchLabel as="span" :class="['ml-3 text-sm font-medium text-gray-900', !disabled ? 'cursor-pointer' : ' cursor-default']">
            <slot></slot>
        </SwitchLabel>
    </SwitchGroup>
</template>
