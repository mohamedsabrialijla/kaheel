<template>
    <div>
        <div class="d-flex flex-column flex-root h-100vh">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Body-->
                <div
                    class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1"
                >
                    <!--begin::Form-->
                    <div
                        class="d-flex flex-center flex-column flex-lg-row-fluid"
                    >
                        <router-link
                            :to="`/${locale}`"
                            style="margin: 0 auto 1rem"
                        >
                            <img
                                v-if="locale == 'ar'"
                                alt="Logo"
                                style="height: 90px !important"
                                src="/assets/media/svg/brand-logos/logo.png"
                                class="logo-default h-25px h-lg-30px"
                            />

                            <img
                                v-else
                                alt="Logo"
                                style="height: 90px !important"
                                src="/assets/media/svg/brand-logos/logo.png"
                                class="logo-default h-25px h-lg-30px"
                            />
                        </router-link>
                        <!--begin::Wrapper-->
                        <div class="w-lg-500px p-10">
                            <!--begin::Form-->
                            <form
                                class="form w-100"
                                novalidate="novalidate"
                                id="kt_sign_in_form"
                                @submitranslations?.prevent="forget"
                            >
                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <!--begin::Title-->
                                    <h1 class="text-gray-900 fw-bolder mb-3">
                                        {{ translations?.forget_password }}
                                    </h1>
                                </div>

                                <div class="fv-row mb-8">
                                    <MobileFlag
                                        v-if="countries.length"
                                        v-model="formData.mobile"
                                        :items="countries"
                                        initialCountry="sa"
                                        placeholder="5xxxxxxxxx"
                                        class="col-md-12"
                                    />
                                </div>

                                <!-- <div
                                    class="d-flex flex-wrap gap-3 fs-base fw-semibold mb-8"
                                ></div> -->

                                <div class="d-grid mb-10">
                                    <button @click="forget()"
                                        type="submit"
                                        id="kt_sign_in_submit"
                                        class="btn btn-primary indicator"
                                        :disabled="isLoading"
                                    >
                                        <!--begin::Indicator label-->
                                        <span
                                            v-if="!isLoading"
                                            class="indicator-label"
                                        >
                                            {{ translations?.send }}</span
                                        >
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span
                                            v-else
                                            class="indicator-progress"
                                            :style="{
                                                display: isLoading
                                                    ? 'inline-flex'
                                                    : 'none',
                                            }"
                                        >
                                            {{ translations?.Please_wait }}

                                            <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"
                                            ></span
                                        ></span>
                                        <!--end::Indicator progress-->
                                    </button>
                                </div>
                                <!--end::Submit button-->
                                <!--begin::Sign up-->
                                <div
                                    class="text-gray-900 text-center fw-semibold fs-6"
                                >
                                    <router-link
                                        :to="`/${locale}/login`"
                                        class="link-primary"
                                        >{{ translations?.signin }}</router-link
                                    >
                                </div>

                                <div
                                    class="text-gray-900 text-center fw-semibold fs-6"
                                >
                                    <!--  <router-link
                                        :to="`/${locale}`"
                                        class="link-primary"
                                        >{{translations?.home}}</router-link
                                    > -->
                                </div>

                                <!--end::Sign up-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                    <!--begin::Footer-->

                    <!--end::Footer-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Authentication - Sign-in-->
        </div>
    </div>
</template>

<script>
import axios from "axios";
import MobileFlag from "./MobileFlag.vue";

export default {
    components: { MobileFlag },
    data() {
        return {
            errors: [],
            isLoading: false,
            countries:[],
            formData: {
                phone: null,
            },
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting");
        this.fetchItemsCountry();
    },

    computed: {
        setting() {
            return this.$store.getters.getSetting;
        },

        locale() {
            return this.$route.params.locale;
        },
        translations() {
            return this.$store.getters.getTranslations || {};
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

        async fetchItemsCountry() {
            this.isLoading = true; 
            await axios
                .get("basic/getCountries"
                )
                .then((response) => {
                    this.countries = response.data.items;
                    this.isLoading = false;
                    this.languages = this.$store.state.languages;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async forget() {
            this.isLoading = true;
            try {
                const response = await axios.post("/auth/forget",this.formData);

                if (response.data.code == 200) {
                    this.isLoading = false;
                    localStorage.setItem('mobile',this.formData.mobile)
                    this.$router.push({
                        path: `/${this.$route.params.locale}/otp-verify`,
                        // query: { mobile: this.formData.mobile }
                    });
                } else {
                    this.isLoading = false;
                    this.swalFunction("info", response.data.message);
                }
            } catch (error) {
                this.isLoading = false;
                // console.error("FORGET ERROR:", error.response || error.message || error);
                this.swalFunction("error", error.response?.data?.message || "Unexpected error");
            }
            
        },
    },
};
</script>
