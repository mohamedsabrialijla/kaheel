<template>
           
  <!--begin::Post-->
  <div class="content flex-column-fluid" id="kt_content">
    <!--begin::Card-->
    <div class="card">
      <!--begin::Card header-->
      <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
          <!--begin::Search-->
          <div class="d-flex align-items-center position-relative my-1">
            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
            <input v-model="searchQuery" @input="searchItems" type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search item ..." />
          </div>
          <!--end::Search-->
        </div>
        <!--begin::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
          <!--begin::Toolbar-->
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base" v-if="selectedItems.length === 0">
          
            <!--end::Menu 1-->
            <!--end::Filter-->
            <!--begin::Export-->
            <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_export_items">
            <i class="ki-duotone ki-exit-up fs-2">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>Export</button>
            <!--end::Export-->
            <!--begin::Add user-->
            <button @click="getModalCreate()" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_item">
            <i class="ki-duotone ki-plus fs-2"></i>Add New</button>
            <!--end::Add user-->
          </div>
          <!--end::Toolbar-->
          <!--begin::Group actions-->
          <div class="d-flex justify-content-end align-items-center justing" v-if="selectedItems.length != 0" >
            <div class="fw-bold me-5">
            <span class="me-2" >{{ selectedItems.length }}</span>Selected</div>
            <button type="button" class="btn btn-light-danger margin-5"  @click="actionSelected('delete')">Delete Selected</button>
            <button type="button" class="btn btn-light-success margin-5"  @click="actionSelected('active')">Active</button>
            <button type="button" class="btn btn-light-secondary margin-5"  @click="actionSelected('not_active')">Not Active</button>
          </div>
          <!--end::Group actions-->
          
          
        </div>
        <!--end::Card toolbar-->
      </div>
      <!--end::Card header-->
      <!--begin::Card body-->
      <div class="card-body py-4">
        <!--begin::Table-->
        <div v-if="isLoading">
          please wait ...
        </div>
        <table v-else class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
          <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
              <th class="w-10px pe-2">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" v-model="selectAll" @change="toggleAll" />
                </div>
              </th>
              <th class="min-w-125px">image</th>
              <th class="min-w-125px">User</th>
              <th class="min-w-125px">Brand</th>
              <th class="min-w-125px">Title</th>
              <th class="min-w-125px">Status</th>
              <th class="min-w-125px">Joined Date</th>
              <th class="text-center min-w-100px">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-semibold">
            <tr v-for="(item, index) in items" :key="item.id"  :class="{ 'highlight-red': item.id == selectedAdId }">
              <td >
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input v-model="selectedItems" :value="item.id" class="form-check-input" type="checkbox"  />
                </div>
              </td>


               <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <img v-if="item.image_basic" :src="item.image_basic.url" class="" alt="" style="width: 70px;">
                </div>
              </td>

              

              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1" v-if="item.user">{{item.user.name}}</a>
                  <span>{{item.user.email}} </span>
                </div>
              </td>

             


              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1" v-if="item.category">{{item.brand.name}}</a>
                  <span>{{item.category.name}} </span>
                </div>
              </td>


              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item.title}}</a>
                  <!-- <span>{{item.code}} </span> -->
                </div>
              </td>



              <td>
                <div v-if="item.status=='active'" class="badge badge-light-success fw-bold">{{item.status}}</div>
                <div v-else class="badge badge-light-danger fw-bold">{{item.status}}</div>
              </td>
              <td>{{item.created_at}}</td>
             

              <td class="text-end">
                 <div class="d-flex flex-center rounded p-4 h-80px mb-1 overlay">
                      <div class="overlay-wrapper text-gray-600">
                          <i @click="getModalEdit(item)" class="ki-duotone ki-notepad-edit fs-2x"><span class="path1"></span><span class="path2"></span></i>
                        </div>



                      <div class="overlay-wrapper text-gray-600">
                        <i @click="getModalFav(item)" class="ki-duotone ki-heart-circle fs-2x"><span class="path1"></span><span class="path2"></span></i>
                      </div>



                      <div class="overlay-wrapper text-gray-600">
                        <i class="ki-duotone ki-messages fs-2x" @click="getModalComment(item)" >
                         <span class="path1"></span>
                         <span class="path2"></span>
                         <span class="path3"></span>
                         <span class="path4"></span>
                         <span class="path5"></span>
                        </i>
                      </div>

                         


                        <div class="overlay-wrapper text-gray-600">
                          <i @click="deleteItem(item.id)" class="ki-duotone ki-trash-square fs-2x">
                           <span class="path1"></span>
                           <span class="path2"></span>
                           <span class="path3"></span>
                           <span class="path4"></span>
                          </i>
                        </div>
                            
                  </div>
              </td>
            </tr>
       
          </tbody>
        </table>

        <!-- <pagination begin  -->
        <pagination :totalItems="totalItems" :currentPage="currentPage" @page-changed="fetchItems"></pagination>
        <!-- <pagination end  -->

        <!--end::Table-->
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Card-->
  </div>
  <!--end::Post-->









<!--begin::Modal - Add task-->
  <div class="modal fade" id="kt_modal_add_item" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-950px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">Add Edit Item</h2>
          <!--end::Modal title-->
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" @click="closeModal">
            <i class="ki-duotone ki-cross fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </div>
          <!--end::Close-->
        </div>


        <div class="modal-body px-5 my-7">
          <form id="kt_modal_add_user_form" class="form" action="#">
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">









          <!-- User -->
          <div class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">User</label>
            <div class="col-lg-8">
              <multiselect v-model="formData.user" placeholder="Search..." label="name" track-by="id"
                           :options="itemsUsers" :multiple="false" :taggable="false" class="form-control"></multiselect>
            </div>
          </div>

          <!-- Category -->
          <div class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">Category</label>
            <div class="col-lg-8">
              <multiselect v-model="formData.category" placeholder="Search..." label="name" track-by="id"
                           :options="itemsCategories" :multiple="false" :taggable="false" class="form-control"></multiselect>
            </div>
          </div>

          <!-- Brand -->
          <div class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">Brand</label>
            <div class="col-lg-8">
              <multiselect @open="fetchItemsCategoriesBrands2()" v-model="formData.brand"  placeholder="Search..." label="name" track-by="id"
                           :options="itemsBrands" :multiple="false" :taggable="false" class="form-control"></multiselect>
            </div>
          </div>
           
          <!-- Country -->
          <div class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">Country</label>
            <div class="col-lg-8">
              <multiselect v-model="formData.country" placeholder="Search..." label="name" track-by="id"
                           :options="itemsCountries" :multiple="false" :taggable="false" class="form-control"></multiselect>
            </div>
          </div>

          <!-- City -->
          <div class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">City</label>
            <div class="col-lg-8">
              <multiselect v-model="formData.city" placeholder="Search..." label="name" track-by="id"
                           :options="itemsCities" :multiple="false" :taggable="false" class="form-control"></multiselect>
            </div>
          </div>

          <!-- Titles -->
          <div v-for="lang in languages" :key="lang.locale" class="row mb-5">
            <label :for="'title_' + lang.locale" class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} Title</label>
            <div class="col-lg-8">
              <input type="text" :name="'title_' + lang.locale" class="form-control form-control-solid" :placeholder="'title_' + lang.lang"
                     v-model="formData['title_' + lang.lang]" required />
            </div>
          </div>

          <!-- Descriptions -->
          <div v-for="lang in languages" :key="lang.locale + '_desc'" class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">{{ lang.name }} Description</label>
            <div class="col-lg-8">
              <textarea rows="6" maxlength="250" v-model="formData['description_' + lang.lang]"
                        placeholder="Description" class="form-control form-control-solid"></textarea>
            </div>
          </div>

          <!-- Price -->
          <div class="row mb-5">
            <label class=" col-lg-4 col-form-label fw-semibold fs-6">Price</label>
            <div class="col-lg-8">
              <input type="text" class="form-control form-control-solid" placeholder="Price" v-model="formData.price" required />
            </div>
          </div>

          <!-- Mobile -->
          <div class="row mb-5">
            <label class=" col-lg-4 col-form-label fw-semibold fs-6">Mobile</label>
            <div class="col-lg-8">
              <input type="text" class="form-control form-control-solid" placeholder="Mobile" v-model="formData.mobile" required />
            </div>
          </div>

          <!-- Product Type -->
          <div class="row mb-5">
            <label class="required col-lg-4 col-form-label fw-semibold fs-6">Product Type</label>
            <div class="col-lg-8">
              <select class="form-control form-control-solid" v-model="formData.product_type">
                <option value="new" selected>New</option>
                <option value="used">Used</option>
              </select>
            </div>
          </div>

          <!-- Invoice Upload -->
          <div class="row mb-5"style="margin: auto;">
            <label class=" col-lg-4 col-form-label fw-semibold fs-6">Invoice</label>
            <a :href="formData.invoice"  target="_blank" v-if="ItemID"> open File </a>
            <div class="col-lg-8">
              <input type="file" class="form-control form-control-solid" accept=".pdf" @change="handleInvoiceUpload" />
              <div class="form-text">Allowed file types: PDF.</div>
            </div>
          </div>





                <!-- Images Upload Preview -->
                 <!-- صور المعاينة -->
          <div class="d-flex flex-wrap gap-4 mb-5" style="margin: auto;">
            <div v-for="(image, index) in formData.imagesPreview" :key="index" class="position-relative">
              <img
                :src="image"
                class="rounded border"
                style="width: 100px; height: 100px; object-fit: cover;"
              />
              <button
                type="button"
                style="width: 20px; height: 20px;"
                class="btn btn-sm btn-icon btn-light-danger position-absolute top-0 end-0"
                @click="removeImage(index)"
              >
                <i class="ki-duotone ki-cross fs-1">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>
              </button>
            </div>




          <div class="d-flex align-items-center gap-4">
            <!-- جعل صورة الرفع نفسها زرًا -->
            <label
              class="image-input-wrapper w-100px h-100px rounded"
              style="background-image: url('/assets/avatar.svg'); background-size: cover; background-position: center; cursor: pointer;"
              title="اضغط لاختيار الصور"
            >
              <input
                type="file"
                multiple
                accept="image/*"
                @change="handleImageUpload"
                style="display: none;"
              />
            </label>
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

              <button style="margin: 7px;" type="submit" class="btn btn-secondary" @click.prevent="closeModal">
              <span  class="indicator-label">Canacel</span>
              
            </button>

            </div>
          </form>
        </div>


      </div>
    </div>
  </div>
<!--begin::Modal - Add task-->




<!--begin::Modal - Comments-->
  <div class="modal fade" id="kt_modal_add_item_comments" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-950px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">Comments</h2>
          <!--end::Modal title-->
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" @click="closeModalComment">
            <i class="ki-duotone ki-cross fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </div>
          <!--end::Close-->
        </div>


        <div class="modal-body px-5 my-7">
          <form id="kt_modal_add_user_form" class="form" action="#">
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">



               <div class="table-responsive">
              <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
               <!--  <thead>
                  <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                    <th class="p-0 pb-3 min-w-150px text-start">AUTHOR</th>
                    <th class="p-0 pb-3 min-w-100px text-end pe-13">CONV.</th>
                    <th class="p-0 pb-3 w-125px text-end pe-7">CHART</th>
                    <th class="p-0 pb-3 w-50px text-end">VIEW</th>
                  </tr>
                </thead> -->
                <tbody>
                  
                  
                  <tr v-for="(f,index) in comments" :key="f.id"  :class="{ 'background-warning': f.isOwner }" >
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-3">
                          <img :src="f?.user?.logo" class="" alt="">
                        </div>
                        <div class="d-flex justify-content-start flex-column">
                          <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">{{f.body}}</a>
                          <span class="text-gray-500 fw-semibold d-block fs-7">{{f.user.name}}</span>
                        </div>
                      </div>
                    </td>
                   
                    <td class="text-end pe-0">
                     
                      {{f.created_at_human}} 
                    </td>
                    <td class="text-end">
                      <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                        <i class="ki-duotone ki-trash-square fs-2x" @click="deleteComment(f.id)">
                           <span class="path1"></span>
                           <span class="path2"></span>
                           <span class="path3"></span>
                           <span class="path4"></span>
                          </i>
                      </a>
                    </td>
                  </tr>


              </tbody>
              </table>
            </div>







            </div>
            <div class="text-center pt-10">
              <button style="margin: 7px;" type="submit" class="btn btn-secondary" @click.prevent="closeModalComment">
                <span  class="indicator-label">Canacel</span>
              </button>
            </div>



          </form>
        </div>


      </div>
    </div>
  </div>
<!--begin::Modal - Comments task-->



 

<!--begin::Modal - Comments-->
  <div class="modal fade" id="kt_modal_add_item_favs" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">favourites</h2>
          <!--end::Modal title-->
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" @click="closeModalFav">
            <i class="ki-duotone ki-cross fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </div>
          <!--end::Close-->
        </div>


        <div class="modal-body px-5 my-7">
          <form id="kt_modal_add_user_form" class="form" action="#">
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">



            <div class="table-responsive">
              <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
               <!--  <thead>
                  <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                    <th class="p-0 pb-3 min-w-150px text-start">AUTHOR</th>
                    <th class="p-0 pb-3 min-w-100px text-end pe-13">CONV.</th>
                    <th class="p-0 pb-3 w-125px text-end pe-7">CHART</th>
                    <th class="p-0 pb-3 w-50px text-end">VIEW</th>
                  </tr>
                </thead> -->
                <tbody>
                  
                  
                  <tr v-for="(f,index) in favs" :key="f.id"  >
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-3">
                          <img :src="f.logo" class="" alt="">
                        </div>
                        <div class="d-flex justify-content-start flex-column">
                          <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">{{f.name}}</a>
                          <span class="text-gray-500 fw-semibold d-block fs-7">{{f.country.name}} - {{f.city.name}}</span>
                        </div>
                      </div>
                    </td>
                    <td class="text-end pe-13">
                      <span class="text-gray-600 fw-bold fs-6">{{f.account_age}}</span>
                    </td>
                    <td class="text-end pe-0">
                     

                    </td>
                    <!-- <td class="text-end">
                      <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                        <i class="ki-duotone ki-black-right fs-2 text-gray-500"></i>
                      </a>
                    </td> -->
                  </tr>


              </tbody>
              </table>
            </div>









            </div>
            <div class="text-center pt-10">
              <button style="margin: 7px;" type="submit" class="btn btn-secondary" @click.prevent="closeModalFav">
                <span  class="indicator-label">Canacel</span>
              </button>
            </div>



          </form>
        </div>


      </div>
    </div>
  </div>
<!--begin::Modal - Comments task-->



             
</template>

<script>
import axios from 'axios';
import Pagination from '../../layout/pagination.vue';
import { mapState } from 'vuex';
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
            itemsUsers: [], 
            itemsCategories: [],
            itemsCountries:[], 
            itemsCities:[], 
            parent_id:'',
            parent_id2:'',
            itemsBrands: [], 
            searchQuery: '',
            formData: {
                user_id: '',
                category_id: '',
                brand_id: '',
                country_id: '',
                city_id: '',
                price: '',
                mobile: '',
                product_type: 'new',
                invoice: '',
                

                images: [],
                imagesPreview: [],

                existingImages: [],
            },

            
            ItemID: null, // المستخدم الذي يتم تعديله
            URL:'Ads/createItem',

            comments: [],
            favs: [],


            invoice: '',
            invoice_preview  :'./../assets/media/avatars/300-1.jpg',


            selectedAdId: null,


        };
    },


    


    mounted() {
       
         this.fetchItems(this.currentPage)

         this.$nextTick(() => {
            if (this.$route.query.openModal && this.$route.query.adId) {
              this.selectedAdId = Number(this.$route.query.adId);
            }
          });



    },

 
   
    methods: {


      async urlToFile(url) {
        const response = await fetch(url);
        const blob = await response.blob();
        const filename = url.substring(url.lastIndexOf('/') + 1);
        return new File([blob], filename, { type: blob.type });
      },

  



      handleImageUpload(event) {
        const files = Array.from(event.target.files);

        files.forEach((file) => {
          this.formData.images.push(file);

          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.imagesPreview.push(e.target.result);
          };
          reader.readAsDataURL(file);
        });
      },

      removeImage(index) {
        this.formData.images.splice(index, 1);
        this.formData.imagesPreview.splice(index, 1);
      },




     // handleImageUpload(event) {
     //    const files = Array.from(event.target.files);

     //    if (!this.formData.images) this.formData.images = [];
     //    if (!this.formData.imagesPreview) this.formData.imagesPreview = [];

     //    files.forEach((file) => {
     //      this.formData.images.push(file);

     //      const reader = new FileReader();
     //      reader.onload = (e) => {
     //        this.formData.imagesPreview.push(e.target.result);
     //      };
     //      reader.readAsDataURL(file);
     //    });
     //  },

     //  removeImage(index) {
     //    this.formData.images.splice(index, 1);
     //    this.formData.imagesPreview.splice(index, 1);
     //  },



        handleInvoiceUpload(e) {
          const file = e.target.files[0];
          if (!file) return;
          
          this.invoice = file;
          this.invoice_preview = URL.createObjectURL(file);
        },
         


        async fetchItemsUsers() {
          this.isLoading = true;
            await axios.get('Users/getAllItems', {
                params: {
                  search: this.searchQuery,  // تمرير قيمة البحث
                  pagination: 0,
                }
              })
                .then(response => {
                    this.itemsUsers = response.data.items;
                    this.isLoading = false;
                })
                .catch(error => {
                   Swal.fire({
                text: error,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });

                   this.isLoading = false;
                });
        }, 

 
        async fetchItemsCategories() {
            axios.get('/ItemsCategories/getAllItems?pagination=0', {
                params: {
                    pagination: 0,
                    // parent_id: this.parent_id,
                    
                } 
            })
            .then(response => {
              this.ItemsOverall = response.data.items;
                this.itemsCategories = this.ItemsOverall.filter(item => item.type && item.type.name === "category");
                this.itemsCountries = this.ItemsOverall.filter(item => item.type && item.type.name === "country");
            })
            .catch(error => {
                console.log(error);
            });
        },


 

        async fetchItemsCategoriesBrands() {
            axios.get('/ItemsCategories/getAllItems?pagination=0', {
                params: {
                    pagination: 0,
                    parent_id: this.parent_id,
                    
                } 
            })
            .then(response => {
              this.itemsBrands = response.data.items;
               
            })
            .catch(error => {
                console.log(error);
            });
        },

        async fetchItemsCategoriesBrands2() {
          
          // alert(this.formData.category.id)

            axios.get('/ItemsCategories/getAllItems?pagination=0', {
                params: {
                    pagination: 0,
                    parent_id: this.formData.category.id,
                    
                } 
            })
            .then(response => {
              this.itemsBrands = response.data.items;
               
            })
            .catch(error => {
                console.log(error);
            });
        },



        parentID(selected) {
            if(!this.ItemID || this.ItemID == ''){
              this.parent_id = selected.id 
              this.fetchItemsCategoriesBrands()
            }
        },



        async fetchItemsCategoriesCities() {
            axios.get('/ItemsCategories/getAllItems?pagination=0', {
                params: {
                    pagination: 0,
                    parent_id2: this.parent_id2,
                    
                } 
            })
            .then(response => {
              this.itemsCities = response.data.items;
               
            })
            .catch(error => {
                console.log(error);
            });
        },



         parentID2(selected) {
            this.parent_id2 = selected.id 
            this.fetchItemsCategoriesCities()
          },


   
        getModalCreate(){
            this.resetItem();
            this.URL = 'Ads/createItem'
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'Add New'
            this.fetchItemsUsers()
            this.fetchItemsCategories()

        },
 

        getModalComment(ad){
            $('#kt_modal_add_item_comments').modal('show');
            this.comments = ad.comments

        },

        closeModalComment(){
            $('#kt_modal_add_item_comments').modal('hide');
        },




        getModalFav(ad){
            $('#kt_modal_add_item_favs').modal('show');
            this.favs = ad.favorited_by
            

        },

        closeModalFav(){
            $('#kt_modal_add_item_favs').modal('hide');
        },



        getModalEdit(item){
            this.ItemID = item.id
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'Edit Item'
            this.getItemById() 
            this.URL = 'Ads/editItem'
            this.fetchItemsUsers()
            this.fetchItemsCategories()



        },


      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Ads/createItem'
            this.ItemID = null
            // this.resetMark();
        },


      

        resetItem(){
          this.URL = 'Ads/createItem'
          this.formData =  {
                user: '',
                category: '',
                brand: '',
                country: '',
                city: '',
                price: '',
                product_type: 'new',
            }
          this.languages.forEach((element) => { 
                this.formData['title_'+element.lang] = ''; 
                this.formData['description_'+element.lang] = ''; 
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


        searchItems() {
          this.fetchItems(this.searchQuery); // استدعاء الدالة مع الكلمة المفتاحية
        },


        async fetchItems(page,query = '') {
          this.currentPage = page;
          this.isLoading = true;
            await axios.get('Ads/getAllItems', {
                params: {
                  search: this.searchQuery,  // تمرير قيمة البحث
                  pagination: 1,
                  page:page,
                }
              })
                .then(response => {
                    this.items = response.data.items.data;
                    this.isLoading = false;
                    this.totalItems = response.data.items.total
                    this.languages = this.$store.state.languages ;
                })
                .catch(error => {
                   Swal.fire({
                text: error,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });

                   this.isLoading = false;
                });
        },


    
       

        addEditItem() {

          // console.log(this.formData)
          this.isLoading = true;
          const config = {
                  headers: {
                      'content-type': 'multipart/form-data'
                  }
              }

            let form = new FormData();
              form.append('user_id', this.formData.user.id);
              form.append('category_id', this.formData.category.id);
              form.append('brand_id', this.formData.brand.id);
              form.append('country_id', this.formData.country.id);
              form.append('city_id', this.formData.city.id);
              
              form.append('mobile', this.formData.mobile);
              form.append('price', this.formData.price);
              form.append('product_type', this.formData.product_type);


              form.append('title_ar', this.formData.title_ar);
              form.append('title_en', this.formData.title_en);
              form.append('description_ar', this.formData.description_ar);
              form.append('description_en', this.formData.description_en);
             
              if (this.invoice) {
                form.append('invoice', this.invoice);
              }
           
            



 


          // this.formData.images.forEach((image, index) => {
          //   form.append(`images[${index}]`, image);
          // });

              this.formData.images.forEach((file, index) => {
                if (file instanceof File) {
                  form.append('images[]', file);
                } else {
                  console.warn(`Skipped invalid file at index ${index}:`, file);
                }
              });


            if(this.ItemID != ''){
              form.append('Item_id', this.ItemID);
                this.URL = 'Ads/editItem'

            }

            console.log("Images before send:", this.formData.images);

             
          axios.post(this.URL,form,config).then((response)=>{
                 this.isLoading = false;
                if(response.data.items){
                   swal.fire({
                    text: "Save successfully",
                    icon: "success",
                    timer: 2000,
                    button: false
                    }); 
                    this.closeModal()
                    this.fetchItems()           

                }else{
                    swal.fire({
                    text: response.data.message,
                    icon: 'error',
                    timer: false,
                    button: true
                    });
                }             
            
          }).catch(error => {
                  this.isLoading = false;
                    swal.fire({
                    text: error,
                    icon: 'error',
                    timer: false,
                    button: true
                    });
                });

          
        },




        async loadOldImages(imageUrls) {
          this.formData.images = []; 
          this.formData.imagesPreview = [];

          for (const url of imageUrls) {
            try {
              const file = await this.urlToFile(url); // تنتظر التحويل
              this.formData.images.push(file);

              const reader = new FileReader();
              reader.onload = (e) => {
                this.formData.imagesPreview.push(e.target.result);
              };
              reader.readAsDataURL(file);
            } catch (error) {
              console.error('Error converting URL to file:', error);
            }
          }
        },
          

        async getItemById(){

            axios.get('Ads/getById', { params: { ID: this.ItemID } }).then(response => {
                if(response.data){
                  let data = response.data.items
                  let translations = response.data.items.translations
                  this.resetItem()

                  this.formData = data;
                  // this.formData.imagesPreview = this.formData.images.map(img => img.url);
                  // this.formData.existingImages = this.formData.images.map(img => img.url);
                  this.loadOldImages(this.formData.images.map(img => img.url))

                   translations.forEach((element) => { 
                              this.formData['title_'+element.locale] = element.title; 
                              this.formData['description_'+element.locale] = element.description; 
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

                    swal({
                    text: 'Error happens',
                    icon: 'error',
                    timer: false,
                    button: true
                    });         
            });
        },


    
        deleteItem(id) {
          swal.fire({
            text: "Are you sure you want to delete this item?",
            icon: "warning",
            buttonsStyling: false,
            showDenyButton: true,
            confirmButtonText: "Yes",
            denyButtonText: 'No',
            customClass: {
              confirmButton: "btn btn-primary",
              denyButton: "btn btn-light-danger"
            }
          }).then((result) => {
            if (result.isConfirmed) {
              // إذا تم تأكيد الحذف
              axios.delete(`Ads/deleteItem/${id}`)
                .then(() => {
                  // this.items = this.items.filter(i => i.id !== item.id);
                  // this.ItemID = ''
                  this.swalFunction('success','The item has been deleted successfully.')
                  this.fetchItems()
                })
                .catch(error => {
                  swal.fire({
                    text: 'Error deleting the item. Please try again.', 
                    icon: 'error',
                    confirmButtonText: "Ok",
                    buttonsStyling: false,
                    customClass: {
                      confirmButton: "btn btn-light-primary"
                    }
                  });
                });
            } else if (result.isDenied) {
              // إذا تم رفض الحذف
              swal.fire({
                text: 'The deletion has been canceled.', 
                icon: 'info',
                confirmButtonText: "Ok",
                buttonsStyling: false,
                customClass: {
                  confirmButton: "btn btn-light-primary"
                }
              });
            }
          });
        },

       toggleAll() {
          if (this.selectAll) {
            this.selectedItems = this.items.map(item => item.id);
          } else {
            this.selectedItems = [];
          }

        },
      
        deleteSelected() {
          this.selectedItems.forEach(id => {
            this.deleteItem(id);
          });
          this.selectedItems = [];
          this.selectAll = false;
        },


         actionSelected(action) {
 

          swal.fire({
            text: "Are you sure you want to action this items?",
            icon: "warning",
            buttonsStyling: false,
            showDenyButton: true,
            confirmButtonText: "Yes",
            denyButtonText: 'No',
            customClass: {
              confirmButton: "btn btn-primary",
              denyButton: "btn btn-light-danger"
            }
          }).then((result) => {
            if (result.isConfirmed) {
              axios.delete(`Ads/deleteAllItem`, {
                  params: {
                    selectedItems: this.selectedItems.join(','),
                    action: action 
                  }
                })
                .then(() => {
                  
                  // this.swalFunction('success','The item has been action successfully.')
                  this.fetchItems()
                   this.selectedItems = [];
                   this.selectAll = false;
                })
                .catch(error => {
                  swal.fire({
                    text: 'Error deleting the item. Please try again.', 
                    icon: 'error',
                    confirmButtonText: "Ok",
                    buttonsStyling: false,
                    customClass: {
                      confirmButton: "btn btn-light-primary"
                    }
                  });
                });
            } else if (result.isDenied) {
              // إذا تم رفض الحذف
              swal.fire({
                text: 'The deletion has been canceled.', 
                icon: 'info',
                confirmButtonText: "Ok",
                buttonsStyling: false,
                customClass: {
                  confirmButton: "btn btn-light-primary"
                }
              });
            }
          });
         
        },


        deleteComment(id) {
          swal.fire({
            text: "Are you sure you want to delete this item?",
            icon: "warning",
            buttonsStyling: false,
            showDenyButton: true,
            confirmButtonText: "Yes",
            denyButtonText: 'No',
            customClass: {
              confirmButton: "btn btn-primary",
              denyButton: "btn btn-light-danger"
            }
          }).then((result) => {
            if (result.isConfirmed) {
              axios.delete(`Ads/deleteComment/${id}`)
                .then(() => {
                  $('#kt_modal_add_item_comments').modal('hide');
                  this.swalFunction('success','The item has been deleted successfully.')
                  this.fetchItems()
                })
                .catch(error => {
                  swal.fire({
                    text: 'Error deleting the item. Please try again.', 
                    icon: 'error',
                    confirmButtonText: "Ok",
                    buttonsStyling: false,
                    customClass: {
                      confirmButton: "btn btn-light-primary"
                    }
                  });
                });
            } else if (result.isDenied) {
              // إذا تم رفض الحذف
              swal.fire({
                text: 'The deletion has been canceled.', 
                icon: 'info',
                confirmButtonText: "Ok",
                buttonsStyling: false,
                customClass: {
                  confirmButton: "btn btn-light-primary"
                }
              });
            }
          });
        },
      
     
  },



  watch: {
    'formData.category'(newValue) {
      if(!this.ItemID || this.ItemID == ''){
        this.parentID(newValue);
      }
      
    },



    'formData.country'(newValue) {
      if(!this.ItemID || this.ItemID == ''){
        this.parentID2(newValue);
      }
    },


 

}


 
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style type="text/css" scoped>
  .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }



  .justing {
    gap: 7px;
  }


  .multiselect {
    border: none !important;
  }


  .multiselect--above {
    background: white !important; 
  }

.background-warning{
  background-color: var(--bs-warning-light);
}


.highlight-red {

    background-color: #e4a92a9c;

  }


</style>