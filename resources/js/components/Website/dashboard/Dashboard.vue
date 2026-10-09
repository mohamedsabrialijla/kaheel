<template>
    <div class="container">
        <div class="col-md-6 pb-10 pb-lg-0" style="margin-top: 15px">
            <h2>{{ t.hi }} , {{ user ? user.name : "Loading..." }}</h2>
            <p class="fs-6 fw-semibold text-gray-600 py-2">{{ t.edit_info }}</p>
        </div>

        <!--begin::Alert-->
        <div
            class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10"
        >
            <i
                class="ki-duotone ki-notification-bing fs-2hx text-primary me-4 mb-5 mb-sm-0"
                ><span class="path1"></span><span class="path2"></span
                ><span class="path3"></span
            ></i>

            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h4 class="fw-semibold">{{ t.activation_account }}</h4>
                <span>{{ t.acc }}</span>
            </div>
        </div>
        <!--end::Alert-->

        <div class="mb-5 hover-scroll-x">
            <div class="d-grid">
                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                    <li class="nav-item">
                        <a
                            class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                            data-bs-toggle="tab"
                            href="#kt_tab_pane_1"
                            id="tabpanel_1"
                            >{{ t.pinfo }}</a
                        >
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                            data-bs-toggle="tab"
                            href="#kt_tab_pane_2"
                            >{{ t.rest }}</a
                        >
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                            data-bs-toggle="tab"
                            href="#kt_tab_pane_3"
                            >{{ t.notes }}</a
                        >
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            <div
                class="tab-pane fade show active"
                id="kt_tab_pane_1"
                role="tabpanel"
            >
                <div class="card mb-5 mb-xl-10">
                    <!--begin::Card header-->

                    <!--begin::Card header-->
                    <!--begin::Content-->
                    <div
                        id="kt_account_settings_profile_details"
                        class="collapse show"
                    >
                        <!--begin::Form-->
                        <form
                            id="kt_account_profile_details_form"
                            class="form fv-plugins-bootstrap5 fv-plugins-framework"
                            novalidate="novalidate"
                        >
                            <div class="card-body border-top p-9">
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label
                                        class="col-lg-4 col-form-label fw-semibold fs-6"
                                        >{{ t.image }}</label
                                    >
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <!--begin::Image input-->
                                        <div
                                            class="image-input image-input-outline"
                                            data-kt-image-input="true"
                                        >
                                            <!--begin::Preview existing avatar-->
                                            <div
                                                class="image-input-wrapper w-125px h-125px"
                                                :style="{
                                                    'background-image':
                                                        user.logo
                                                            ? `url(${user.logo})`
                                                            : 'url(/assets_user/media/avatars/blank.png)',
                                                }"
                                            ></div>
                                            <!--end::Preview existing avatar-->
                                            <!--begin::Label-->
                                            <label
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="change"
                                                data-bs-toggle="tooltip"
                                                aria-label="Change avatar"
                                                data-bs-original-title="Change avatar"
                                                data-kt-initialized="1"
                                            >
                                                <i
                                                    class="ki-outline ki-pencil fs-7"
                                                ></i>
                                                <!--begin::Inputs-->
                                                <input
                                                    type="file"
                                                    name="avatar"
                                                    accept=".png, .jpg, .jpeg"
                                                    @change="onFileChange"
                                                />
                                                <input
                                                    type="hidden"
                                                    name="avatar_remove"
                                                />
                                                <!--end::Inputs-->
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Cancel-->
                                            <span
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="cancel"
                                                data-bs-toggle="tooltip"
                                                aria-label="Cancel avatar"
                                                data-bs-original-title="Cancel avatar"
                                                data-kt-initialized="1"
                                            >
                                                <i
                                                    class="ki-outline ki-cross fs-2"
                                                ></i>
                                            </span>
                                            <!--end::Cancel-->
                                            <!--begin::Remove-->
                                            <span
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="remove"
                                                data-bs-toggle="tooltip"
                                                aria-label="Remove avatar"
                                                data-bs-original-title="Remove avatar"
                                                data-kt-initialized="1"
                                            >
                                                <i
                                                    class="ki-outline ki-cross fs-2"
                                                ></i>
                                            </span>
                                            <!--end::Remove-->
                                        </div>
                                        <!--end::Image input-->
                                        <!--begin::Hint-->
                                        <div class="form-text">
                                            {{ t.allow }}
                                        </div>
                                        <!--end::Hint-->
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label
                                        class="col-lg-4 col-form-label required fw-semibold fs-6"
                                        >{{ t.name }}</label
                                    >
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div
                                                class="col-lg-6 fv-row fv-plugins-icon-container"
                                            >
                                                <input
                                                    type="text"
                                                    name="fname"
                                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                    v-model="user.name"
                                                    placeholder="First name"
                                                    value="Max"
                                                />
                                                <div
                                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"
                                                ></div>
                                            </div>
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label
                                        class="col-lg-4 col-form-label fw-semibold fs-6"
                                    >
                                        <span class="required">{{
                                            t.mobile
                                        }}</span>
                                        <span
                                            class="ms-1"
                                            data-bs-toggle="tooltip"
                                            aria-label="Phone number must be active"
                                            data-bs-original-title="Phone number must be active"
                                            data-kt-initialized="1"
                                        >
                                            <i
                                                class="ki-outline ki-information-5 text-gray-500 fs-6"
                                            ></i>
                                        </span>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div
                                        class="col-lg-4 fv-row fv-plugins-icon-container"
                                    >
                                        <input
                                            type="tel"
                                            name="phone"
                                            class="form-control form-control-lg form-control-solid"
                                            v-model="user.mobile"
                                            placeholder="Phone number"
                                            value="044 3276 454 935"
                                        />
                                        <div
                                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"
                                        ></div>
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label
                                        class="col-lg-4 col-form-label fw-semibold fs-6"
                                    >
                                        <span class="required">{{
                                            t.email
                                        }}</span>
                                        <span
                                            class="ms-1"
                                            data-bs-toggle="tooltip"
                                            aria-label="Phone number must be active"
                                            data-bs-original-title="Phone number must be active"
                                            data-kt-initialized="1"
                                        >
                                            <i
                                                class="ki-outline ki-information-5 text-gray-500 fs-6"
                                            ></i>
                                        </span>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div
                                        class="col-lg-4 fv-row fv-plugins-icon-container"
                                    >
                                        <input
                                            type="email"
                                            name="phone"
                                            class="form-control form-control-lg form-control-solid"
                                            v-model="user.email"
                                            placeholder="Email Address"
                                            value=""
                                        />
                                        <div
                                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"
                                        ></div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                            </div>
                            <!--end::Card body-->
                            <!--begin::Actions-->
                            <div
                                class="card-footer d-flex justify-content-end py-6 px-9"
                            >
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    @click.prevent="EditInfo"
                                    :disabled="isLoading"
                                >
                                    <span class="indicator-label">{{
                                        t.save
                                    }}</span>
                                    <span class="indicator-progress"
                                        >{{ t.waitMessage }}
                                        <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                        ></span>
                                    </span>
                                </button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Content-->
                </div>
            </div>

            <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                <div class="card mb-5 mb-xl-10">
                    <div
                        id="kt_account_settings_signin_method"
                        class="collapse show"
                    >
                        <!--begin::Card body-->
                        <div class="card-body border-top p-9">
                            <!--begin::Email Address-->

                            <!--end::Email Address-->
                            <!--begin::Separator-->
                            <!--end::Separator-->
                            <!--begin::Password-->
                            <div
                                class="d-flex flex-wrap align-items-center mb-10"
                            >
                                <!--begin::Label-->
                                <div id="kt_signin_password" class="d-none">
                                    <div class="fs-6 fw-bold mb-1">
                                        {{ t.password }}
                                    </div>
                                    <div class="fw-semibold text-gray-600">
                                        ************
                                    </div>
                                </div>
                                <!--end::Label-->
                                <!--begin::Edit-->
                                <div
                                    id="kt_signin_password_edit"
                                    class="flex-row-fluid"
                                >
                                    <!--begin::Form-->
                                    <form
                                        id="kt_signin_change_password"
                                        class="form fv-plugins-bootstrap5 fv-plugins-framework"
                                        novalidate="novalidate"
                                    >
                                        <div class="row mb-1">
                                            <!-- <div class="col-lg-4">
                        <div class="fv-row mb-0 fv-plugins-icon-container">
                          <label for="currentpassword" class="form-label fs-6 fw-bold mb-3">Current Password</label>
                          <input type="password" class="form-control form-control-lg form-control-solid" name="currentpassword" id="currentpassword">
                        <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div></div>
                      </div> -->
                                            <div class="col-lg-4">
                                                <div
                                                    class="fv-row mb-0 fv-plugins-icon-container"
                                                >
                                                    <label
                                                        for="newpassword"
                                                        class="form-label fs-6 fw-bold mb-3"
                                                        >{{ t.np }}</label
                                                    >
                                                    <input
                                                        type="password"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="newpassword"
                                                        id="newpassword"
                                                        v-model="formData.pass"
                                                    />
                                                    <div
                                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"
                                                    ></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div
                                                    class="fv-row mb-0 fv-plugins-icon-container"
                                                >
                                                    <label
                                                        for="confirmpassword"
                                                        class="form-label fs-6 fw-bold mb-3"
                                                        >{{ t.cnp }}</label
                                                    >
                                                    <input
                                                        type="password"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="confirmpassword"
                                                        id="confirmpassword"
                                                        v-model="formData.cpass"
                                                    />
                                                    <div
                                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-text mb-5">
                                            {{ t.pnotes }}
                                        </div>
                                        <div class="d-flex">
                                            <button
                                                type="submit"
                                                class="btn btn-primary"
                                                @click.prevent="
                                                    EditInfoPassword
                                                "
                                                :disabled="isLoading"
                                            >
                                                <span class="indicator-label">{{
                                                    t.update_pass
                                                }}</span>
                                                <span class="indicator-progress"
                                                    >{{ t.waitMessage }}
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"
                                                    ></span>
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                    <!--end::Form-->
                                </div>
                                <!--end::Edit-->
                                <!--begin::Action-->
                                <div
                                    id="kt_signin_password_button"
                                    class="ms-auto d-none"
                                >
                                    <button
                                        class="btn btn-light btn-active-light-primary"
                                    >
                                        {{ rest_pass }}
                                    </button>
                                </div>
                                <!--end::Action-->
                            </div>
                            <!--end::Password-->
                            <!--begin::Notice-->
                            <!-- <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                <i class="ki-outline ki-shield-tick fs-2tx text-primary me-4"></i>
                 <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                  <div class="mb-3 mb-md-0 fw-semibold">
                    <h4 class="text-gray-900 fw-bold">Secure Your Account</h4>
                    <div class="fs-6 text-gray-700 pe-7">Two-factor authentication adds an extra layer of security to your account. To log in, in addition you'll need to provide a 6 digit code</div>
                  </div>

                  <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_two_factor_authentication">Enable</a>
                </div>
              </div> -->
                            <!--end::Notice-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Content-->
                </div>
            </div>

            <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
                <div class="rounded border d-flex flex-column p-10">
                    <label for="" class="form-label">{{ t.plac_notes }} </label>
                    <textarea
                        v-model="user.notes"
                        class="form-control"
                        data-kt-autosize="true"
                        style="min-height: 200px !important"
                    ></textarea>

                    <button @click="checkText">تدقيق</button>
                    <button @click="rephraseText">اعادة صياغة</button>

                    <button @click="AtsText">اختبار ATs</button>
                    <button @click="Suggestion">اقتراح</button>
                </div>

                <h2>نسبة قوة السيرة الذاتية: {{ percentage }}%</h2>
                <div v-for="(section, index) in sections" :key="index">
                    <h3>{{ Object.keys(section)[0] }}</h3>
                    <ul>
                        <li
                            v-for="(issue, idx) in Object.values(section)[0]"
                            :key="idx"
                        >
                            {{ issue }}
                        </li>
                    </ul>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button
                        type="submit"
                        class="btn btn-primary"
                        @click.prevent="EditInfo"
                        :disabled="isLoading"
                    >
                        <span class="indicator-label">{{ t.save }}</span>
                        <span class="indicator-progress"
                            >{{ t.waitMessage }}
                            <span
                                class="spinner-border spinner-border-sm align-middle ms-2"
                            ></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- <Slider></Slider> -->
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
// import Slider from './Slider.vue';

export default {
    name: "Header",
    components: {
        // Slider,
    },

    data() {
        return {
            urlEditInfo: "Users/editItem",
            urlEditPassword: "Users/editItemPassword",
            isLoading: false,
            logo: "",
            logo_preview: "./../assets/media/avatars/300-1.jpg",

            formData: {
                pass: "",
                cpass: "",
            },

            result: [],
            percentage: 0,
            sections: [],
        };
    },

    computed: {
        user() {
            return this.$store.getters.getUser || {};
        },

        locale() {
            return this.$route.params.locale;
        },

        t() {
            return this.$store.getters.getTranslations;
        },
    },

    mounted() {
        this.$store.dispatch("fetchUser");
        this.activateFirstTab();
    },

    methods: {
        async checkText() {
            const response = await axios.post("/check-text", {
                text: this.user.notes,
            });
            this.result = response.data.choices[0].text;
        },

        async rephraseText() {
            const response = await axios.post("/rephrase-text", {
                text: this.user.notes,
            });
            this.result = response.data.choices[0].text;
        },

        async AtsText() {
            const response = await axios.post("/check-ats", {
                text: this.user.notes,
            });

            // this.result = JSON.parse(response.data.corrected_text);
            this.result = response.data.corrected_text;
            console.log(this.result);

            this.percentage = this.result[0];
            this.sections = this.result[1];
        },

        async Suggestion() {
            const response = await axios.post("/check-suggestions", {
                text: this.user.notes,
            });

            console.log(response);
            this.result = response.data.corrected_text;
            let items = this.result;

            items.forEach(function (skill) {
                console.log("Skill:", skill);
            });
        },

        activateFirstTab() {
            const firstTab = document.querySelector("#tabpanel_1");
            if (firstTab) {
                firstTab.click();
            }
        },

        onFileChange(e) {
            this.logo = e.target.files[0];
            const file = e.target.files[0];
            if (file) {
                this.formData.logo = URL.createObjectURL(file);
            }
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

        EditInfo() {
            this.isLoading = true;

            const config = {
                headers: {
                    "content-type": "multipart/form-data",
                },
            };

            let form = new FormData();
            form.append("name", this.user.name);
            form.append("mobile", this.user.mobile);
            form.append("email", this.user.email);
            form.append("notes", this.user.notes);

            if (this.logo) {
                form.append("logo", this.logo);
            }

            axios
                .post(this.urlEditInfo, form, config)
                .then((response) => {
                    this.isLoading = false;
                    if (response.data.items) {
                        this.isLoading = false;
                        this.swalFunction("success", "Doing successfully");
                        this.$store.dispatch("fetchUser");
                    } else {
                        this.isLoading = false;
                        this.swalFunction("error", "Error happens");
                    }
                })
                .catch((error) => {
                    this.isLoading = false;
                    this.swalFunction("error", "Error happens");
                });
        },

        EditInfoPassword() {
            this.isLoading = true;

            let form = new FormData();
            form.append("password", this.formData.pass);
            form.append("confirm_password", this.formData.cpass);

            axios
                .post(this.urlEditPassword, form)
                .then((response) => {
                    this.isLoading = false;
                    if (response.data.code == 200) {
                        this.isLoading = false;
                        this.formData.pass = "";
                        this.formData.cpass = "";
                        this.swalFunction("success", "Doing successfully");
                        this.$store.dispatch("fetchUser");
                    } else {
                        this.isLoading = false;
                        this.swalFunction("error", response.data.message);
                    }
                })
                .catch((error) => {
                    this.isLoading = false;
                    this.swalFunction("error", "Error happens");
                });
        },
    },
};
</script>
