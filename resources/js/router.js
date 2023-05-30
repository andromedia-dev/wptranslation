import { createRouter, createWebHashHistory } from "vue-router";
import Dashboard from "@/Pages/Dashboard.vue";
import Settings from "@/Pages/Settings.vue";
import NoPolylang from "@/Pages/NoPolylang.vue";
import { app } from "@/app.js";

const routes = [
    {
        name: "dashboard",
        path: "/",
        component: Dashboard,
    },
    {
        name: "settings",
        path: "/settings",
        component: Settings,
    },
    {
        name: "no-polylang",
        path: "/no-polylang",
        component: NoPolylang,
    },
    {
        name: "error",
        path: "/:catchAll(.*)",
        redirect: "/",
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

router.beforeEach(async (to, from) => {
    if (to.name !== "no-polylang" && !app.config.globalProperties.$app.polylang_active) {
        return { name: "no-polylang" };
    } else if (to.name === "no-polylang" && app.config.globalProperties.$app.polylang_active) {
        return { name: "dashboard" };
    }
});

export default router;
