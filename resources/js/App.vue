<script setup>
import { computed } from "vue";
import { useRoute, RouterView } from "vue-router";

const route = useRoute();
const layout = computed(() => {
    if (route.name) {
        return (route.meta.layout || "app") + "-layout";
    }
});
</script>

<template>
    <div class="w-full h-full font-sans text-base text-gray-900 bg-white min-h-wpcontent">
        <div id="nprogress-container" class="sticky top-0 z-50"></div>
        <component v-if="layout" :is="layout">
            <RouterView />
        </component>
    </div>
</template>

<style lang="css">
.wptranslation-app #nprogress {
    --nprogress-color: var(--color-primary-500);
}
</style>
