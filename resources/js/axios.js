import axios from "axios";

export default {
    install(app, options) {
        const instance = axios.create({
            baseURL: window.polytranslate?.ajaxurl,
            transformRequest: [
                (data, headers) => {
                    let formData = new FormData();

                    formData.set("_ajax_nonce", window.polytranslate?.nonce);

                    for (const property in data) {
                        if (typeof data[property] === "object" && data[property] !== null) {
                            formData.set(property, JSON.stringify(data[property]));
                        } else {
                            formData.set(property, data[property]);
                        }
                    }

                    return formData;
                },
            ],
        });

        instance.interceptors.response.use(
            (response) => {
                return response;
            },
            (error) => {
                if (error.response.status !== 422) {
                    options.onError(error);
                }

                return Promise.reject(error);
            }
        );

        app.config.globalProperties.$axios = instance;
        app.provide("$axios", app.config.globalProperties.$axios);
    },
};
