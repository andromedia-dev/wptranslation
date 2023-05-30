import { createApp, onMounted, h } from "vue";
import AppLayout from "@Layouts/AppLayout";
// import NotActivePolylang from "@Pages/NotActivePolylang";
import NProgress from "nprogress";
import VueTippy from "vue-tippy";
import { patchHeadlessuiPortalRoot } from "./patches";
import App from "./App.vue";
import router from "./router";
import notifications from "./notifications";
import axios from "./axios";
import form from "./form";

import "@fontsource/inter/variable.css";
import "../css/app.css";

const el = document.getElementById("polytranslate-app");

NProgress.configure({
    parent: "#nprogress-container",
    showSpinner: false,
});

export const app = createApp({
    setup() {
        patchHeadlessuiPortalRoot("polytranslate-app");
    },

    render(a, b, c) {
        return h(App);
    },
});

app.config.unwrapInjectedRef = true;
app.config.globalProperties.$app = window.polytranslate;
app.config.globalProperties.$asset = (path) => {
    if (path === undefined) {
        path = "";
    }
    return app.config.globalProperties.$app.public_path.replace(/\/$/, "") + "/" + path.replace(/^\//, "");
};
app.config.globalProperties.$admin = (path) => {
    if (path === undefined) {
        path = "";
    }
    return app.config.globalProperties.$app.admin_url.replace(/\/$/, "") + "/" + path.replace(/^\//, "");
};
app.provide("app", app.config.globalProperties.$app);
app.provide("admin", app.config.globalProperties.$admin);

app.component("AppLayout", AppLayout);

app.use(router);
app.use(notifications);
app.use(axios, {
    onError: (err) => {
        const data = err.response.data;
        app.config.globalProperties.$notify({
            title: data.message,
            body: data.error,
            type: "error",
        });
    },
});
app.use(form, {
    prefix: "polytranslate_",
});
app.use(VueTippy, {
    defaultProps: {
        appendTo: el,
        delay: 100,
        animation: "fade",
    },
});
app.mount(el);

export default app;
