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
                                @submitranslations.prevent="login"
                            >
                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <!--begin::Title-->
                                    <h1 class="text-gray-900 fw-bolder mb-3">
                                        {{ translations.signin }}
                                    </h1>
                                </div>
                                <!--begin::Tabs-->
                                <div
                                    class="d-flex justify-content-between mb-6 login-tabs"
                                >
                                    <div
                                        :class="[
                                            'login-tab',
                                            loginType === 'mobile'
                                                ? 'active'
                                                : '',
                                        ]"
                                        @click="loginType = 'mobile'"
                                    >
                                        {{ translations.mobile || "mobile Number" }}
                                    </div>
                                    <div
                                        :class="[
                                            'login-tab',
                                            loginType === 'email'
                                                ? 'active'
                                                : '',
                                        ]"
                                        @click="loginType = 'email'"
                                    >
                                        {{ translations.email || "Email" }}
                                    </div>
                                </div>
                                <!--end::Tabs-->
                                <!--begin::Input group=--> 
                                <div
                                    class="fv-row mb-8"
                                    v-if="loginType === 'mobile'"
                                > 
                                    <MobileFlag
                                        v-if="countries.length"
                                        v-model="formData.mobile"
                                        :items="countries"
                                        initialCountry="sa"
                                        placeholder="5xxxxxxxxx"
                                        class="col-md-12"
                                    />
                                </div>
                                <div
                                    class="fv-row mb-8"
                                    v-if="loginType === 'email'"
                                > 
                                    <input
                                        type="text"
                                        :placeholder="translations.email"
                                        v-model="formData.email"
                                        name="email"
                                        autocomplete="off"
                                        class="form-control bg-transparent"
                                    />
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-3 position-relative">
                                    <input
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        :placeholder="translations.password"
                                        v-model="formData.password"
                                        name="password"
                                        autocomplete="off"
                                        class="form-control bg-transparent"
                                    />
                                    <span
                                        class="password-toggle-eye"
                                        @click="showPassword = !showPassword"
                                        :title="
                                            showPassword
                                                ? translations.hide_password || 'Hide'
                                                : translations.show_password || 'Show'
                                        "
                                    >
                                        <svg
                                            v-if="!showPassword"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="22"
                                            height="22"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke="#888"
                                                stroke-width="2"
                                                d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"
                                            />
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="3"
                                                stroke="#888"
                                                stroke-width="2"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="22"
                                            height="22"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke="#888"
                                                stroke-width="2"
                                                d="M3 3l18 18M1 12s4-7 11-7c2.5 0 4.7.7 6.5 1.8M23 12s-4 7-11 7c-2.5 0-4.7-.7-6.5-1.8"
                                            />
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="3"
                                                stroke="#888"
                                                stroke-width="2"
                                            />
                                        </svg>
                                    </span>
                                </div>
                                <!--end::Input group=-->
                                <!--begin::Wrapper-->
                                <div
                                    class="d-flex flex-wrap gap-3 fs-base fw-semibold mb-8"
                                >
                                    <router-link
                                        :to="`/${locale}/forget-password`"
                                        href="#"
                                        class="text-gray-900"
                                        >{{ translations.forget_password }} ?
                                    </router-link>
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Submit button-->
                                <div class="d-grid mb-10">
                                    <button @click="login()"
                                        type="submit"
                                        id="kt_sign_in_submit"
                                        class="btn btn-primary indicator"
                                        :disabled="isLoading"
                                    >
                                        <span
                                            v-if="!isLoading"
                                            class="indicator-label"
                                        >
                                            {{ translations.login || 'Login' }}</span
                                        >
                                        <span
                                            v-else
                                            class="indicator-progress"
                                            :style="{
                                                display: isLoading
                                                    ? 'inline-flex'
                                                    : 'none',
                                            }"
                                        >
                                            {{ translations.Please_wait }}

                                            <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"
                                            ></span
                                        ></span>
                                    </button>
                                </div>
                                <!--end::Submit button-->
                                <!--begin::Sign up-->
                                <div
                                    class="text-gray-900 text-center fw-semibold fs-6"
                                >
                                    {{ translations.Dont_have_an_account_yet }}

                                    <router-link
                                        :to="`/${locale}/signUp`"
                                        class="link-primary"
                                        >{{ translations.signup }}</router-link
                                    >
                                </div>

                                <div
                                    class="text-gray-900 text-center fw-semibold fs-6"
                                >
                                    <router-link
                                        :to="`/${locale}`"
                                        class="link-primary"
                                        >{{ translations.home }}</router-link
                                    >
                                </div>

                                <!--end::Sign up-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Body-->
                <!--begin::Aside-->

                <!--end::Aside-->
            </div>
            <!--end::Authentication - Sign-in-->
        </div>
    </div>
</template>

<script>
import MobileFlag from "./MobileFlag.vue";
import { authApi, syncAuthToken } from "@/axiosUser";

export default {
    components: { MobileFlag },
    data() {
        return {
            errors: [],
            isLoading: false,
            loginType: "mobile",
            formData: {
                email: null,
                mobile: null,
                password: null,
            },
            countries:[],
            showPassword: false,
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
            await authApi({
                method: "GET",
                url: "basic/getCountries"
            })
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


        async login() {
            localStorage.removeItem("authToken");
            localStorage.removeItem("token");
            syncAuthToken();
            this.isLoading = true;
            try {
                let payload = {
                    password: this.formData.password,
                    flag: this.loginType,
                };
                if (this.loginType === "mobile") {
                    payload.mobile = this.formData.mobile;
                } else {
                    payload.email_or_username = this.formData.email;
                }
                const response = await authApi({
                    method: "POST",
                    url: "auth/login",
                    data: payload,
                });
                const data = response.data || {};
                const ok = data.code == 200 || data.status === true;
                if (ok && data.items?.token) {
                    const userData = { ...data.items };
                    const token = userData.token;

                    localStorage.setItem("authToken", token);
                    localStorage.setItem("token", token);
                    syncAuthToken();

                    userData.userType = "user";
                    this.$store.commit("setUser", userData);
                    localStorage.setItem("user", JSON.stringify(userData));

                    this.isLoading = false;
                    this.$store.dispatch("fetchUser", { redirectOnFail: false }).catch(() => {});
                    this.$router.push("/" + this.$route.params.locale);
                } else if (ok) {
                    this.isLoading = false;
                    this.swalFunction("error", "Login succeeded but token was missing. Contact support.");
                } else {
                    this.isLoading = false;
                    this.swalFunction("info", data.message);
                }
            } catch (error) {
                this.isLoading = false;
                this.swalFunction("error", error.response?.data?.message || "Unexpected error");
            }
        },
    },
};
</script>

<style scoped>
.login-tabs {
    border-bottom: 1.5px solid #eee;
    margin-bottom: 1.5rem;
}
.login-tab {
    font-weight: 600;
    font-size: 1.1rem;
    color: #222;
    padding: 0.5rem 1.2rem 0.7rem 1.2rem;
    cursor: pointer;
    border-bottom: 2.5px solid transparent;
    transition: border 0.18s, color 0.18s;
}
.login-tab.active {
    color: #222;
    border-bottom: 2.5px solid #e5aa2a;
    background: #faf7ee;
}
.password-toggle-eye {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 2;
    padding: 2px 4px;
    background: none;
    border: none;
    outline: none;
    display: flex;
    align-items: center;
}

/* إذا كانت الصفحة LTR */
html[dir="ltr"] .password-toggle-eye {
    right: 1.2rem;
    left: auto;
}

/* إذا كانت الصفحة RTL */
html[dir="rtl"] .password-toggle-eye {
    left: 1.2rem;
    right: auto;
}

</style>
