<template>
    <div>
        <div class="mb-0" id="home">
            <!--begin::Wrapper-->
            <div
                class="bgi-no-repeat bgi-size-cover bgi-position-x-center bgi-position-y-bottom"
                :style="{ backgroundImage: 'url(/assets/media/landing.png)' }"
            >
                <!--begin::Header-->
                <div
                    class="landing-header"
                    data-kt-sticky="true"
                    data-kt-sticky-name="landing-header"
                    data-kt-sticky-offset="{default: '200px', lg: '300px'}"
                >
                    <div class="container">
                        <div
                            class="d-flex align-items-center justify-content-between borderCostom"
                        >
                            <div class="d-flex align-items-center flex-equal">
                                <button
                                    class="btn btn-icon btn-active-color-primary me-3 d-flex d-lg-none"
                                    id="kt_landing_menu_toggle"
                                >
                                    <i class="ki-duotone ki-abstract-14 fs-2hx">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>

                                <router-link :to="`/${locale}`">
                                    <img
                                        alt="Logo"
                                        src="/assets/media/svg/brand-logos/logo.png"
                                        class="logo-default h-25px h-lg-30px"
                                    />
                                    <img
                                        alt="Logo"
                                        src="/assets/media/svg/brand-logos/logo.png"
                                        class="logo-sticky h-20px h-lg-25px"
                                    />
                                </router-link>
                            </div>

                            <div class="flex-equal text-end ms-1">
                                <ul
                                    class="headMenu menu menu-gray-600 menu-hover-primary fw-semibold order-1 align-items-center mb-3 mb-md-0"
                                >
                                    <li class="menu-item">
                                        <a
                                            target="_blank"
                                            :href="
                                                setting
                                                    ? setting.linked_in
                                                    : 'Loading...'
                                            "
                                            class="mb-6"
                                        >
                                            <img
                                                src="/assets/media/linkedin-logo.png"
                                                class="h-20px"
                                                alt=""
                                            />
                                        </a>
                                    </li>

                                    <li class="menu-item">
                                        <a
                                            target="_blank"
                                            :href="
                                                setting
                                                    ? setting.twitter
                                                    : 'Loading...'
                                            "
                                            class="mb-6"
                                        >
                                            <img
                                                src="/assets/media/twitter.png"
                                                class="h-20px mx-5"
                                                alt=""
                                            />
                                        </a>
                                    </li>

                                    <li class="menu-item">
                                        <a
                                            target="_blank"
                                            :href="
                                                setting
                                                    ? setting.instagram
                                                    : 'Loading...'
                                            "
                                            class="mb-6"
                                        >
                                            <img
                                                src="/assets/media/instagram.png"
                                                class="h-20px"
                                                alt=""
                                            />
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a
                                            @click="switchLocale"
                                            class="ms-5 otherLocale"
                                            style="cursor: pointer"
                                        >
                                            {{ otherLocale }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div class="d-lg-block" id="kt_header_nav_wrapper">
                                <div
                                    class="d-lg-block p-5 p-lg-0"
                                    data-kt-drawer="true"
                                    data-kt-drawer-name="landing-menu"
                                    data-kt-drawer-activate="{default: true, lg: false}"
                                    data-kt-drawer-overlay="true"
                                    data-kt-drawer-width="200px"
                                    data-kt-drawer-direction="start"
                                    data-kt-drawer-toggle="#kt_landing_menu_toggle"
                                    data-kt-swapper="true"
                                    data-kt-swapper-mode="prepend"
                                    data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}"
                                >
                                    <!--begin::Menu-->
                                    <div
                                        class="menu menu-column flex-nowrap menu-rounded menu-lg-row menu-title-gray-900 menu-state-title-primary nav nav-flush fs-5 fw-semibold"
                                        id="kt_landing_menu"
                                    >
                                        <!--begin::Menu item-->
                                        <div class="menu-item">
                                            <!--begin::Menu link-->
                                            <router-link
                                                :to="`/${locale}`"
                                                class="menu-link nav-link active py-3 px-4 px-xxl-6"
                                                data-kt-scroll-toggle="true"
                                                data-kt-drawer-dismiss="true"
                                            >
                                                {{ translations.home }}
                                            </router-link>
                                            <!--end::Menu link-->
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item">
                                            <!--begin::Menu link-->
                                            <router-link
                                                :to="`/${locale}/about-us`"
                                                class="menu-link nav-link py-3 px-4 px-xxl-6"
                                                >{{
                                                    translations.about
                                                }}</router-link
                                            >

                                            <!--end::Menu link-->
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item">
                                            <!--begin::Menu link-->
                                            <a
                                                class="menu-link nav-link py-3 px-4 px-xxl-6"
                                                href="#achievements"
                                                data-kt-scroll-toggle="true"
                                                data-kt-drawer-dismiss="true"
                                            >
                                                {{ translations.templates }}
                                            </a>
                                            <!--end::Menu link-->
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item">
                                            <!--begin::Menu link-->
                                            <router-link
                                                :to="`/${locale}/faq`"
                                                class="menu-link nav-link py-3 px-4 px-xxl-6"
                                            >
                                                {{ translations.FAQS }}
                                            </router-link>
                                            <!--end::Menu link-->
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item">
                                            <!--begin::Menu link-->
                                            <router-link
                                                :to="`/${locale}/contact-us`"
                                                class="menu-link nav-link py-3 px-4 px-xxl-6"
                                            >
                                                {{ translations.contact }}
                                            </router-link>
                                            <!--end::Menu link-->
                                        </div>

                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <!-- <div class="menu-item"> -->
                                        <!--begin::Menu link-->
                                        <!-- <a
                                                class="menu-link nav-link py-3 px-4 px-xxl-6"
                                                href="#pricing"
                                                data-kt-scroll-toggle="true"
                                                data-kt-drawer-dismiss="true"
                                                >Pricing</a
                                            > -->
                                        <!--end::Menu link-->
                                        <!-- </div> -->
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu-->
                                </div>
                            </div>

                            <div
                                class="flex-equal text-end ms-1"
                                v-if="!$route.meta.isMenu"
                            >
                                <!-- زر تسجيل الدخول يظهر فقط إذا المستخدم غير مسجل دخول -->
                                <router-link
                                    v-if="!isLoggedIn"
                                    :to="`/${locale}/login`"
                                    class="btn btn-primary border-rounded"
                                >
                                    {{ translations.signin }}
                                </router-link>

                                <!-- زر لوحة التحكم يظهر فقط إذا المستخدم مسجل دخول -->
                                <router-link
                                    v-else
                                    :to="`/${locale}/app/info`"
                                    class=""
                                >
                                    <i
                                        class="fas fa-user-cog"
                                        style="color: #e4a92a"
                                    ></i>
                                </router-link>
                            </div>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Header-->
                <!--begin::Landing hero-->
                <Slider v-if="$route.meta.isHomePage"></Slider>
                <!--end::Landing hero-->
            </div>
            <Companies v-if="$route.meta.isHomePage"></Companies>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { computed } from "vue"; // ✅ تأكد من استيراد computed

import { mapActions, mapState } from "vuex";
import { api } from "../../../axiosUser";
import { useRoute } from "vue-router";

import Slider from "./Slider.vue";
import Companies from "./Companies.vue";

export default {
    name: "Header",
    components: {
        Slider,
        Companies,
    },

    data() {
        return {
            success: 0,
            translations: window.translations,
            items: [],

            locale: this.$route.params.locale || "en", // أو استخدم القيمة الافتراضية
        };
    },
    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {
            this.fetchItems();
        });

        this.loadTranslations(this.locale);
    },

    created() {
        const savedLocale = localStorage.getItem("locale");

        if (savedLocale) {
            this.locale = savedLocale;
            this.loadTranslations(savedLocale);

            if (savedLocale === "ar") {
                document.dir = "rtl";
                document.body.classList.add("rtl");
            } else {
                document.dir = "ltr";
                document.body.classList.remove("rtl");
            }
        } else {
            // لو ما فيش لغة محفوظة، نحاول نأخذها من الرابط الحالي
            const urlLocale =
                this.$route.params.locale || this.$route.path.split("/")[1];
            this.locale = urlLocale || "en";
            this.loadTranslations(this.locale);
        }
    },

    computed: {
        isLoggedIn() {
            return !!localStorage.getItem("token");
        },

        // locale() {
        //     return this.$route.params.locale || "en";
        // },

        setting() {
            return this.$store.getters.getSetting;
        },

        otherLocale() {
            return this.locale === "en" ? "ar" : "en";
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

        switchLocale() {
            let currentPath = this.$route.path.replace(/^\/(en|ar)/, "");
            if (currentPath === "") {
                currentPath = "/";
            }

            let newLocale = this.otherLocale;
            this.locale = newLocale;

            let updatedPath = `/${newLocale}${currentPath}`;

            if (updatedPath.slice(-1) === "/") {
                updatedPath = updatedPath.slice(0, -1); // إزالة "/" في النهاية إذا كان موجودًا
            }

            this.$router.push(updatedPath);

            this.loadTranslations(newLocale);

            localStorage.setItem("locale", this.locale);

            if (newLocale === "ar") {
                document.dir = "rtl";
                document.body.classList.add("rtl");
            } else {
                document.dir = "ltr";
                document.body.classList.remove("rtl");
            }
        },

        async loadTranslations(locale) {
            let storedTranslations = localStorage.getItem(
                `translations_${locale}`
            );

            if (storedTranslations) {
                this.translations = JSON.parse(storedTranslations);
                return;
            }

            try {
                const response = await api.get("Languages/translations", {
                    params: { locale: locale },
                });

                const data = response.data;

                localStorage.setItem(
                    `translations_${locale}`,
                    JSON.stringify(data)
                );
                this.translations = data;

                console.log(this.translations);
            } catch (error) {
                this.swalFunction("error", error);
                this.isLoading = false;
            }
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

    watch: {
        $route(to) {
            const newLocale = to.params.locale || to.path.split("/")[1];

            if (newLocale !== this.locale) {
                this.locale = newLocale;
                this.loadTranslations(this.locale);

                if (this.locale === "ar") {
                    document.dir = "rtl";
                    document.body.classList.add("rtl");
                } else {
                    document.dir = "ltr";
                    document.body.classList.remove("rtl");
                }
            }
        },
    },
};
</script>
<style type="text/css">
body.rtl {
    direction: rtl;
    text-align: right;
}

body:not(.rtl) {
    direction: ltr;
    text-align: left;
}
</style>
