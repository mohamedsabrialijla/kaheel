<template>
    <div>
        
            <div style="background-color:#ffffff; padding: 45px 0 34px 0; border-radius: 24px; margin:40px auto; max-width: 600px;">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" height="auto" style="border-collapse:collapse">
                                <tbody>
                                    <tr>
                                        <td align="center" valign="center" style="text-align:center; padding-bottom: 10px">
                                            <!--begin:Email content-->
                                            <div style="text-align:center; margin:0 15px 34px 15px">
                                                <!--begin:Logo-->
                                                <div style="margin-bottom: 10px">
                                                    <router-link :to="`/${locale}`" rel="noopener" >
                                                        <img alt="Logo" :src="setting
                                                    ? setting.logo
                                                    : 'Loading...'" style="height: 35px">
                                                    </router-link>  
                                                </div>
                                                <!--end:Logo-->
                                                <!--begin:Media-->
                                                <div style="margin-bottom: 15px" v-if="status === 'success'" >
                                                    <img alt="Logo" :src="'/assets/media/email/icon-positive-vote-1.svg'">
                                                </div>


                                                 <div style="margin-bottom: 15px" v-else >
                                                    <img alt="Logo" :src="'/assets/media/auth/404-error-dark.png'" width="100px">
                                                </div>
                                                <!--end:Media-->
                                              
                                                   
                                                    

                                                <p v-if="status === 'success'" style="margin-bottom:9px; color:#181C32; font-size: 22px; font-weight:700">
                                                    {{ translations?.verified_title }} ({{ email }})
                                                     <b style="margin-bottom:2px; color:#7E8299">
                                                        {{ translations?.verified_message }}
                                                    </b>

                                                </p>

                                                <p v-else-if="status === 'info'" style="margin-bottom:9px; color:#181C32; font-size: 22px; font-weight:700">
                                                    {{ translations?.verify_already }}
                                                </p>

                                                <p v-else style="margin-bottom:9px; color:#181C32; font-size: 22px; font-weight:700">
                                                    {{ translations?.verify_invalid }}
                                                </p>

 


                                                <!--end:Text-->
                                                <!--begin:Action-->
                                                <router-link :to="`/${locale}`"  class="btn btn-primary">{{translations?.home}}</router-link>
                                                <!--begin:Action-->
                                            </div>
                                            <!--end:Email content-->
                                        </td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>

    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            errors: [],
            t: window.translations,
            isLoading: false,
            status: null,
            message: null,
            email: null, 
            
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting");

        const { status, message, email } = this.$route.query;
        this.status = status;
        this.message = message;
            this.email = decodeURIComponent(this.$route.query.email || '');


        // أظهر alert حسب الحالة
        // if (status === 'error') {
        //     this.swalFunction('error', this.getMessage(message));
        // } else if (status === 'info') {
        //     this.swalFunction('info', this.getMessage(message));
        // } else if (status === 'success') {
        //     this.swalFunction('success', this.getMessage(message));
        // }
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


         getMessage(key) {
            const messages = {
                verified_success: this.translations?.verified_title + ' ' + (this.email ?? ''),
                invalid: this.translations?.verify_invalid,
                already_verified: this.translations?.verify_already,
            };
            return messages[key] || key;
        },



       


    },
};
</script>
