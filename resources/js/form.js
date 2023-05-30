import _isEqual from "lodash/isEqual";
import _cloneDeep from "lodash/cloneDeep";
import { reactive, watch } from "vue";

export default {
    install(app, options) {
        app.config.globalProperties.$form = (data) => {
            if (data === undefined) {
                data = {};
            }
            let defaults = _cloneDeep(data);
            let cancelToken = null;
            let recentlySuccessfulTimeoutId = null;
            let prefix = options.prefix !== undefined ? options.prefix : "";

            let form = reactive({
                ...data,
                isDirty: false,
                errors: {},
                defaults: defaults,
                hasErrors: false,
                processing: false,
                wasSuccessful: false,
                recentlySuccessful: false,
                data() {
                    return Object.keys(data).reduce((carry, key) => {
                        carry[key] = this[key];
                        return carry;
                    }, {});
                },
                reset(...fields) {
                    let clonedDefaults = _cloneDeep(defaults);
                    if (fields.length === 0) {
                        Object.assign(this, clonedDefaults);
                    } else {
                        Object.assign(
                            this,
                            Object.keys(clonedDefaults)
                                .filter((key) => fields.includes(key))
                                .reduce((carry, key) => {
                                    carry[key] = clonedDefaults[key];
                                    return carry;
                                }, {})
                        );
                    }

                    return this;
                },
                setErrors(errors) {
                    Object.assign(this.errors, errors);

                    this.hasErrors = Object.keys(this.errors).length > 0;

                    return this;
                },
                clearErrors(...fields) {
                    this.errors = Object.keys(this.errors).reduce(
                        (carry, field) => ({
                            ...carry,
                            ...(fields.length > 0 && !fields.includes(field) ? { [field]: this.errors[field] } : {}),
                        }),
                        {}
                    );

                    this.hasErrors = Object.keys(this.errors).length > 0;

                    return this;
                },
                submit(key, options) {
                    if (options === undefined) {
                        options = {};
                    }
                    const data = this.data();

                    let params = {
                        action: prefix + key,
                    };
                    if (Object.keys(data).length > 0) {
                        params.payload = data;
                    }

                    const _options = {
                        ...options,
                    };

                    this.processing = true;
                    this.wasSuccessful = false;
                    this.recentlySuccessful = false;
                    clearTimeout(recentlySuccessfulTimeoutId);

                    return new Promise((resolve, reject) => {
                        app.config.globalProperties
                            .$axios({
                                method: "post",
                                data: params,
                            })
                            .then((response) => {
                                this.processing = false;
                                this.clearErrors();
                                this.wasSuccessful = true;
                                this.recentlySuccessful = true;
                                recentlySuccessfulTimeoutId = setTimeout(() => (this.recentlySuccessful = false), 2000);
                                const onSuccess = options.onSuccess ? options.onSuccess(response?.data) : null;
                                defaults = _cloneDeep(this.data());
                                resolve(response?.data);
                                this.isDirty = false;
                                return onSuccess;
                            })
                            .catch((errors) => {
                                this.processing = false;
                                this.clearErrors();
                                if (errors.response.data?.errors) {
                                    this.setErrors(errors.response.data.errors);
                                    reject(errors.response.data.errors);

                                    if (options.onError) {
                                        return options.onError(errors.response.data.errors);
                                    }
                                } else {
                                    reject(errors.response.data.error);

                                    if (options.onError) {
                                        return options.onError(errors.response.data.error);
                                    }
                                }
                            });
                    });
                },
                cancel() {
                    if (cancelToken) {
                        cancelToken.cancel();
                    }
                },
            });

            watch(
                form,
                (newValue) => {
                    form.isDirty = !_isEqual(form.data(), defaults);
                },
                { immediate: true, deep: true }
            );

            return form;
        };
        app.provide("$form", app.config.globalProperties.$form);
    },
};
