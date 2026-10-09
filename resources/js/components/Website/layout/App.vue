<template>
    <div id="app">
        <!--  <component :is="isDashboard ? 'HeaderDashboard' : 'HeaderWebsite'"></component>
        <router-view></router-view>
        <component :is="isDashboard ? 'FooterDashboard' : 'FooterWebsite'"></component> -->

        <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
            <!--begin::Page-->
            <div
                class="app-page flex-column flex-column-fluid"
                id="kt_app_page"
            >
                <!--begin::Header-->
                <HeaderWebsite v-if="!isLoginPage"></HeaderWebsite>

                <!--end::Header-->
                <!--begin::Wrapper-->
                <div
                    class="app-wrapper flex-column flex-row-fluid"
                    id="kt_app_wrapper"
                >
                    <!--begin::Wrapper container-->
                    <div class="app-container d-flex">
                        <!--begin::Sidebar-->
                        <Menu v-if="$route.meta.isMenu"></Menu>
                        <!--end::Sidebar-->
                        <!--begin::Main-->
                        <div
                            class="app-main flex-column flex-row-fluid"
                            id="kt_app_main"
                        >
                            <router-view :key="$route.fullPath"></router-view>
                        </div>
                        <!--end:::Main-->
                    </div>
                    <!--end::Wrapper container-->
                    <FooterWebsite v-if="!isLoginPage"></FooterWebsite>
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::App-->

        <!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <i class="ki-outline ki-arrow-up"></i>
        </div>

        <LoginRequiredModal />
    </div>
</template>

<script>
import HeaderWebsite from "./Header.vue";
import FooterWebsite from "./Footer.vue";
import Menu from "./Menu.vue";
import LoginRequiredModal from "../common/LoginRequiredModal.vue";

export default {
    components: {
        HeaderWebsite,
        FooterWebsite,
        Menu,
        LoginRequiredModal,
    },

    mounted() {
        let url = window.location.origin;
        this.loadScript(
            "https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/min/tiny-slider.js"
        );
        this.loadTranslationsOnStart();
    },

    created() {
        this.$store.dispatch("fetchUser");
    },


    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        isLoginPage() {
            return this.$route.meta.hideHeaderFooter;
        },
    },

    methods: {
        loadTranslationsOnStart() {
            this.$store.dispatch("fetchSetting").then(() => {});
        },

        loadScript(src) {
            let script = document.createElement("script");
            script.src = src;
            script.async = true; // جعل التحميل غير متزامن لتحسين الأداء
            document.body.appendChild(script);
        },
    },

    // watch: {
    //   locale(newLocale) {
    //     this.$store.dispatch('loadTranslations', newLocale);
    //   }
    // },
};
</script>

<style type="text/css">
#kt_app_main {
    /* padding: 67px;
        margin-top: -59px; */
}

html[dir="rtl"] .modal-account-close {
       /* left: 24px !important;
        right: 0px !important;*/
}
</style>
