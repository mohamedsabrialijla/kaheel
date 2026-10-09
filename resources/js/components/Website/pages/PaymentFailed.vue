<template>
    <div class="pay-commission-container">
        <div class="pay-commission-card">
            <button class="pay-commission-back" @click="handleBack">
                <i class="fas fa-angle-left"></i> go To Home
            </button>
            <div class="pay-commission-header">
                <img
                    src="/assets/media/svg/brand-logos/logo.png"
                    class="pay-commission-logo"
                />
                <div class="pay-commission-title">Payment Status Details</div>
            </div>
            <hr class="pay-commission-divider" />
            <div>
                <div class="pay-commission-label">
                    Sorry Please can Try Again 
                </div>     
            </div>
            <div class="pay-commission-bank-card">
               
                <div class="pay-commission-bank-title">
                    Ad Name: {{payment?.ad?.title}}
                </div>
                <div class="pay-commission-bank-info">
                    Price : {{payment?.amount}} {{payment?.currency}}
                </div>
                <div class="pay-commission-bank-info">
                    status : {{payment?.status}}
                </div>
                <!-- <div class="pay-commission-bank-info">
                   Payment Type : {{payment?.payment_type?.name}}
                </div> -->
            </div>
        </div>
            
    </div>

   
</template>

<script>
import Multiselect from "vue-multiselect";
import axios from 'axios';

export default {
    name: "PaymentSuccess",
    components: {
        Multiselect,
    },
    data() {
        return {
           
            price: "",
            IDAd: "",
            price_pay: "",
            locale:"",
            payment: null
        };
    },
 
    mounted() {
        this.fetchItems();
        
    },

  computed:{
    locale() {
            return this.$route.params.locale || "en";
        },
  },

    methods: {
   
          handleBack() {

            this.$router.push({ path: `/${this.locale}` });
        },

  

        async fetchItems() {
            this.isLoading = true;
            const trackId = this.$route.query.track_id;
            await axios
                .get("Payment/getAllItems", {
                    params: {
                        trackId: trackId,
                    },
                })
                .then((response) => {
                    this.payment = response.data.items;
                    console.log(this.payment)
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

       
       

        

        swalFunction(type , text){
          Swal.fire({
                text: text,
                icon: type,
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        },



      


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
@media (max-width: 700px) {
    .pay-commission-card {
        min-width: 0;
        padding: 18px 4vw 18px 4vw;
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
