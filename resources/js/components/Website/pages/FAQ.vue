<template>
    <div>
        <TitleHead :title="t.FAQS" :subtitle="t.FAQSDEs"></TitleHead>
        
             <div id="kt_app_content" class="app-content">
    <div class="card">
      <div class="card-body p-lg-15">
        <div class="mb-13">
          <div class="mb-15">
           
          </div>
          <!--end::Intro-->
          <!--begin::Row-->
          <div class="row mb-12">
              <!--begin::Col-->
             <div class="col-md-6 pe-md-10 mb-10 mb-md-0" v-for="(itemArray, key) in items" :key="key">
                  <h2 class="text-gray-800 fw-bold mb-4" v-if="itemArray.length > 0">{{ itemArray[0].category.name }}</h2>
                  
                  <div v-for="(item, index) in itemArray" :key="item.id">
                      
                      <div class="m-0">
                        <div class="d-flex align-items-center collapsible py-3 toggle mb-0" 
                             :data-bs-toggle="`collapse`" 
                             :data-bs-target="`#kt_job_4_${key}_${index}`">
                          <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                            <i class="fa" :class="index === 0 ? 'fa-minus-square' : 'fa-plus-square'"></i>
                          </div>
                          <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">{{ item.name }}</h4>
                        </div>
                      
                        <div :id="`kt_job_4_${key}_${index}`" 
                             :class="{'collapse': index !== 0, 'show': index === 0}" 
                             class="fs-6 ms-1">
                          <div class="mb-4 text-gray-600 fw-semibold fs-6 ps-10">{{ item.description }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                      </div>
                      
                  </div>
              </div>

            
          </div>
       
        </div>
      
      </div>
    </div>
  </div>


    </div>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from "vuex";
import { api } from "../../../axiosUser";
import Companies from "../layout/Companies.vue";
import TitleHead from "../layout/TitleHead.vue";
export default {
    name: "about",
    components: {
        TitleHead,
        Companies,
    },
    data() {
        return {
            // t: window.translations,
            translations: window.translations,
            items: [],
        };
    },

    created() {},

    mounted() {

        this.$store.dispatch("fetchSetting").then(() => {
            this.fetchItems();
        });
        
    },
    

    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        setting() {
            return this.$store.getters.getSetting;
        },

        t() {
          return this.$store.getters.getTranslations;
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




        async fetchItems() {
          this.isLoading = true;
            await axios.get('ItemsCategories/getAllItems', {
                params: {
                  search: this.searchQuery,
                  pagination: 0,
                  type: 'faq',
                  groupBy: 'category_id',
                }
              })
                .then(response => {
                    this.items = response.data.items;
                    this.isLoading = false;
                    this.languages = this.$store.state.languages ;
                })
                .catch(error => {
                  this.swalFunction('error',error)
                   this.isLoading = false;
                });
        },



    },
};
</script>
<style type="text/css"></style>
