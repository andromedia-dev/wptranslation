<script setup>
import { ref, reactive, watch, provide } from "vue";
import _cloneDeep from "lodash/cloneDeep";
import _isEqual from "lodash/isEqual";

const props = defineProps(["selected", "items", "sort"]);
const emit = defineEmits(["update:selected", "update:sort"]);

const _items = ref(props.items);

const isSelectable = props.selected !== undefined && props.items !== undefined;

const selected = ref(props.selected);

const sort = reactive(props.sort);

watch(
    () => props.selected,
    () => {
        if (!_isEqual(props.selected, selected.value)) {
            selected.value = _cloneDeep(props.selected);
        }
    },
    { deep: true }
);

watch(
    () => props.sort,
    () => {
        if (!_isEqual(props.sort, sort)) {
            const c = _cloneDeep(props.sort);
            sort.order = c.order;
            sort.direction = c.direction;
        }
    }
);

watch(sort, () => {
    emit("update:sort", sort);
});

watch(
    () => props.items,
    (val) => {
        _items.value = val;
    }
);

provide("isSelectable", isSelectable);
provide("selected", selected);
provide("sort", sort);
provide("items", _items);
</script>

<template>
    <div class="flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="relative overflow-hidden border-gray-300 shadow border-y md:border-x md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 table-fixed">
                        <slot></slot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
