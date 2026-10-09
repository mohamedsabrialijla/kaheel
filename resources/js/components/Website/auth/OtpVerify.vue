<template>
    <div>
        <div class="d-flex flex-column flex-root h-100vh">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <div
                    class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1"
                >
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
                        <div class="w-lg-500px p-10">
                            <form
                                class="form w-100"
                                novalidate="novalidate"
                                @submit.prevent="submitCode"
                            >
                                <div class="text-center mb-11">
                                    <h1 class="text-gray-900 fw-bolder mb-3">
                                        {{ translations?.hello_brandy }}
                                    </h1>
                                    <div class="fw-bold fs-3 mb-2">
                                        
                                        {{ translations?.the_code_sent }}
                                    </div>
                                    <div class="text-gray-500 mb-6">
                                        
                                        {{ translations?.please_mobile }}
                                        <span class="otp-masked">{{
                                            maskedPhone
                                        }}</span>
                                    </div>
                                </div>
                                <div class="otp-inputs mb-6">
                                    <input
                                        v-for="(digit, idx) in code"
                                        :key="idx"
                                        ref="otpInput"
                                        type="text"
                                        inputmode="numeric"
                                        maxlength="1"
                                        class="otp-input"
                                        v-model="code[idx]"
                                        @input="onInput(idx, $event)"
                                        @keydown.backspace="
                                            onBackspace(idx, $event)
                                        "
                                        @paste="onPaste"
                                    />
                                </div> 
                                <div class="otp-timer mb-6">
                                    <span v-if="timer > 0"
                                        >{{ translations?.Resend }} 0:{{
                                            timer < 10 ? "0" + timer : timer
                                        }}</span
                                    >
                                    <button
                                        v-else
                                        class="otp-resend-btn"
                                        type="button"
                                        @click="resendCode"
                                    >
                                        {{ translations?.Resend }}
                                    </button>
                                </div>
                                <div class="d-grid mb-10">
                                    <button
                                        class="btn btn-primary indicator"
                                        :disabled="!isCodeComplete"
                                    >
                                        {{ translations?.submit }}

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import axios from "axios";
import { ref, computed, onMounted, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useStore } from "vuex";

const store = useStore();


const code = ref(["", "", "", ""]);
const timer = ref(59);
const interval = ref(null);
const phone = ref("");
const route = useRoute();
const router = useRouter();

const locale = computed(() => route.params.locale);

onMounted(() => {
    phone.value = route.query.phone || "+966********9";
    startTimer();
    focusInput(0);
});
const translations = computed(() => store.getters.getTranslations || {});

function startTimer() {
    timer.value = 59;
    if (interval.value) clearInterval(interval.value);
    interval.value = setInterval(() => {
        if (timer.value > 0) timer.value--;
        else clearInterval(interval.value);
    }, 1000);
}




async function resendCode() {
    const mobile = localStorage.getItem('mobile');

    try {
        const response = await axios.post('/auth/resendCode', {
            mobile: mobile,
        });

        const data = response.data;

        if (data.code == 200) {
            startTimer();
        } else {
            Swal.fire({
                text: data.message,
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        }
    } catch (error) {
        console.error(error);

        Swal.fire({
            text: error.response?.data?.message || "Unexpected error",
            icon: 'error',
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary",
            },
        });
    }
}

function onInput(idx, e) {
    const val = e.target.value.replace(/\D/g, "");
    code.value[idx] = val;
    if (val && idx < 3) {
        focusInput(idx + 1);
    }
}

function onBackspace(idx, e) {
    if (!code.value[idx] && idx > 0) {
        focusInput(idx - 1);
    }
}

function onPaste(e) {
    const paste = e.clipboardData
        .getData("text")
        .replace(/\D/g, "")
        .slice(0, 4);
    for (let i = 0; i < paste.length; i++) {
        code.value[i] = paste[i];
    }
    nextTick(() => {
        focusInput(paste.length < 4 ? paste.length : 3);
    });
}

function focusInput(idx) {
    nextTick(() => {
        const inputs = document.querySelectorAll(".otp-input");
        if (inputs[idx]) inputs[idx].focus();
    });
}




const isCodeComplete = computed(() => code.value.every((d) => d.length === 1));

async function submitCode() {
    if (!isCodeComplete.value) return;

    const mobile = localStorage.getItem('mobile');
    const enteredCode = code.value.join('');

    try {
        const response = await axios.post('/auth/verify', {
            mobile: mobile,
            code: enteredCode,
        });

        const data = response.data;

        if (data.code == 200) {
            if (data.items?.token) {
                localStorage.setItem("authToken",response.data.items.token);
                localStorage.setItem("token", response.data.items.token);
            }

            router.push(`/${locale.value}`);
        } else {
            Swal.fire({
                text: data.message,
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        }
    } catch (error) {
        console.error(error);

        Swal.fire({
                text: error.response?.data?.message || "Unexpected error",
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
    }
};



const maskedPhone = computed(() => {
    let mobile = localStorage.getItem('mobile')

    if (!mobile) return "";
    const last = mobile.slice(-3);
    return "*******" + last;
});
</script>

<style scoped>
.otp-inputs {
    display: flex;
    justify-content: center;
    gap: 24px;
    margin-bottom: 24px;
}
.otp-input {
    width: 64px;
    height: 64px;
    border-radius: 0.475rem;
    border: none;
    background: #f0f1f3;
    font-size: 2.2rem;
    text-align: center;
    font-weight: 600;
    color: #222;
    box-shadow: unset;
    outline: none;
    transition: border 0.18s;
}
.otp-input:focus {
    border: 2px solid #e5aa2a;
    background: #fffbe7;
}
.otp-timer {
    margin-bottom: 32px;
    color: #888;
    font-size: 1.1rem;
    min-height: 28px;
    text-align: center;
}
.otp-resend-btn {
    background: none;
    border: none;
    color: #e5aa2a;
    font-weight: 600;
    cursor: pointer;
    font-size: 1.1rem;
}
</style>
