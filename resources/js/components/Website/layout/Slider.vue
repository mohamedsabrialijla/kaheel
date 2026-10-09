<template>
    <div>
        <div class="container">
                   
                    <div class="row w-100 gy-10 mb-md-20 pt-20">
                        <div class="col-md-6 alignCenter mt-0">
                            <div class="position-relative">
                               <!--  <img style="z-index:9999" 
                                    alt="pattern"
                                    src="assets/media/patern.png"
                                    class="pattern"
                                /> -->
                                <h1 style="font-size:2.8rem !important" 
                                    class="text-white lh-base fw-bold fs-2x fs-lg-3x mb-8"
                                >
                                    {{translations.Create_your_resume_in_moments }}<br /> {{translations.ai_powered}}          </h1>
                                <p class="Paragr" style="font-size: 15px;">
                                    {{translations.cv_highlight}}
                                </p>
                                <div class="actionsBtns">
                                    <a @click="openPage()"
                                        href="#" style="z-index: 99999999999999999999999;"
                                        class="btn btn-color-primary btn-active-light-primary btn-light px-12 me-4"
                                        >{{translations.create_cv_2}}</a
                                    >
                                    <a @click="openPage()"
                                        href="#"
                                        class="btn btn-color-primary btn-active-light-primary btn-light px-12"
                                        >{{translations.download_improve_cv}}</a
                                    >
                                </div>
                                <p class="Paragr mt-4">
                                    {{translations.trusted_by}}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="landingImgLeft">
                                <img
                                    alt="landingLeft"
                                    src="assets/media/landingLeft.png"
                                    class="landingLeft"
                                />
                                <img
                                    alt="landingLeftShadow"
                                    src="assets/media/landingLeftShadow.png"
                                    class="landingLeftShadow"
                                />
                            </div>
                        </div>
                    </div>

                    <!--end::Clients-->
                </div>
    </div>
</template>

<script>


import axios from 'axios';
import { mapActions, mapState } from 'vuex';
import { api } from '../../../axiosUser'; 


export default {
    name: "Slider",
    data() {
        return {
            success: 0,
            translations: window.translations,
            items:[],

        };
    },
    mounted() {    

         this.$store.dispatch('fetchSetting').then(() => {
            this.fetchItems();
        });          
        
    },



 computed: {

        locale() {
            return this.$route.params.locale || 'en';
        },

        setting() {
            return this.$store.getters.getSetting;
        },

        otherLocale() {
            return this.locale === 'en' ? 'ar' : 'en';
        },



        translations() {
          return this.$store.getters.getTranslations;
        }


        
       
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



        openPage(){

            this.$router.push({ path: `/${this.locale}/app/create-template` });


        
        },


        // switchLocale() {
        //     let currentPath = this.$route.path.replace(/^\/(en|ar)/, '');

        //     if (currentPath === '') {
        //         currentPath = '';
        //     }
        //     let updatedPath = `/${this.otherLocale}${currentPath}`;
        //     window.location.href = updatedPath;
        // },
 

        async fetchItems(page,query = '') {
          this.isLoading = true;
            await api.get('ItemsCategories/getAllItems', {
                params: {
                  pagination: 0,
                  page:page,
                  type: 'company',
                }
              })
                .then(response => {
                    this.items = response.data.items;
                    this.isLoading = false;
                })
                .catch(error => {
                  
                  this.swalFunction('error',error)

                   this.isLoading = false;
                });
        },


    },
   









};

</script>
