<template>
    <div>
        <div class="d-flex flex-column flex-root h-100vh">
            <!--begin::Authentication - Sign-up -->
            <div
                class="d-flex flex-column flex-lg-row flex-column-fluid"
                style="background: #fafbfd"
            >
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
                        <div class="w-lg-600px p-10">
                            <!--begin::Form-->
                            <form class="form w-100">
                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <!--begin::Title-->
                                    <h1 class="text-gray-900 fw-bolder mb-3">
                                        {{ translations?.signup }}
                                    </h1>
                                    <!--end::Title-->
                                    <!--begin::Subtitle-->
                                    <!-- <div class="text-gray-500 fw-semibold fs-6">
                                        Your Social Campaigns
                                    </div> -->
                                    <!--end::Subtitle=-->
                                </div>
                                <!--begin::Heading-->
                                <!--begin::Login options-->
                                <div class="row g-3 mb-9"></div>

                                <!-- Name -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">{{ nameFieldLabel }}</label>
                                    <input
                                        inputmode="text"
                                        :placeholder="nameFieldLabel"
                                        v-model="formData.name"
                                        name="name"
                                        autocomplete="name"
                                        class="form-control bg-transparent"
                                    />
                                </div>

                                <!-- Email -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">{{translations?.email}}</label>
                                    <input
                                        type="text"
                                        :placeholder="translations?.email"
                                        v-model="formData.email"
                                        name="email"
                                        autocomplete="off"
                                        class="form-control bg-transparent"
                                    />
                                </div>

                                <!-- Mobile -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">{{translations?.mobile}}</label>
                                   <MobileFlag
                                        v-if="countries.length"
                                        v-model="formData.mobile"
                                        :items="countries"
                                        initialCountry="sa"
                                        placeholder="5xxxxxxxxx"
                                        class="col-md-12"
                                    />
                                </div>

                                <!-- Gender -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">{{translations?.gender}}</label>
                                    <div class="d-flex gap-4">
                                        <label
                                            class="form-check form-check-inline"
                                        > 
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                value="male"
                                                v-model="formData.gender"
                                                required
                                            />
                                            <span class="form-check-label"
                                                >{{translations?.mail}}</span
                                            >
                                        </label>
                                        <label
                                            class="form-check form-check-inline"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                value="female"
                                                v-model="formData.gender"
                                                required
                                            />
                                            <span class="form-check-label"
                                                >{{translations?.femail}}</span
                                            >
                                        </label>
                                    </div>
                                </div>

                                <!-- Country -->
                                <!-- <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">Country</label>
                                    <select
                                        class="form-control bg-white country-select"
                                        v-model="formData.country"
                                        required
                                    >
                                        <option value="" disabled>
                                            Select Country
                                        </option>

                                        <option :value="country.id" v-for="(country,index) in countries">{{country.name}}</option>
                                    </select>
                                </div> -->

 
                                <!-- City -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">{{translations?.city}}</label>
                                    <select @click="fetchItemsCity"
                                        class="form-control bg-white country-select"
                                        v-model="formData.city_id"
                                        required
                                    >
                                        <option value="" disabled>
                                            {{translations?.city}}
                                        </option>
                                        <option
                                            :value="city.id"
                                            v-for="city in cities"
                                            :key="city.id"
                                        >
                                            {{ city.name }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Password -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block">{{translations?.password}}</label>
                                    <div class="position-relative mb-3">
                                        <div class="position-relative">
                                          <input
                                            class="form-control bg-transparent"
                                            :type="passwordVisible ? 'text' : 'password'"
                                            :placeholder="translations?.password"
                                            v-model="formData.password"
                                            @input="validatePassword"
                                          />
                                          <span
                                            class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-2"
                                            @click="togglePasswordVisibility('passwordVisible')"
                                          >
                                            <i v-if="!passwordVisible" class="fa fa-eye-slash"></i>
                                            <i v-else class="fa fa-eye"></i>
                                          </span>
                                        </div>

                                    </div>
                                    <div
                                        v-if="passwordError"
                                        class="text-danger"
                                    >
                                        {{ passwordError }}
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="fv-row mb-8">
                                    <label class="mb-2 d-block"
                                        >{{translations?.confirm_password}}</label
                                    >
                                    <div class="position-relative mb-3">
                                        <div class="position-relative">
                                          <input
                                            class="form-control bg-transparent"
                                            :type="confirmPasswordVisible ? 'text' : 'password'"
                                            :placeholder="translations?.confirm_password"
                                            v-model="formData.confirmPassword"
                                            @input="validateConfirmPassword"
                                          />
                                          <span
                                            class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-2"
                                            @click="togglePasswordVisibility('confirmPasswordVisible')"
                                          >
                                            <i v-if="!confirmPasswordVisible" class="fa fa-eye-slash"></i>
                                            <i v-else class="fa fa-eye"></i>
                                          </span>
                                        </div>

                                    </div>
                                    <div
                                        v-if="confirmPasswordError"
                                        class="text-danger"
                                    >
                                        {{ confirmPasswordError }}
                                    </div>
                                </div>

                                <!--begin::Accept-->
                                <div class="fv-row mb-8">
                                    <label class="form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="toc"
                                            value="1"
                                            v-model="formData.terms"
                                        />
                                        <span
                                            class="form-check-label fw-semibold text-gray-700 fs-base ms-1"
                                        >
                                            {{ translations?.I_Accept_the }}
                                            <a
                                                @click="getModale()"
                                                href="#"
                                                class="ms-1 link-primary"
                                                >{{ translations?.terms }}
                                            </a></span
                                        >
                                    </label>
                                </div>
                                <!--end::Accept-->
                                <!--begin::Submit button-->
                                <div class="d-grid mb-10">
                                    <button @click="Register()"
                                        type="submit"
                                        id="kt_sign_up_submit"
                                        class="btn btn-primary indicator"
                                        :disabled="isLoading"
                                    >
                                        <!--begin::Indicator label-->
                                        <span
                                            v-if="!isLoading"
                                            class="indicator-label"
                                        >
                                            {{ translations?.signup }}
                                        </span>
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
                                            ></span>
                                        </span>
                                        <!--end::Indicator progress-->
                                    </button>
                                </div>
                                <!--end::Submit button-->
                                <!--begin::Sign up-->
                                <div
                                    class="text-gray-500 text-center fw-semibold fs-6"
                                >
                                    {{ translations?.Already_have_an_Account }} ?

                                    <router-link
                                        :to="`/${locale}/login`"
                                        class="link-primary fw-semibold"
                                        >{{ translations?.signin }}</router-link
                                    >
                                </div>

                                <div
                                    class="text-gray-900 text-center fw-semibold fs-6"
                                >
                                    <router-link
                                        :to="`/${locale}`"
                                        class="link-primary"
                                        >{{ translations?.home }}</router-link
                                    >
                                </div>

                                <!--end::Sign up-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                    <!--begin::Footer-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Authentication - Sign-up-->
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="kt_modal_scrollable_2">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations?.terms }}</h5>

                    <!--begin::Close-->
                    <div
                        @click="closeModel()"
                        class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <i class="ki-duotone ki-cross fs-2x"
                            ><span class="path1"></span
                            ><span class="path2"></span
                        ></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div v-html="items.description"></div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        @click="closeModel()"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

import { ref } from "vue";
import MobileFlag from "./MobileFlag.vue";

export default {
    components: {
        MobileFlag,
    },
    data() {
        return {
            errors: [],
            formData: {
                name: null,
                email: null,
                mobile: null,
                password: null,
                confirmPassword: null,
                terms: null,
                gender: null,
                country_id: null,
                city_id: null,
            },

            items: [],
            titleModel: "",
            countries: [],
            cities: [],
            passwordError: null,
            confirmPasswordError: null,
            passwordVisible: false,
            confirmPasswordVisible: false,
            isLoading: false,
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting");
        this.fetchItemsCountry();
        // this.onMobileInput()

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

        nameFieldLabel() {
            const t = this.translations || {};
            return t.name || t.full_name || t.username || (this.locale === 'ar' ? 'الاسم' : 'Name');
        },
    },

    methods: {
        getModale() {
            this.fetchItems();

            $("#kt_modal_scrollable_2").modal("show");
        },

        closeModel() {
            $("#kt_modal_scrollable_2").modal("hide");
        },

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
  
        async fetchItems() {
            this.isLoading = true;
            await axios
                .get("Pages/getById", {
                    params: {
                        ID: 4,
                    },
                })
                .then((response) => {
                    this.items = response.data.items;
                    this.isLoading = false;
                    this.languages = this.$store.state.languages;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },




        async fetchItemsCountry() {
            this.isLoading = true;
            await axios
                .get("basic/getCountries", {
                    params: {
                        country_id: this.formData.country_id,
                    },
                })
                .then((response) => {
                    if (this.formData.country_id) {
                        this.cities = response.data.items;
                    } else {
                        this.countries = response.data.items;
                    }


                    this.isLoading = false;
                    this.languages = this.$store.state.languages;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },


        async fetchItemsCity() {

            let intro = $('.iti__selected-dial-code').html();
            let code = intro.replace("+", "");
            let matchedCountry = null;

            console.log(code)

            for (const country of this.countries) {
                if (country.code == code) {
                    matchedCountry = country;
                    break;
                }
            }

            this.formData.country_id = matchedCountry.id
            this.isLoading = true;
            await axios
                .get("basic/getCountries", {
                    params: {
                        country_id: matchedCountry.id,
                    },
                })
                .then((response) => {
                        this.cities = response.data.items;
                  


                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },


        validatePassword() {
            if (this.formData.password.length < 8) {
                this.passwordError = "Password must be at least 8 characters.";
            } else if (!/[A-Z]/.test(this.formData.password)) {
                this.passwordError =
                    "Password must contain at least one uppercase letter.";
            } else if (!/[a-z]/.test(this.formData.password)) {
                this.passwordError =
                    "Password must contain at least one lowercase letter.";
            } else if (!/[0-9]/.test(this.formData.password)) {
                this.passwordError =
                    "Password must contain at least one number.";
            } else if (!/[\W_]/.test(this.formData.password)) {
                this.passwordError =
                    "Password must contain at least one special character.";
            } else {
                this.passwordError = null;
            }
        },

        // التحقق من تطابق كلمة المرور وتأكيدها
        validateConfirmPassword() {
            if (this.formData.confirmPassword !== this.formData.password) {
                this.confirmPasswordError = "Passwords do not match.";
            } else {
                this.confirmPasswordError = null;
            }
        },

        // تبديل عرض/إخفاء كلمة المرور
        togglePasswordVisibility(type) {
            if (type === "passwordVisible") {
                this.passwordVisible = !this.passwordVisible;
            } else if (type === "confirmPasswordVisible") {
                this.confirmPasswordVisible = !this.confirmPasswordVisible;
            }
        },

        async Register() {
            this.isLoading = true;
            console.log(this.formData)

            try {
                let url = window.location.origin;
                const response = await axios.post(
                    "/auth/register",

                    this.formData
                );

                if (response.data.code == 200) {
                    this.swalFunction("info", response.data.message);
                   
                    localStorage.setItem("mobile", this.formData.mobile);

                    this.isLoading = false;
                    this.$router.push(
                        "/" + this.$route.params.locale + "/otp-verify"
                    );
                } else {
                    this.isLoading = false;
                    this.swalFunction("info", response.data.message);
                }

                // this.$router.push('/'+ this.$route.params.locale +'/dashboard').then(() => {
                //     window.location.reload(); // إعادة تحميل الصفحة
                // });
                // this.$router.push('/'+ this.$route.params.locale +'/dashboard');  // تأكد من المسار الصحيح
            } catch (error) {
                this.isLoading = false;
                this.swalFunction("error", response.data.message);
            }
        },
    },
};
</script>
<style type="text/css">
.iti__country-container {
    left: 0 !important;
    direction: ltr !important;
}

.iti__tel-input {
    padding-left: 81px;
}

.country-select {
    border: 1px solid #ced4da !important;
    background: #fff !important;
    color: #222 !important;
    border-radius: 8px;
    min-height: 48px;
    font-size: 1rem;
}
.country-select:focus {
    border-color: #e5aa2a !important;
    box-shadow: 0 0 0 0.2rem rgba(229, 170, 42, 0.15);
}
</style>
