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
            <input v-model="searchQuery" @input="searchItems" type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search Item ..." />
          </div>
          <!--end::Search-->
        </div>
        <!--begin::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
          <!--begin::Toolbar-->
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base" v-if="selectedItems.length === 0">
          
          
            <button @click="getModalCreate()" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_item">
            <i class="ki-duotone ki-plus fs-2"></i>Add New</button>
            <!--end::Add user-->
          </div>
          <!--end::Toolbar-->
          <!--begin::Group actions-->
          <div class="d-flex justify-content-end align-items-center " v-if="selectedItems.length != 0" >
            <div class="fw-bold me-5">
            <span class="me-2" >{{ selectedItems.length }}</span>Selected</div>
            <button type="button" class="btn btn-danger"  @click="deleteSelected">Delete Selected</button>
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
              <th class="min-w-125px">Message</th>
              <th class="min-w-125px">type</th>
              <th class="min-w-125px">Joined Date</th>
              <th class="text-center min-w-100px">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-semibold">
            <tr v-for="(item, index) in items" :key="item.id">
              <td >
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input v-model="selectedItems" :value="item.id" class="form-check-input" type="checkbox"  />
                </div>
              </td>


              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item.title}}</a>
                </div>
              </td>

              <td class="align-items-center" style="margin-top: 15px;">
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item.body}}</a>
                </div>
              </td>


            

              <td>{{item.created_at}}</td>
             

              <td class="text-end">
                 <div class="d-flex flex-center rounded p-4 h-80px mb-1 overlay">
                    

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
    <div class="modal-dialog modal-dialog-centered mw-650px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">Add Edit Notification</h2>
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


                <div class="fv-row mb-7">
                  <label class="required fw-semibold fs-6 mb-2">Type</label>
                  <select class="form-select form-select-solid" data-placeholder="Select an Type" v-model="formData.target_type">
                    <option v-for="item in itemType" :key="item.key" :value="item.key">{{item.name}}</option>
                  </select>              
                </div>


                <div class="fv-row mb-7" v-if="formData.target_type=='user'">
                  <label class="required fw-semibold fs-6 mb-2">Users</label>
                  <multiselect class="" v-model="formData.users" tag-placeholder="Select " placeholder="Search ..." label="name" track-by="id" :options="itemsUsers" :multiple="true" :taggable="false" :options-limit="10000" :allow-empty="false" ></multiselect>

                </div>

               
                <div class="fv-row mb-7">
                  <label class="required fw-semibold fs-6 mb-2">Title</label>
                  <input  name="name"  class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Title" value="" v-model="formData.title" required />
                </div>


                <div class="fv-row mb-7">
                  <label class="required fw-semibold fs-6 mb-2">Message</label>
                  <textarea  name="name"  class="form-control form-control-solid mb-3 mb-lg-0" placeholder="message" value="" v-model="formData.message" required ></textarea>
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
            searchQuery: '',
            formData: {
                title:'',
                message:'',
                users: [],
                target_type: 'all',
            },

            itemType: [

                {
                    key: 'all',
                    name: "All Users",
                },
                {
                    key: 'user',
                    name: "Specific Users",
                }, 
            ],

            
            ItemID: null,
            URL:'Notifications/createItem',
        };
    },


    mounted() {
       
        this.fetchItems(this.currentPage);
        
    },


  
    methods: {

    
        getModalCreate(){
          this.URL = 'Notifications/createItem'
          this.resetItem();
          $('#kt_modal_add_item').modal('show');
          this.titleModal = 'اضافة جديد '
          this.fetchItemsUsers()


        },

 
      

      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Notifications/createItem'
            this.ItemID = null
        },


      

        resetItem(){
          this.formData.code=''
          this.formData =  {
                message:'',
                users: [],
                target_type: 'all',
            }
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
            await axios.get('Notifications/getAllItems', {
                params: {
                  search: this.searchQuery,
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
        this.isLoading = true;

        const payload = {
          title: this.formData.title,
          message: this.formData.message,
          target_type: this.formData.target_type,
        };

        if (this.formData.target_type === "user") {
          payload.users = (this.formData.users || []).map((u) =>
            u && u.id ? u.id : u
          );
        }

        axios
          .post(this.URL, payload)
          .then((response) => {
            this.isLoading = false;
            const items = response.data.items;
            const sent = items?.sent ?? 0;
            const total = items?.total_tokens ?? 0;
            const failed = items?.failed ?? 0;

            if (response.data.status && sent > 0) {
              swal.fire({
                text: `${response.data.message} — تم الإرسال: ${sent}/${total}${failed ? ` (فشل: ${failed})` : ""}`,
                icon: "success",
                timer: 3000,
                button: false,
              });
              this.closeModal();
              this.fetchItems(this.currentPage);
              return;
            }

            const errDetail =
              items?.errors?.[0] ||
              response.data.message ||
              "فشل إرسال الإشعار.";
            swal.fire({
              text: errDetail,
              icon: "error",
              timer: false,
              button: true,
            });
          })
          .catch((error) => {
            this.isLoading = false;
            const msg =
              error?.response?.data?.message ||
              error?.response?.data?.items?.errors?.[0] ||
              "حدث خطأ أثناء الإرسال.";
            swal.fire({
              text: msg,
              icon: "error",
              timer: false,
              button: true,
            });
          });
      },

      async fetchItemsUsers() {
          this.isLoading = true;
            await axios.get('Users/getAllItems', {
                params: {
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
              axios.delete(`Notifications/deleteItem/${id}`)
                .then(() => {
                
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

