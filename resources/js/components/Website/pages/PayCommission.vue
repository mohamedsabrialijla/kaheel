<template>
    <div class="pay-commission-container">
        <div class="pay-commission-card">
            <button class="pay-commission-back" @click="handleBack">
                <i
                    :class="
                        locale == 'ar'
                            ? 'fas fa-angle-right'
                            : 'fas fa-angle-left'
                    "
                ></i>
                {{ translations?.Back || "Back" }}
            </button>
            <div class="pay-commission-header">
                <img
                    src="/assets/media/svg/brand-logos/logo.png"
                    class="pay-commission-logo"
                />
                <div class="pay-commission-title">
                    {{
                        translations?.Commission_Payment || "Commission Payment"
                    }}
                </div>
            </div>
            <hr class="pay-commission-divider" />
            <div v-if="!showPaymentForm">
                <div class="pay-commission-label">
                    {{
                        translations?.Please_enter_1 ||
                        "Please enter the amount to pay the commission or can select Ad ."
                    }}
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12">
                        <multiselect
                            v-model="selectAd"
                            :placeholder="translations?.search || 'Search'"
                            label="title"
                            track-by="id"
                            :options="Myads"
                            :multiple="false"
                            :taggable="false"
                            class="form-control"
                            selectLabel=""
                            selectedLabel=""
                            deselectLabel=""
                            @update:modelValue="handleAdChange"
                            @click="fetchItemsAds"
                        >
                        </multiselect>
                    </div>
                </div>

                <div class="pay-commission-row">
                    <input
                        class="pay-commission-input"
                        placeholder="Enter Amount"
                        v-model="price"
                    />

                    <button class="pay-commission-calc" @click="Calculate()">
                        {{ translations?.Calculate || "Calculate" }}
                    </button>
                </div>
                <input
                    v-if="IDAd"
                    class="pay-commission-input"
                    placeholder="ID Number for Ads"
                    style="margin-bottom: 10px"
                    v-model="IDAd"
                    disabled
                />

                <div class="pay-commission-row">
                    <input
                        class="pay-commission-input"
                        placeholder="000.0"
                        v-model="price_pay"
                    />
                    <button class="pay-commission-price" @click="PayNow()">
                        {{ translations?.Pay_Now || "Pay Now" }}
                    </button>
                </div>
                <div class="pay-commission-note">
                    {{
                        translations?.site_c || "Site Commission Transfer Only"
                    }}
                </div>
                <div class="pay-commission-methods">
                    <!--       <div>
                  <label><input type="radio" value="card"     v-model="selectedMethod"> بطاقة (Visa/Master/Mada)</label><br>
                  <label><input type="radio" value="mada"     v-model="selectedMethod"> Mada فقط</label><br>
                  <label><input type="radio" value="applepay" v-model="selectedMethod"> Apple Pay</label><br>
                  <label><input type="radio" value="stcpay"   v-model="selectedMethod"> STC Pay</label><br>
                </div>

                <button @click="PayNow">ادفع الآن</button>
                -->

                    <div class="pay-commission-method">
                        <img :src="payImg" alt="pay" style="width: 32px" />
                    </div>

                    <div class="pay-commission-method">
                        <img :src="visaImg" alt="visa" />
                    </div>
                    <div class="pay-commission-method">
                        <img :src="stcImg" alt="stc" />
                    </div>

                    <div class="pay-commission-method">
                        <img :src="madaImg" alt="mada" />
                    </div>
                    <button
                        class="pay-commission-transfer"
                        @click="showPaymentForm = true"
                    >
                        {{
                            translations?.Transfers_Banking ||
                            "Transfers Banking"
                        }}
                    </button>
                </div>
                <div class="pay-commission-bank-card">
                    <img
                        :src="footerImg"
                        class="pay-commission-bank-logo"
                        alt="footer"
                    />
                    <div class="pay-commission-bank-title">
                        Bank: Al-Rajhi Bank
                    </div>
                    <div class="pay-commission-bank-info">
                        Account Name : Abdelrahman
                    </div>
                    <div class="pay-commission-bank-info">
                        Account Number : 142345464
                    </div>
                    <div class="pay-commission-bank-info">
                        IBAN Number : 46578585
                    </div>
                </div>
            </div>

            <div v-else>
                <div class="payment-form-title">Brandy KSA</div>
                <div class="payment-form-amount">Amount: 320.00 SAR</div>
                <div class="payment-form-group">
                    <label>Card Number</label>
                    <div class="payment-form-input-icon">
                        <input
                            class="payment-form-input"
                            placeholder="Enter Amount"
                        />
                        <img :src="cardImg" class="payment-form-card-icon" />
                    </div>
                </div>
                <div class="payment-form-row">
                    <div class="payment-form-group" style="flex: 1">
                        <label>Expiry date</label>
                        <input class="payment-form-input" placeholder="MM/YY" />
                    </div>
                    <div class="payment-form-group" style="flex: 1">
                        <label>CVV</label>
                        <div class="payment-form-input-icon">
                            <input
                                class="payment-form-input"
                                placeholder="XXXX"
                            />
                            <img
                                :src="cardImg"
                                class="payment-form-card-icon"
                            />
                        </div>
                    </div>
                </div>
                <div class="payment-form-group">
                    <label>Card Holder Number</label>
                    <input
                        class="payment-form-input"
                        placeholder="Card Holder First Name and Last Name"
                    />
                </div>

                <div class="payment-form-logos">
                    <img :src="payImg" class="payment-form-logo" />
                    <img :src="visaImg" class="payment-form-logo" />
                    <img :src="stcImg" class="payment-form-logo" />
                    <img :src="masterImg" class="payment-form-logo" />
                    <img :src="visamImg" class="payment-form-logo" />
                    <img :src="madaImg" class="payment-form-logo" />
                    <img :src="urwayImg" class="payment-form-logo" />
                    <img :src="pciImg" class="payment-form-logo" />
                </div>
            </div>
        </div>
    </div>

    <div v-if="showUpdateModal" class="modal-update-backdrop">
        <div class="modal-update-content">
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
                {{ titleBlack }}<br />{{ bodyBlack }}
            </div>
            <button class="modal-update-ok" @click="showUpdateModal = false">
                Ok
            </button>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import cardImg from "@/assets/pay-commission/card.png";
import footerImg from "@/assets/pay-commission/footer.png";
import madaImg from "@/assets/pay-commission/mada.png";
import masterImg from "@/assets/pay-commission/master.png";
import payImg from "@/assets/pay-commission/pay.png";
import pciImg from "@/assets/pay-commission/pci.png";
import stcImg from "@/assets/pay-commission/stc.png";
import urwayImg from "@/assets/pay-commission/urway.png";
import visaImg from "@/assets/pay-commission/visa.png";
import visamImg from "@/assets/pay-commission/visam.png";
import { authApi } from "@/axiosUser";

export default {
    name: "PayCommission",
    components: {
        Multiselect,
    },
    data() {
        return {
            cardImg,
            footerImg,
            madaImg,
            masterImg,
            payImg,
            pciImg,
            stcImg,
            urwayImg,
            visaImg,
            visamImg,
            showPaymentForm: false,
            price: "",
            IDAd: "",
            showUpdateModal: "",
            price_pay: "",
            Myads: [],
            selectAd: "",
            selectedMethod: "card",
        };
    },

    mounted() {
        // const ad = this.$store.state.adCurrent;
        this.fetchItemsAds();
        this.ad = JSON.parse(localStorage.getItem("adPay"));
        if (this.ad?.id) {
            this.price = this.ad.price;
            this.IDAd = this.ad.ad_number;
            this.selectAd = this.ad;
        }
    },

    beforeUnmount() {
        this.ad = null;
        this.price = null;
        this.IDAd = null;
        this.selectAd = null;

        // حذف من localStorage
        localStorage.removeItem("adPay");
    },

    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        translations() {
            return this.$store.getters.getTranslations || {};
        },
    },

    methods: {
        handleBack() {
            if (this.showPaymentForm) {
                this.showPaymentForm = false;
                return;
            }

            // this.$router.push("/my-page");
            this.$router.push({ path: `/${this.locale}/my-page` });
        },

        async Calculate() {
            this.isLoading = true;
            await axios
                .get("ads/Calculate", {
                    params: {
                        ad_id: this.ad.id,
                        price: this.price,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        this.price_pay = response.data.items;
                    } else {
                        this.titleBlack = response.data.message;
                        this.bodyBlack = "Error";
                        this.showUpdateModal = true;
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    let message = "حدث خطأ ما";

                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.message
                    ) {
                        message = error.response.data.message;
                        this.titleBlack = message;
                    }

                    // this.bodyBlack = '';
                    this.showUpdateModal = true;
                    this.isLoading = false;
                });
        },
 
        async fetchItemsAds() {
            this.isLoading = true;
        
                await authApi({
                    method: "get",
                    url: "ads/getAllItemsMyAdsWithTrash",
                    params: {
                        pagination: 0,
                    },
                })
                .then((response) => {
                    this.Myads = response.data.items;
                    this.selectAd = this.ad;

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        handleAdChange(ad) {
            this.ad = ad;
            this.price = ad?.price || "";
            this.IDAd = ad?.id ? ad.ad_number : "";
            // إذا كنت تريد تحديث بيانات معينة بناءً على الإعلان الجديد
            console.log("Ad Selected:", ad);
        },

        async PayNow() {
            if (!this.price_pay || this.price_pay <= 0) {
                this.swalFunction(
                    "error",
                    "Please check Data Select ad and check Price For Pay"
                );
                return;
            }

            this.isLoading = true;
            try {
                const response = await authApi({
                    method: "get",
                    url: "Payment/generateUrwayPaymentLink",
                    params: {
                        amount: this.price_pay,
                        ad_id: this.selectAd?.id,
                        payment_from: 'web',
                    },
                });

                if (response.data.status && response.data.payment_url) {
                    window.location.href = response.data.payment_url;
                } else {
                    this.swalFunction(
                        "error",
                        response.data.message ||
                            "حدث خطأ أثناء إنشاء رابط الدفع"
                    );
                }
            } catch (error) {
                console.error(error);
                this.swalFunction(
                    "error",
                    error.message || "تعذر الاتصال بالسيرفر"
                );
            } finally {
                this.isLoading = false;
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

        // async PayNow() {
        //     alert(this.selectedMethod)
        //     this.isLoading = true;
        //     try {
        //       const res = await axios.get('/Payment/generateUrwayPaymentLink', {
        //         params: {
        //           amount: this.price_pay,
        //           ad_id: this.selectAd.id,
        //           method: this.selectedMethod, // <-- المهم
        //         },
        //       });

        //       console.log(res)
        //       if (res.data?.status && res.data?.payment_url) {
        //         window.location.href = res.data.payment_url; // فتح صفحة الدفع
        //       } else {
        //         this.swalFunction('error', res.data?.message || 'Something went wrong.');
        //       }
        //     } catch (e) {
        //       this.swalFunction('error', e?.message || 'Request failed');
        //     } finally {
        //       this.isLoading = false;
        //     }
        // },
    },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
.multiselect--active .multiselect__select {
    right: 8px;
    top: 10px !important;
}
.multiselect__select {
    right: 8px;
    top: 17px;
}
.pay-commission-container {
    min-height: 100vh;
    background: #fafbfc;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 48px 0 64px 0;
}
.pay-commission-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
    padding: 38px 38px 32px 38px;
    min-width: 420px;
    max-width: 710px;
    width: 100%;
    margin-top: 18px;
}
.pay-commission-header {
    display: flex;
    align-items: center;
    gap: 18px;
    justify-content: flex-start;
}
.pay-commission-logo {
    width: 54px;
    height: 54px;
    border-radius: 8px;
    object-fit: contain;
}
.pay-commission-title {
    flex: 1;
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #212f3a;
}
.pay-commission-divider {
    margin: 18px 0 18px 0;
    border: none;
    border-top: 2px solid #e5e5e5;
}
.pay-commission-label {
    text-align: center;
    color: #222;
    font-size: 1.08rem;
    margin-bottom: 18px;
}
.pay-commission-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}
.pay-commission-input {
    flex: 1;
    border: none;
    background: #f5f5f5;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 1.08rem;
    color: #222;
    outline: none;
}
.pay-commission-calc,
.pay-commission-price {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0 28px;
    font-size: 1.08rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.pay-commission-calc:hover,
.pay-commission-price:hover {
    background: #d49a1a;
}
.pay-commission-note {
    color: #888;
    font-size: 1.05rem;
    margin: 18px 0 10px 0;
}
.pay-commission-methods {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.pay-commission-method {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    padding: 12px 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 90px;
    min-height: 54px;
}
.pay-commission-method img {
    max-width: 60px;
    max-height: 32px;
}
.pay-commission-transfer {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0 22px;
    font-size: 1.08rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
    min-height: 54px;
}
.pay-commission-transfer:hover {
    background: #d49a1a;
}
.pay-commission-bank-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    padding: 24px 18px 18px 18px;
    margin-top: 24px;
    text-align: center;
}
.pay-commission-bank-logo {
    width: 64px;
    height: 64px;
    object-fit: contain;
    margin-bottom: 10px;
}
.pay-commission-bank-title {
    font-weight: bold;
    color: #222;
    font-size: 1.15rem;
    margin-bottom: 6px;
}
.pay-commission-bank-info {
    color: #888;
    font-size: 1.05rem;
    margin-bottom: 2px;
}
.pay-commission-back {
    background: none;
    border: none;
    color: #222;
    font-size: 1.08rem;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    margin-bottom: 16px;
    font-weight: 500;
    transition: color 0.18s;
    margin-top: -80px;
    margin-bottom: 50px;
}
.pay-commission-back:hover {
    color: #e5aa2a;
}
/* Enhanced Responsive Design */
@media (max-width: 1200px) {
    .pay-commission-card {
        max-width: 95%;
        padding: 24px;
    }

    .payment-form-logos {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
}

@media (max-width: 992px) {
    .pay-commission-card {
        max-width: 98%;
        padding: 20px;
    }

    .pay-commission-title {
        font-size: 1.8rem;
    }

    .payment-form-logos {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .payment-form-row {
        flex-direction: column;
        gap: 12px;
    }
}

@media (max-width: 768px) {
    .pay-commission-card {
        max-width: 100%;
        padding: 16px;
        margin: 8px;
    }

    .pay-commission-title {
        font-size: 1.6rem;
    }

    .payment-form-logos {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .payment-form-logo {
        max-height: 45px;
        padding: 12px 6px;
    }

    .pay-commission-row {
        flex-direction: column;
        gap: 12px;
    }

    .pay-commission-input,
    .pay-commission-calc {
        width: 100%;
    }
}

@media (max-width: 700px) {
    .pay-commission-card {
        min-width: 0;
        padding: 18px 4vw 18px 4vw;
    }

    .pay-commission-title {
        font-size: 1.4rem;
    }

    .payment-form-logos {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .payment-form-logo {
        max-height: 40px;
        padding: 10px 4px;
    }

    .payment-form-input {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}
.payment-form-title {
    text-align: center;
    font-size: 1.3rem;
    font-weight: 500;
    margin-bottom: 4px;
}
.payment-form-amount {
    text-align: center;
    font-size: 1.1rem;
    color: #222;
    margin-bottom: 18px;
}
.payment-form-group {
    margin-bottom: 18px;
}
.payment-form-group label {
    display: block;
    font-size: 1.05rem;
    color: #222;
    margin-bottom: 6px;
}
.payment-form-input {
    width: 100%;
    border: 1px solid #bbb;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 1.08rem;
    color: #222;
    outline: none;
    background: #fff;
}
.payment-form-input-icon {
    position: relative;
    display: flex;
    align-items: center;
}
.payment-form-card-icon {
    position: absolute;
    right: 12px;
    width: 32px;
    height: 22px;
    object-fit: contain;
}
.payment-form-row {
    display: flex;
    gap: 16px;
    margin-bottom: 18px;
}
.payment-form-logos {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-top: 32px;
}
.payment-form-logo {
    width: 100%;
    /* max-width: 120px; */
    max-height: 55px;
    -o-object-fit: contain;
    object-fit: contain;
    background: #f7f7f9;
    border-radius: 8px;
    padding: 16px 8px;
    margin: 0 auto;
    display: block;
}

.image-input-placeholder {
    background-image: url("assets/media/svg/files/blank-image.svg");
}
[data-bs-theme="dark"] .image-input-placeholder {
    background-image: url("assets/media/svg/files/blank-image-dark.svg");
}

.justing {
    gap: 7px;
}

:deep(.multiselect__option--selected) {
    background: rgb(214, 162, 48) !important;
    color: #fff !important;
}

:deep(.multiselect__option--highlight) {
    background: rgb(214, 162, 48) !important;
    color: #fff !important;
}
</style>
