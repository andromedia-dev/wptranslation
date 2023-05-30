let component = null;

export default {
    install(app, options) {
        app.config.globalProperties.$notify = (item) => {
            component.value.notify(item);
        };

        app.provide("$notify", app.config.globalProperties.$notify);
        app.provide("setNotificationComponent", (el) => {
            component = el;
        });
    },
};
