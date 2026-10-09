<template>
    <div v-if="modelValue" class="modal-update-backdrop">
        <div class="modal-update-content" style="max-width: 90%; width: 600px">
            <div class="modal-update-icon-wrap">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="32" fill="#fff" />
                    <path
                        d="M20 34L29 43L44 25"
                        stroke="#e5aa2a"
                        stroke-width="4"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <circle
                        cx="32"
                        cy="32"
                        r="28"
                        stroke="#e5aa2a"
                        stroke-width="4"
                    />
                </svg>
            </div>
            <div class="modal-update-text">
                <p class="mb-2" style="line-height: 1.55">
                    {{ translations?.add_ad_verify_intro }}
                </p>
                <p class="mb-3 text-muted" style="font-size: 0.95rem; line-height: 1.5">
                    {{ translations?.add_ad_verify_fees_note }}
                </p>

                <template v-if="verifyStep === 'id'">
                    <label
                        class="trust-field-label"
                        style="display: block; margin-top: 14px; font-weight: 600; color: #333"
                    >
                        {{ translations?.trust_id_or_iqama_label || "رقم الهوية أو الإقامة" }}
                    </label>
                    <input
                        style="margin-top: 6px"
                        class="ad-form-input"
                        :placeholder="translations?.ID_NO || '10 أرقام'"
                        v-model="formTrust.id_no"
                        maxlength="10"
                        inputmode="numeric"
                    />
                    <p class="text-muted mb-2" style="font-size: 0.9rem">
                        {{ trustBirthHint }}
                    </p>
                    <template v-if="trustIsSaudi || trustIsIqama">
                        <div class="trust-calendar-row">
                            <label class="trust-calendar-option">
                                <input
                                    type="radio"
                                    name="trust_birth_calendar"
                                    value="hijri"
                                    v-model="formTrust.birth_calendar"
                                />
                                <span>{{
                                    translations?.trust_calendar_hijri || "تاريخ هجري"
                                }}</span>
                            </label>
                            <label class="trust-calendar-option">
                                <input
                                    type="radio"
                                    name="trust_birth_calendar"
                                    value="gregorian"
                                    v-model="formTrust.birth_calendar"
                                />
                                <span>{{
                                    translations?.trust_calendar_gregorian || "تاريخ ميلادي"
                                }}</span>
                            </label>
                        </div>
                        <label
                            class="trust-field-label"
                            style="display: block; margin-top: 8px; font-weight: 600; color: #333"
                        >
                            <template v-if="formTrust.birth_calendar === 'hijri'">
                                {{
                                    translations?.trust_date_of_birth_hijri ||
                                    "تاريخ الميلاد الهجري (شهر / سنة)"
                                }}
                            </template>
                            <template v-else>
                                {{
                                    translations?.trust_date_of_birth ||
                                    "تاريخ الميلاد الميلادي (شهر / سنة)"
                                }}
                            </template>
                        </label>
                        <div
                            class="trust-birth-row"
                            v-if="formTrust.birth_calendar === 'hijri'"
                        >
                            <select
                                class="ad-form-input trust-birth-select"
                                v-model="formTrust.birth_month_h"
                            >
                                <option disabled :value="''">
                                    {{ translations?.birth_month_label || "الشهر" }}
                                </option>
                                <option
                                    v-for="m in trustBirthMonthsHijri"
                                    :key="'mh-' + m.value"
                                    :value="m.value"
                                >
                                    {{ m.label }}
                                </option>
                            </select>
                            <select
                                class="ad-form-input trust-birth-select"
                                v-model="formTrust.birth_year_h"
                            >
                                <option disabled :value="''">
                                    {{ translations?.birth_year_label || "السنة" }}
                                </option>
                                <option
                                    v-for="y in trustBirthYearsHijri"
                                    :key="'yh-' + y"
                                    :value="y"
                                >
                                    {{ y }}
                                </option>
                            </select>
                        </div>
                        <div class="trust-birth-row" v-else>
                            <select
                                class="ad-form-input trust-birth-select"
                                v-model="formTrust.birth_month"
                            >
                                <option disabled :value="''">
                                    {{ translations?.birth_month_label || "الشهر" }}
                                </option>
                                <option
                                    v-for="m in trustBirthMonths"
                                    :key="'m-' + m.value"
                                    :value="m.value"
                                >
                                    {{ m.label }}
                                </option>
                            </select>
                            <select
                                class="ad-form-input trust-birth-select"
                                v-model="formTrust.birth_year"
                            >
                                <option disabled :value="''">
                                    {{ translations?.birth_year_label || "السنة" }}
                                </option>
                                <option
                                    v-for="y in trustBirthYears"
                                    :key="'y-' + y"
                                    :value="y"
                                >
                                    {{ y }}
                                </option>
                            </select>
                        </div>
                    </template>
                </template>

                <template v-else-if="verifyStep === 'otp'">
                    <p style="margin-top: 19px; font-weight: 600; color: #333">
                        {{
                            translations?.enter_otp_hint ||
                            "أدخل رمز التحقق المرسل إلى جوالك"
                        }}
                    </p>
                    <div class="trust-otp-inputs">
                        <input
                            v-for="(digit, idx) in yakeenOtpDigits"
                            :key="'yakeen-otp-' + idx"
                            ref="yakeenOtpInput"
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="trust-otp-input"
                            v-model="yakeenOtpDigits[idx]"
                            @input="onYakeenOtpInput(idx, $event)"
                            @keydown.backspace="onYakeenOtpBackspace(idx, $event)"
                            @paste="onYakeenOtpPaste"
                        />
                    </div>
                </template>
            </div>

            <div class="add-ad-actions">
                <template v-if="verifyStep === 'id'">
                    <button
                        class="add-ad-btn active"
                        @click="sendYakeenConsent()"
                        :disabled="isLoadingYakeen"
                    >
                        <span v-if="isLoadingYakeen">{{
                            translations?.Loading || "جاري الإرسال..."
                        }}</span>
                        <span v-else>{{ translations?.Agree || "موافق" }}</span>
                    </button>
                    <button class="add-ad-btn" @click="onDisagree()">
                        {{ translations?.Disagree || "غير موافق" }}
                    </button>
                </template>
                <template v-else-if="verifyStep === 'otp'">
                    <button
                        class="add-ad-btn active"
                        @click="sendYakeenOtp()"
                        :disabled="isLoadingYakeen || !yakeenOtpComplete"
                    >
                        <span v-if="isLoadingYakeen">{{
                            translations?.Loading || "جاري التحقق..."
                        }}</span>
                        <span v-else>{{ translations?.verify_btn || "تحقق" }}</span>
                    </button>
                    <button
                        class="add-ad-btn"
                        @click="
                            verifyStep = 'id';
                            resetYakeenOtpState();
                        "
                    >
                        {{ translations?.Back || "رجوع" }}
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import { authApi } from "@/axiosUser";

export default {
    name: "YakeenVerifyModal",
    props: {
        modelValue: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:modelValue", "verified", "cancel"],
    data() {
        return {
            formTrust: {
                id_no: "",
                birth_calendar: "hijri",
                birth_month: "",
                birth_year: "",
                birth_month_h: "",
                birth_year_h: "",
                otp_code: "",
            },
            verifyStep: "id",
            isLoadingYakeen: false,
            yakeenOtpDigits: ["", "", "", ""],
            yakeenTransactionId: null,
        };
    },
    computed: {
        locale() {
            return this.$route?.params?.locale || localStorage.getItem("locale") || "en";
        },
        translations() {
            return this.$store.getters.getTranslations || {};
        },
        trustIdDigits() {
            return (this.formTrust.id_no || "").trim().replace(/\s/g, "");
        },
        trustIsSaudi() {
            return this.trustIdDigits.length === 10 && this.trustIdDigits.startsWith("1");
        },
        trustIsIqama() {
            return this.trustIdDigits.length === 10 && this.trustIdDigits.startsWith("2");
        },
        trustBirthHint() {
            if (this.trustIsSaudi || this.trustIsIqama) {
                return (
                    this.translations?.trust_birth_hint_calendar ||
                    "اختر هجري أو ميلادي كما مكتوب على الهوية/الإقامة — يُرسل ليقين تاريخ واحد فقط (yyyy-mm)."
                );
            }
            return (
                this.translations?.trust_birth_hint_id_first ||
                "أدخل رقم الهوية أو الإقامة (10 أرقام) أولاً."
            );
        },
        trustBirthYears() {
            const maxYear = new Date().getFullYear() - 10;
            const years = [];
            for (let y = maxYear; y >= 1940; y--) {
                years.push(y);
            }
            return years;
        },
        trustBirthMonths() {
            const isAr = this.locale === "ar";
            const arNames = [
                "يناير",
                "فبراير",
                "مارس",
                "أبريل",
                "مايو",
                "يونيو",
                "يوليو",
                "أغسطس",
                "سبتمبر",
                "أكتوبر",
                "نوفمبر",
                "ديسمبر",
            ];
            const enNames = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ];
            const names = isAr ? arNames : enNames;
            return names.map((label, i) => ({ value: i + 1, label }));
        },
        trustBirthYearsHijri() {
            const years = [];
            for (let y = 1450; y >= 1350; y--) {
                years.push(y);
            }
            return years;
        },
        trustBirthMonthsHijri() {
            const isAr = this.locale === "ar";
            const arNames = [
                "محرم",
                "صفر",
                "ربيع الأول",
                "ربيع الثاني",
                "جمادى الأولى",
                "جمادى الآخرة",
                "رجب",
                "شعبان",
                "رمضان",
                "شوال",
                "ذو القعدة",
                "ذو الحجة",
            ];
            const enNames = [
                "Muharram",
                "Safar",
                "Rabi I",
                "Rabi II",
                "Jumada I",
                "Jumada II",
                "Rajab",
                "Shaaban",
                "Ramadan",
                "Shawwal",
                "Dhu al-Qadah",
                "Dhu al-Hijjah",
            ];
            const names = isAr ? arNames : enNames;
            return names.map((label, i) => ({ value: i + 1, label }));
        },
        yakeenOtpComplete() {
            return this.yakeenOtpDigits.every((d) => String(d).length === 1);
        },
    },
    watch: {
        modelValue(open) {
            if (open) {
                this.verifyStep = "id";
                this.resetTrustBirthFields();
                this.resetYakeenOtpState();
                this.formTrust.id_no = "";
                this.prefillTrustBirthFromUser();
            }
        },
        trustIsSaudi(isSaudi) {
            if (isSaudi) {
                this.formTrust.birth_calendar = "hijri";
                this.resetTrustBirthFields();
            }
        },
        trustIsIqama(isIqama) {
            if (isIqama) {
                this.formTrust.birth_calendar = "gregorian";
                this.resetTrustBirthFields();
            }
        },
        "formTrust.birth_calendar"() {
            this.resetTrustBirthFields();
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

        close() {
            this.$emit("update:modelValue", false);
        },

        onDisagree() {
            this.verifyStep = "id";
            this.resetYakeenOtpState();
            this.resetTrustBirthFields();
            this.close();
            this.$emit("cancel");
        },

        resetYakeenOtpState() {
            this.yakeenOtpDigits = ["", "", "", ""];
            this.yakeenTransactionId = null;
            this.formTrust.otp_code = "";
        },

        resetTrustBirthFields() {
            this.formTrust.birth_month = "";
            this.formTrust.birth_year = "";
            this.formTrust.birth_month_h = "";
            this.formTrust.birth_year_h = "";
        },

        focusYakeenOtpInput(idx) {
            this.$nextTick(() => {
                const inputs = this.$refs.yakeenOtpInput;
                const el = Array.isArray(inputs) ? inputs[idx] : inputs;
                if (el) {
                    el.focus();
                }
            });
        },

        onYakeenOtpInput(idx, e) {
            const val = (e.target.value || "").replace(/\D/g, "").slice(0, 1);
            this.yakeenOtpDigits[idx] = val;
            if (val && idx < 3) {
                this.focusYakeenOtpInput(idx + 1);
            }
        },

        onYakeenOtpBackspace(idx) {
            if (!this.yakeenOtpDigits[idx] && idx > 0) {
                this.focusYakeenOtpInput(idx - 1);
            }
        },

        onYakeenOtpPaste(e) {
            const paste = (e.clipboardData.getData("text") || "")
                .replace(/\D/g, "")
                .slice(0, 4);
            if (!paste) {
                return;
            }
            e.preventDefault();
            for (let i = 0; i < paste.length; i++) {
                this.yakeenOtpDigits[i] = paste[i];
            }
            this.focusYakeenOtpInput(paste.length < 4 ? paste.length : 3);
        },

        prefillTrustBirthFromUser() {
            const u = this.$store.getters.getUser;
            if (!u) {
                return;
            }
            let iso = "";
            if (u.birth_date) {
                iso = String(u.birth_date).split("T")[0].split(" ")[0];
            }
            if (iso && /^\d{4}-\d{2}-\d{2}$/.test(iso)) {
                const [y, m] = iso.split("-").map((n) => parseInt(n, 10));
                this.formTrust.birth_year = y;
                this.formTrust.birth_month = m;
                return;
            }
            if (u.birth_year) {
                this.formTrust.birth_year = parseInt(u.birth_year, 10) || "";
            }
            if (u.birth_month) {
                this.formTrust.birth_month = parseInt(u.birth_month, 10) || "";
            }
        },

        async sendYakeenConsent() {
            const idNo = this.trustIdDigits;
            if (!idNo || idNo.length !== 10) {
                this.swalFunction(
                    "error",
                    this.translations?.Enter_valid_id || "Enter a valid 10-digit ID"
                );
                return;
            }
            if (!this.trustIsSaudi && !this.trustIsIqama) {
                this.swalFunction(
                    "error",
                    this.translations?.Enter_valid_id || "رقم هوية أو إقامة غير صالح"
                );
                return;
            }
            const payload = {
                id_number: idNo,
                birth_calendar: this.formTrust.birth_calendar,
            };
            if (this.formTrust.birth_calendar === "hijri") {
                const monthH = parseInt(this.formTrust.birth_month_h, 10);
                const yearH = parseInt(this.formTrust.birth_year_h, 10);
                if (!monthH || !yearH || yearH < 1350) {
                    this.swalFunction(
                        "error",
                        this.translations?.trust_date_of_birth_hijri_required ||
                            "اختر شهر وسنة الميلاد الهجري"
                    );
                    return;
                }
                payload.birth_month_h = monthH;
                payload.birth_year_h = yearH;
            } else {
                const month = parseInt(this.formTrust.birth_month, 10);
                const year = parseInt(this.formTrust.birth_year, 10);
                if (!month || !year) {
                    this.swalFunction(
                        "error",
                        this.translations?.trust_date_of_birth_required ||
                            "اختر شهر وسنة الميلاد الميلادي"
                    );
                    return;
                }
                payload.birth_month = month;
                payload.birth_year = year;
            }

            this.isLoadingYakeen = true;
            try {
                const response = await authApi({
                    method: "post",
                    url: "auth/yakeen/consent",
                    data: payload,
                });
                const data = response.data || {};
                const ok =
                    data.code === 200 || data.code === "200" || data.status === true;
                const items = data.items || {};
                const txId = items.transaction_id || null;
                if (ok && txId) {
                    this.resetYakeenOtpState();
                    this.yakeenTransactionId = txId;
                    this.verifyStep = "otp";
                    this.focusYakeenOtpInput(0);
                    if (items.otp_already_sent) {
                        this.swalFunction(
                            "info",
                            data.message ||
                                (this.translations?.enter_otp_hint ||
                                    "أدخل رمز التحقق المرسل إلى جوالك")
                        );
                    }
                } else if (ok && !txId) {
                    this.swalFunction(
                        "error",
                        data.message || "لم يُرجع معرّف المعاملة من يقين"
                    );
                } else {
                    const msg = data.message || "Consent request failed";
                    this.swalFunction(
                        "error",
                        typeof msg === "string" ? msg : JSON.stringify(msg)
                    );
                }
            } catch (error) {
                console.error("Yakeen consent error:", error);
                const res = error.response;
                let msg = "Error connecting to Yakeen";
                if (res) {
                    if (res.status === 401) msg = "Please log in first";
                    else if (res.data && typeof res.data === "object")
                        msg = res.data.message || msg;
                }
                this.swalFunction("error", msg);
            } finally {
                this.isLoadingYakeen = false;
            }
        },

        async sendYakeenOtp() {
            const otp = this.yakeenOtpDigits.join("").trim();
            if (!this.yakeenOtpComplete || otp.length < 4) {
                this.swalFunction(
                    "error",
                    this.translations?.enter_valid_otp ||
                        "أدخل رمز التحقق كاملاً (4 أرقام)"
                );
                return;
            }
            if (!this.yakeenTransactionId) {
                this.swalFunction(
                    "error",
                    this.translations?.yakeen_tx_missing ||
                        "انتهت الجلسة. أعد الخطوة الأولى واطلب رمزاً جديداً."
                );
                this.verifyStep = "id";
                return;
            }

            this.isLoadingYakeen = true;
            try {
                const response = await authApi({
                    method: "post",
                    url: "auth/yakeen/verify",
                    data: {
                        otp_code: otp,
                        transaction_id: this.yakeenTransactionId,
                    },
                });
                const data = response.data || {};
                const ok =
                    data.code === 200 || data.code === "200" || data.status === true;
                if (ok) {
                    this.verifyStep = "id";
                    this.resetYakeenOtpState();
                    await this.$store.dispatch("markNafathVerified");
                    this.close();
                    this.swalFunction(
                        "success",
                        this.translations?.Verification_success ||
                            "Identity verified successfully"
                    );
                    this.$emit("verified");
                    this.$store.dispatch("fetchUser").catch(() => {});
                } else {
                    const msg = data.message || "Verification failed";
                    this.swalFunction(
                        "error",
                        typeof msg === "string" ? msg : JSON.stringify(msg)
                    );
                }
            } catch (error) {
                console.error("Yakeen verify error:", error);
                const res = error.response;
                let msg = "Error during verification";
                if (res && res.data && typeof res.data === "object") {
                    msg = res.data.message || msg;
                }
                this.swalFunction("error", msg);
            } finally {
                this.isLoadingYakeen = false;
            }
        },
    },
};
</script>

<style scoped>
.ad-form-input {
    background: #f7f7f9;
    border: none;
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 1.08rem;
    color: #222;
    outline: none;
    width: 100%;
}
.trust-birth-row {
    display: flex;
    gap: 8px;
    margin-top: 6px;
    flex-wrap: wrap;
}
.trust-calendar-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 10px;
}
.trust-calendar-option {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #333;
    cursor: pointer;
}
.trust-calendar-option input {
    margin: 0;
}
.trust-birth-select {
    flex: 1;
    min-width: 90px;
}
.trust-otp-inputs {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 16px;
}
.trust-otp-input {
    width: 52px;
    height: 52px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 600;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
}
.trust-otp-input:focus {
    border-color: #b8860b;
    box-shadow: 0 0 0 2px rgba(184, 134, 11, 0.2);
}
.add-ad-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 22px;
    flex-wrap: wrap;
}
.add-ad-btn {
    min-width: 120px;
    padding: 12px 22px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: #fff;
    color: #333;
    font-weight: 600;
    cursor: pointer;
}
.add-ad-btn.active {
    background: #e5aa2a;
    border-color: #e5aa2a;
    color: #fff;
}
.modal-update-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2600;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-update-content {
    background: #fff;
    border-radius: 1.3rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 38px 38px 32px 38px;
    min-width: 340px;
    max-width: 585px;
    text-align: center;
    position: relative;
}
.modal-update-icon-wrap {
    width: 80px;
    height: 80px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 5px auto 5px auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    position: absolute;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    top: 0;
}
.modal-update-text {
    color: #222;
    font-size: 1.25rem;
    margin-top: 36px;
}
</style>
