<template>
    <div
        id="kt_app_sidebar"
        style="display: inline-block"
        class="app-sidebar flex-column flex-shrink-0 custom_margin"
        data-kt-drawer="true"
        data-kt-drawer-name="app-sidebar"
        data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="auto"
        data-kt-drawer-direction="start"
        data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    >
        <!--begin::Sidebar secondary menu-->
        <div
            class="card py-5 bg-gray-200 liner-gray"
            data-kt-sticky="true"
            data-kt-sticky-name="app-sidebar-menu-sticky"
            data-kt-sticky-offset="{default: false, xl: '500px'}"
            data-kt-sticky-width="250px"
            data-kt-sticky-left="auto"
            data-kt-sticky-top="100px"
            data-kt-sticky-animation="false"
            data-kt-sticky-zindex="95"
        >
            <div
                class="hover-scroll-y mx-2 px-1 px-lg-2"
                data-kt-scroll="true"
                data-kt-scroll-activate="{default: false, lg: true}"
                data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_header, #kt_app_footer"
                data-kt-scroll-wrappers="#kt_app_toolbar"
                data-kt-scroll-offset="5px"
            >
                <div
                    id="kt_app_sidebar_menu"
                    data-kt-menu="true"
                    class="menu menu-sub-indention menu-rounded menu-column menu-active-bg menu-title-gray-600 menu-icon-gray-500 menu-state-primary menu-arrow-gray-500 fw-semibold fs-6"
                >
                    <div class="menu-item px-3 ProfileBox">
                        <div
                            class="menu-content d-flex align-items-center px-3"
                            :class="locale === 'ar' ? 'margin-ar' : 'margin-en'"
                        >
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img
                                    v-if="user && user.logo"
                                    :src="user.logo"
                                    alt="Logo"
                                />
                                <img
                                    v-else
                                    :src="'../../assets_user/media/avatars/blank.png'"
                                    alt="Logo"
                                />
                            </div>
                            <!--end::Avatar-->

                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div
                                    class="fw-bold d-flex align-items-center fs-5"
                                    @click="logout()"
                                >
                                    {{ user ? user.name : "Loading..." }}
                                    <!-- <span
                                        class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"
                                        >Pro</span
                                    > -->
                                </div>

                                <a
                                    href="#"
                                    class="fw-semibold text-muted text-hover-primary fs-7"
                                >
                                    {{ user ? user.email : "Loading..." }}
                                </a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>

                    <!--begin:Menu item-->
                    <!-- <div class="menu-item">
                        <div class="menu-content">
                            <span class="menu-section fs-5 fw-bolder ps-1 py-1"
                                >Home</span
                            >
                        </div>
                    </div> -->
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->

                        <router-link
                            :to="`/${locale}/app/info`"
                            class="menu-link"
                        >
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">{{ t.my_data }}</span>
                        </router-link>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <router-link
                            :to="`/${locale}/app/templates`"
                            class="menu-link"
                        >
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">{{ t.my_template }}</span>
                        </router-link>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="#" @click="logout()">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">{{t.sign_out}}</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    <!--end:Menu item-->
                </div>
            </div>
        </div>
        <!--end::Sidebar secondary menu-->
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
    name: "Header",

    data() {
        return {};
    },

    computed: {
        setting() {
            return this.$store.getters.getSetting;
        },

        user() {
            return this.$store.getters.getUser;
        },

        locale() {
            return this.$route.params.locale;
        },

        t() {
            return this.$store.getters.getTranslations;
        },
    },

    mounted() {
        this.$store.dispatch("fetchSetting");
        this.$store.dispatch("fetchUser");
    },

    methods: {
        logout() {
            localStorage.clear();
            localStorage.removeItem("authToken");
            let url = window.location.origin;
            window.open(url + "/" + this.locale + "/login", "_self");
        },
    },
};
</script>
<style type="text/css">
.custom_margin {
    margin-top: 15px !important;
}

.liner-gray {
    background: linear-gradient(to bottom, white 12%, #f2f2f2 100%);
    position: relative;
    z-index: 10;
}
</style>
