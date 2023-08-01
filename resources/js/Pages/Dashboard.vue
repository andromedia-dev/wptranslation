<script setup>
import { ref, reactive, watch, inject, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import ResourceTable from "@Components/Table/ResourceTable.vue";
import ResourceTableHeaders from "@Components/Table/ResourceTableHeaders.vue";
import ResourceTableHeader from "@Components/Table/ResourceTableHeader.vue";
import ResourceTableBody from "@Components/Table/ResourceTableBody.vue";
import ResourceTableBodyRow from "@Components/Table/ResourceTableBodyRow.vue";
import ResourceTableBodyColumn from "@Components/Table/ResourceTableBodyColumn.vue";
import SearchInput from "@Components/Table/Search/SearchInput.vue";
import FiltersMenu from "@Components/Table/Filters/FiltersMenu.vue";
import SelectFilter from "@Components/Table/Filters/SelectFilter.vue";
import Pagination from "@Components/Pagination.vue";
import TranslateModal from "@Components/TranslateModal.vue";
import LanguagesRow from "@Components/LanguagesRow.vue";
import NProgress from "nprogress";
import { LanguageIcon } from "@heroicons/vue/20/solid";
import _values from "lodash/values";
import _remove from "lodash/remove";
const { __ } = wp.i18n;

const $form = inject("$form");

const router = useRouter();
const route = useRoute();

const translateModal = ref();
const processing = ref(false);

const posts = ref();
const selectedPosts = reactive({
    current: false,
    all: false,
    selected: [],
});
const [languages, users, categories, productCategories, postTypes] = await Promise.all([
    $form().submit("languages"),
    $form().submit("wp_users"),
    $form().submit("wp_categories"),
    $form().submit("wp_product_categories"),
    $form().submit("wp_post_types"),
]);

const form = $form({
    page: route.query.page ? parseInt(route.query.page) : 1,
    sort_order: route.query?.sort_order,
    sort_direction: route.query?.sort_direction,
    search: route.query?.search,
    post_type: route.query?.post_type,
    category: route.query?.category,
    product_category: route.query?.product_category,
    author: route.query?.author,
});

// computed
const translatableLanguages = computed(() => {
    return _remove(_values(languages.all), (l) => {
        return l.slug !== languages.default;
    });
});

const selectedItemsLength = computed(() => {
    if (selectedPosts.all) {
        return posts.value.total;
    } else if (selectedPosts.current) {
        return posts.value.data.length;
    }

    return selectedPosts.selected.length;
});

// methods
const fetch = async () => {
    NProgress.start();
    processing.value = true;
    posts.value = await form.submit("translate_get_posts");
    selectedPosts.current = false;
    selectedPosts.all = false;
    selectedPosts.selected = [];
    processing.value = false;
    NProgress.done();
};

const resetFilters = () => {
    form.post_type = undefined;
    form.author = undefined;
    form.category = undefined;
    form.lang = undefined;
};

// watchers
watch(
    () => form.data(),
    async () => {
        router.push({ name: "dashboard", query: form.data() });
        await fetch();
    },
    { deep: true }
);

watch(
    () => route.query,
    (params) => {
        if (params.page) {
            form.page = parseInt(params.page);
        }
    }
);

// ---
await fetch();
</script>

<template>
    <div class="px-4 mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center space-x-3 md:justify-between md:space-x-0">
            <SearchInput class="md:max-w-xs" v-model.lazy="form.search" />
            <div class="flex items-center space-x-3">
                <button
                    :disabled="selectedItemsLength === 0"
                    @click="translateModal.show()"
                    :class="[
                        selectedItemsLength === 0 ? 'opacity-50' : 'hover:bg-gray-50',
                        'inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
                    ]"
                >
                    <LanguageIcon class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" />
                    {{ __("Translate", "wptranslation") }}
                </button>
                <FiltersMenu @reset="resetFilters">
                    <SelectFilter v-model="form.post_type" :items="postTypes" prop-key="id" prop-title="name">
                        {{ __("Post type", "wptranslation") }}
                    </SelectFilter>
                    <SelectFilter v-model="form.category" :items="categories" prop-key="term_id" prop-title="name">
                        {{ __("Category", "wptranslation") }}
                    </SelectFilter>
                    <SelectFilter
                        v-if="productCategories.length > 0"
                        v-model="form.product_category"
                        :items="productCategories"
                        prop-key="term_id"
                        prop-title="name"
                    >
                        {{ __("Product Category", "wptranslation") }}
                    </SelectFilter>
                    <SelectFilter v-model="form.author" :items="users" prop-key="id" prop-title="name">
                        {{ __("Author", "wptranslation") }}
                    </SelectFilter>
                </FiltersMenu>
            </div>
        </div>

        <ResourceTable
            class="mt-4"
            v-model:selected="selectedPosts"
            :sort="{
                order: form.sort_order,
                direction: form.sort_direction,
            }"
            @update:sort="
                (sort) => {
                    form.sort_order = sort.order;
                    form.sort_direction = sort.direction;
                }
            "
            :items="posts"
        >
            <ResourceTableHeaders>
                <ResourceTableHeader :sortable="true" column="title">
                    {{ __("Title", "wptranslation") }}
                </ResourceTableHeader>
                <ResourceTableHeader :sortable="false" column="author" class="hidden xl:table-cell">
                    {{ __("Author", "wptranslation") }}
                </ResourceTableHeader>
                <ResourceTableHeader :sortable="false" column="categories" class="hidden xl:table-cell">
                    {{ __("Categories", "wptranslation") }}
                </ResourceTableHeader>
                <ResourceTableHeader :sortable="false" column="tags" class="hidden xl:table-cell">
                    {{ __("Tags", "wptranslation") }}
                </ResourceTableHeader>
                <ResourceTableHeader :sortable="false" column="languages">
                    <LanguagesRow :languages="_values(languages.all)" />
                </ResourceTableHeader>
                <ResourceTableHeader :sortable="true" column="date" class="hidden xl:table-cell">
                    {{ __("Date", "wptranslation") }}
                </ResourceTableHeader>
            </ResourceTableHeaders>
            <ResourceTableBody>
                <ResourceTableBodyRow v-for="post in posts.data" :key="post.id" :item="post">
                    <ResourceTableBodyColumn>
                        <div class="whitespace-normal">{{ post.post_title }}</div></ResourceTableBodyColumn
                    >
                    <ResourceTableBodyColumn class="hidden xl:table-cell"> {{ post.post_author?.name }} </ResourceTableBodyColumn>
                    <ResourceTableBodyColumn class="hidden xl:table-cell"> {{ post.categories.slice(0, 3).join(", ") }} </ResourceTableBodyColumn>
                    <ResourceTableBodyColumn class="hidden xl:table-cell"> {{ post.tags.slice(0, 5).join(", ") }} </ResourceTableBodyColumn>
                    <ResourceTableBodyColumn>
                        <LanguagesRow :languages="_values(languages.all)" :active-Languages="Object.keys(post.translations)" />
                    </ResourceTableBodyColumn>
                    <ResourceTableBodyColumn class="hidden xl:table-cell">
                        <div>
                            {{ __("Published", "wptranslation") }}
                        </div>
                        <div>{{ post.post_date }}</div>
                    </ResourceTableBodyColumn>
                </ResourceTableBodyRow>
            </ResourceTableBody>
        </ResourceTable>

        <Pagination v-if="posts.last_page > 1" class="mt-4" v-model="form.page" :items="posts" :disabled="processing" />

        <TranslateModal ref="translateModal" :languages="translatableLanguages" :filters="form.data()" :selected="selectedPosts" />
    </div>
</template>
