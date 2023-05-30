<script>
import { RouterLink } from "vue-router";

export default {
    name: "AppLink",
    inheritAttrs: false,

    props: {
        // add @ts-ignore if using TypeScript
        ...RouterLink.props,
        inactiveClass: String,
    },

    computed: {
        isExternalLink() {
            return typeof this.to === "string" && this.to.startsWith("http");
        },
    },
};
</script>

<template>
    <a v-if="isExternalLink" v-bind="$attrs" :href="to" :class="inactiveClass">
        <slot />
    </a>
    <RouterLink v-else v-bind="$props" custom v-slot="{ isActive, href, navigate }">
        <a v-bind="$attrs" :href="href" @click="navigate" :class="isActive ? activeClass : inactiveClass" :aria-current="isActive ? 'page' : undefined">
            <slot v-bind="{ isActive, href, navigate }" />
        </a>
    </RouterLink>
</template>
