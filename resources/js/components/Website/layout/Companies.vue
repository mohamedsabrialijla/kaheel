<template>
    <div>
        <!--begin::Wrapper-->

        <div class="container">
            <div class="text-center mb-10">
                <!--begin::Title-->
                <h3
                    class="fs-2hx text-gray-900 mb-5"
                    id="how-it-works"
                    data-kt-scroll-offset="{default: 100, lg: 150}"
                >
                    <span class="vectorSpan"
                        >{{ t.trust }}
                        <img
                            src="/assets/media/vector.png"
                            class="vectorImg"
                            alt="vectorImg"
                        />
                    </span>
                    {{ t.leading_companies }}
                </h3>
            </div>
            <div class="row">
                <div
                    v-for="(item, index) in items"
                    :key="index"
                    class="col text-center"
                    data-bs-toggle="tooltip"
                    title="Fujifilm"
                >
                    <img :src="item.logo" class="mh-40px mh-lg-50px" alt="" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from "vuex";
import { api } from "../../../axiosUser";

export default {
    name: "Companies",
    components: {},
    data() {
        return {
            success: 0,
            // translations: window.translations,
            items: [],
        };
    },
    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {
            this.fetchItems();
        });
    },

    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        setting() {
            return this.$store.getters.getSetting;
        },

        otherLocale() {
            return this.locale === "en" ? "ar" : "en";
        },

        t() {
          return this.$store.getters.getTranslations;
        },
    },

    methods: {
        swalFunction(type, text) {
            Swal.fire({
                text: text,
                icon: type,
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        },

   
        async fetchItems(page, query = "") {
            this.isLoading = true;
            await api
                .get("ItemsCategories/getAllItems", {
                    params: {
                        pagination: 0,
                        page: page,
                        type: "company",
                    },
                })
                .then((response) => {
                    this.items = response.data.items;
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);

                    this.isLoading = false;
                });
        },
    },
};
</script>
