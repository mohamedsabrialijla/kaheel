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
           <!--  <button @click="getModalCreate()" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_item">
            <i class="ki-duotone ki-plus fs-2"></i>Add New</button> -->
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
                NO.
              </th>
              <th class="min-w-125px">User</th>
              <th class="min-w-125px">ads</th>
              <th class="min-w-125px">Price</th>
              <th class="min-w-125px">From</th>
              <th class="min-w-125px">P.Type</th>
              <th class="min-w-125px">Status</th>
              <th class="min-w-125px">Joined Date</th>
              <th class="text-center min-w-100px">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-semibold">
            <tr v-for="(item, index) in items" :key="item.id"  :class="{ 'highlight-red': item.id == selectedOrderId }">
              <td >
                 {{item.order_number}}
              </td>


             
              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1" v-if="item.user">{{item?.user?.name}}</a>
                  <span>{{item?.user?.mobile}} </span>
                </div>
              </td>

             


           

              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item?.ad?.title}}</a>
                  <span v-if="item?.ad?.price">{{item.ad.price}} SAR</span>
                </div>
              </td>



              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item.amount}} SAR</a>
                  <!-- <span v-if="item?.ad?.price">{{item.ad.price}} </span> -->
                </div>
              </td>

              <td>
                {{item.payment_from}}
              </td>

              <td v-if="item.payment_type">
                {{item?.payment_type}}
              </td>

              <td v-else>
                Not Decid
              </td>



              <td>
                <div  :class="statusClass(item.status)" >{{item.status}}</div>
              </td>
              <td>{{item.created_at}}</td>
             

              <td class="text-end">
                 <div class="d-flex flex-center rounded p-4 h-80px mb-1 overlay">
                      <div class="overlay-wrapper text-gray-600">

                          <i @click="getModalEdit(item)" class="ki-duotone ki-exit-right-corner fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                          </i>


                          <i @click="openInvoice(item)" class="fas fa-file-invoice" style="padding: 5px;"></i>


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
          <h2 class="fw-bold">Order Details</h2>
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
                <div class="row">
                    
                    <!-- user info -->
                    <div class="col-md-6 col-xxl-4">
                      <div class="card">
                        <div class="card-body d-flex flex-center flex-column py-9 px-5">
                          <div class="symbol symbol-65px symbol-circle mb-5">
                            <img :src="itemCurrent?.user?.logo" alt="image">
                            
                          </div>
                         
                          <p class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{itemCurrent?.user?.name}}</p>
                          <div class="fw-semibold text-gray-500 mb-6">{{itemCurrent?.user?.country?.name}} - {{itemCurrent?.user?.city?.name}}</div>
                         
                          <div class="d-flex flex-center flex-wrap mb-5">
                            <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                              <div class="fs-6 fw-bold text-gray-700">{{itemCurrent?.user?.email}}</div>
                              <div class="fw-semibold text-gray-500">{{itemCurrent?.user?.mobile}}</div>
                            </div>
                           
                          </div>
                          <!-- <button class="btn btn-sm btn-light-primary btn-flex btn-center">
                            
                            <span class="indicator-label">{{itemCurrent?.user?.gender}}</span>
                            
                          </button> -->
                        </div>
                      </div>
                    </div>


                    <!-- ad info -->
                    <div class="col-md-6 col-xxl-8">
                      <div class="card">
                        <div class="card-body d-flex flex-center flex-column py-9 px-5">
                          <div class="symbol symbol-65px symbol-circle mb-5">
                            <img :src="itemCurrent?.ad?.image_basic?.url" alt="image">
                            
                          </div>
                         
                          <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{itemCurrent?.ad?.title}}</a>
                          <div class="fw-semibold text-gray-500 mb-6">{{itemCurrent?.ad?.user?.name}}</div>
                          <div class="fw-semibold text-gray-500 mb-6">{{itemCurrent?.ad?.user?.mobile}}</div>
                         
                          <div class="d-flex flex-center flex-wrap mb-5">
                            <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                              <div class="fs-6 fw-bold text-gray-700">{{itemCurrent?.ad?.category?.name}} - {{itemCurrent?.ad?.brand?.name}}</div>



                              <div class="fs-6 fw-bold text-gray-700">payment Track ID: {{itemCurrent?.track_id}} </div>
                              <div class="fs-6 fw-bold text-gray-700">Order Number: {{itemCurrent?.order_number}} </div>
                              <div class="fs-6 fw-bold text-gray-700">Price: {{itemCurrent?.amount}} SAR</div>
                              <div class="fs-6 fw-bold text-gray-700" v-if="itemCurrent?.payment_type">payment Type: {{itemCurrent?.payment_typ}} SAR</div>
                              <div class="fs-6 fw-bold text-gray-700">payment Date: {{itemCurrent?.created_at}}</div>
                              
                            </div>
                            
                          </div>
                           <div  :class="statusClass(itemCurrent?.status)" >{{itemCurrent?.status}} </div>
                        </div>
                      </div>
                    </div>



                </div>

              </div>



            <div class="text-center pt-10">

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
            itemsBrands: [], 
            searchQuery: '',
            formData: {
                user_id: '',
            },

            
            ItemID: null, 
            itemCurrent: null,

            selectedOrderId:null,

        };
    },


    mounted() {
       
        this.fetchItems(this.currentPage);

         this.$nextTick(() => {
            if (this.$route.query.openModal && this.$route.query.orderId) {
              this.selectedOrderId = Number(this.$route.query.orderId);
            }
          });


    },


   
    methods: {


      statusClass(status) {
        switch ((status || '').toLowerCase()) {
          case 'paid':
          case 'success':
            return 'badge badge-light-success fw-bold';
          case 'unpaid':
            return 'badge badge-light-danger fw-bold';
          case 'pending':
            return 'badge badge-light-warning fw-bold';
          default:
            return 'badge badge-light-info fw-bold';
        }
      },

 
       

        getModalEdit(item){
            this.ItemID = item.id
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'Show Details'
            this.getItemById(item) 
            this.URL = 'Ads/editItem'

        },


      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Ads/createItem'
            this.ItemID = null
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
            await axios.get('Orders/getAllItems', {
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


    
       






        async getItemById(item){

            this.itemCurrent = item ;

          
        },


       openInvoice(item) {
          const url = `/${this.$route.params.locale}/invoice/${item.track_id}`;
          window.open(url, "_blank"); // "_blank" تفتح في تاب جديدة
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