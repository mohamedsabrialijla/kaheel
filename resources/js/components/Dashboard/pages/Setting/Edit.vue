<template>
           
  <!--begin::Post-->
  <div class="content flex-column-fluid" id="kt_content">
    <!--begin::Card-->
    <div class="card">
      <!--begin::Card header-->
      
      <!--end::Card header-->
      <!--begin::Card body-->
      <div class="card-body py-4">
        <!--begin::Table-->
        <div v-if="isLoading">
          please wait ...
        </div>
        


          <div class="modal-body px-5 my-7">
          <form id="kt_modal_add_user_form" class="form" action="#">
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">


             <!-- VAT -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">VAT</label>
              <div class="col-lg-8">
                <input type="text" placeholder="EX: 5 %" maxlength="12" class="form-control form-control-solid" v-model="formData.vat" required />
              </div>
            </div>

            <!-- Offers Period -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Offers Period</label>
              <div class="col-lg-8">
                <input type="number" placeholder="Offers Period" maxlength="12" class="form-control form-control-solid" v-model="formData.offer_period" required />
              </div>
            </div>

            <!-- Alert to Delete Offer -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Alert to Delete Offer</label>
              <div class="col-lg-8">
                <input type="number" placeholder="Alert to Delete Offer" maxlength="12" class="form-control form-control-solid" v-model="formData.alert_delete_offer" required />
              </div>
            </div>

            <!-- Days to Delete Offer -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Days to Delete Offer</label>
              <div class="col-lg-8">
                <input type="number" placeholder="Days to Delete Offer" maxlength="12" class="form-control form-control-solid" v-model="formData.days_delete_offer" required />
              </div>
            </div>

            <!-- Refresh Reminder Days -->
            <div class="row mb-5">
              <label class="col-lg-4 col-form-label fw-semibold fs-6">عدد أيام طلب التحديث (Refresh Reminder Days)</label>
              <div class="col-lg-8">
                <input type="number" min="0" max="365" placeholder="EX: 5" class="form-control form-control-solid" v-model="formData.refresh_reminder_days" />
                <div class="form-text">كل كم يوم يُرسل إشعار لأصحاب الإعلانات لطلب تحديث الإعلان. 0 = إيقاف.</div>
              </div>
            </div>

            <!-- Play Store -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Play Store Link</label>
              <div class="col-lg-8">
                <input type="url" placeholder="Link here..." maxlength="255" class="form-control form-control-solid" v-model="formData.play_store" required />
              </div>
            </div>

            <!-- Apple Store -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Apple Store Link</label>
              <div class="col-lg-8">
                <input type="url" placeholder="Link here..." maxlength="255" class="form-control form-control-solid" v-model="formData.apple_store" required />
              </div>
            </div>

            <!-- Dynamic keywords -->
            <div v-for="lang in languages" :key="'key_words_' + lang.locale" class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} Keywords</label>
              <div class="col-lg-8">
                <input type="text" :name="'key_words_' + lang.locale" placeholder="Keywords" class="form-control form-control-solid" v-model="formData['key_words_' + lang.lang]" required />
              </div>
            </div>



            <!-- Dynamic keywords -->
            <div v-for="lang in languages" :key="'key_words_' + lang.locale" class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} Alert Gold</label>
              <div class="col-lg-8">
                <input type="text" :name="'alert_gold_' + lang.locale" placeholder="Keywords" class="form-control form-control-solid" v-model="formData['alert_gold_' + lang.lang]" required />
              </div>
            </div>



            <!-- Dynamic names -->
            <div v-for="lang in languages" :key="'name_' + lang.locale" class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} Name</label>
              <div class="col-lg-8">
                <input type="text" :name="'name_' + lang.locale" :placeholder="`${lang.name} Name`" class="form-control form-control-solid" v-model="formData['name_' + lang.lang]" required />
              </div>
            </div>

            <!-- First Condition -->
            <div v-for="lang in languages" :key="'condition1_' + lang.locale" class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} First Condition</label>
              <div class="col-lg-8">
                <textarea rows="6" maxlength="250" :placeholder="`${lang.name} First Condition`" class="form-control form-control-solid" v-model="formData['condition1_' + lang.lang]"></textarea>
              </div>
            </div>

            <!-- Second Condition -->
            <div v-for="lang in languages" :key="'condition2_' + lang.locale" class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} Second Condition</label>
              <div class="col-lg-8">
                <textarea rows="6" maxlength="250" :placeholder="`${lang.name} Second Condition`" class="form-control form-control-solid" v-model="formData['condition2_' + lang.lang]"></textarea>
              </div>
            </div>

            <!-- Mobile -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Mobile</label>
              <div class="col-lg-8">
                <input type="text" placeholder="EX:00966556625489" maxlength="12" class="form-control form-control-solid" v-model="formData.mobile" required />
              </div>
            </div>

            <!-- Email -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Email</label>
              <div class="col-lg-8">
                <input type="email" placeholder="Email" maxlength="255" class="form-control form-control-solid" v-model="formData.email" required />
              </div>
            </div>

            <!-- WhatsApp -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">WhatsApp</label>
              <div class="col-lg-8">
                <input type="url" placeholder="WhatsApp link" class="form-control form-control-solid" v-model="formData.whatsapp" required />
              </div>
            </div>



            <div class="row mb-5">
              <label class="col-lg-4 col-form-label fw-semibold fs-6">Logo</label>
              <div class="col-lg-8">
                <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                  <div class="image-input-wrapper w-125px h-125px" :style="{'background-image': formData.logo ? `url(${formData.logo})` : 'url(/assets/avatar.svg)'}"></div>
                  <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                    <i class="ki-duotone ki-pencil fs-7"></i>
                    <input type="file" @change="onFileChange" accept=".png, .jpg, .jpeg, .webp" />
                    <input type="hidden" name="avatar_remove" />
                  </label>
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                    <i class="ki-duotone ki-cross fs-2"></i>
                  </span>
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                    <i class="ki-duotone ki-cross fs-2"></i>
                  </span>
                </div>
                <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
              </div>
            </div>

            

              





            </div>
            <div class="text-center pt-10">
              <button type="submit" class="btn btn-primary" @click.prevent="addEditItem" :disabled="isLoading">
              <span  class="indicator-label">Submit</span>
              <span  class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
              </span>
            </button>


          
            </div>
          </form>
        </div>

  
        <!--end::Table-->
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Card-->
  </div>
  <!--end::Post-->







             
</template>

<script>
import axios from 'axios';
import Pagination from '../../layout/pagination.vue';
import Multiselect from 'vue-multiselect'

export default {
  components: {
    Pagination,Multiselect
  },
    data() {
        return {
          languages:{},
            totalItems: 0, // إجمالي العناصر (يمكن تحديثه بناءً على الاستجابة)
            currentPage: 1,
            selectedItems: [], // Selected items for deletion
            selectAll: false, // To track if all items are selected
            searchQuery: '', // الكلمة المفتاحية للبحث
            isLoading: false, // حالة التحميل
            items: [], 
            itemsPosition: [], 
            searchQuery: '',
            formData: {
                mobile: '',
                email: '',
                whatsapp: '',
                condition1: '',
                condition2: '',
                vat: '',
                offer_period: '',
                alert_delete_offer: '',
                days_delete_offer: '',
                refresh_reminder_days: 0,
                play_store: '',
                apple_store: '',
                logo: '',
                logo_preview  :'./../assets/media/avatars/300-1.jpg',
            },

           

            ItemID: null, // المستخدم الذي يتم تعديله
            URL:'Settings/editItem',
        };
    },

    mounted() {
       this.getItemById() 

    },


  
    methods: {

       onFileChange(e) {
        this.logo = e.target.files[0];
            const file = e.target.files[0];
            if (file) {
                this.formData.logo = URL.createObjectURL(file);
            }
        },



        resetItem(){
          this.URL = 'Settings/editItem'
          this.formData.mobile=''
          this.formData.email=''
          this.formData.whatsapp=''
          this.formData.vat=''
          this.formData.offer_period=''
          this.formData.days_delete_offer=''
          this.formData.alert_delete_offer=''
          this.formData.refresh_reminder_days=0
          this.formData.play_store=''
          this.formData.apple_store=''
          this.formData.logo=''
          this.logo_preview ='./../assets/companies/img/store-logo.jpg'

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


       


       


        addEditItem() {

          
          this.isLoading = true;

          const config = {
                  headers: {
                      'content-type': 'multipart/form-data'
                  }
              }

            let form = new FormData();
            
              form.append('mobile', this.formData.mobile);
              form.append('email', this.formData.email);
              form.append('whatsapp', this.formData.whatsapp);
              form.append('vat', this.formData.vat);
              form.append('offer_period', this.formData.offer_period);
              form.append('alert_delete_offer', this.formData.alert_delete_offer);
              form.append('days_delete_offer', this.formData.days_delete_offer);
              form.append('refresh_reminder_days', this.formData.refresh_reminder_days ?? 0);
              form.append('play_store', this.formData.play_store);
              form.append('apple_store', this.formData.apple_store);


               this.languages.forEach((element) => { 
                    form.append( 'name_' + element.lang ,this.formData['name_'+element.lang] ); 
                    form.append( 'key_words_' + element.lang ,this.formData['key_words_'+element.lang]); 
                    form.append( 'alert_gold_' + element.lang ,this.formData['alert_gold_'+element.lang]); 
                    form.append( 'condition1_' + element.lang ,this.formData['condition1_'+element.lang] ); 
                    form.append( 'condition2_' + element.lang ,this.formData['condition2_'+element.lang] ); 
                    
              });




              if (this.logo) {
                form.append('logo', this.logo);
              }
           
            if(this.ItemID != ''){
               form.append('Item_id', this.ItemID);
            }

             
          axios.post(this.URL,form,config).then((response)=>{
                 this.isLoading = false;
                if(response.data.items){
                   this.swalFunction('success',response.data.message)
                    this.getItemById()           

                }else{
                  this.isLoading = false;
                    swal.fire({
                    text: response.data.message,
                    icon: 'error',
                    timer: false,
                    button: true
                    });

                }             
            
          }).catch(error => {
            this.isLoading = false;
                    this.swalFunction('error',error)
                });

          
        },


        
        async getItemById(){

            axios.get('Settings/getById', { params: { ID: this.ItemID } }).then(response => {
                if(response.data){
                  let data = response.data.items
                   this.formData = data;
                   this.languages = this.$store.state.languages ;

                    data.translations.forEach((element) => { 
                              this.formData['name_'+element.locale] = element.name; 
                              this.formData['key_words_'+element.locale] = element.key_words; 
                              this.formData['alert_gold_'+element.locale] = element.alert_gold; 
                              this.formData['condition1_'+element.locale] = element.condition1; 
                              this.formData['condition2_'+element.locale] = element.condition2; 
                        });

                }else{
                    Swal.fire({
                        text: "Error happens",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }).catch((error)=>{

                    this.swalFunction('error',error)    
            });
        },


     

        

        
     
    },

 
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>




    ol, p, ul {
        line-height: 1.0;
    }


  .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }



</style>