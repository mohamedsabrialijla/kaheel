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
            <input v-model="searchQuery" @input="searchItems" type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search user" />
          </div>
          <!--end::Search-->
        </div>
        <!--begin::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
          <!--begin::Toolbar-->
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base" v-if="selectedItems.length === 0">
            <!-- <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
            <i class="ki-duotone ki-filter fs-2">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>Filter</button> -->
            <!-- <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
              <div class="px-7 py-5">
                <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
              </div>
             
              <div class="separator border-gray-200"></div>
              
              <div class="px-7 py-5" data-kt-user-table-filter="form">
                <div class="mb-10">
                  <label class="form-label fs-6 fw-semibold">Role:</label>
                  <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
                    <option></option>
                    <option value="Administrator">Administrator</option>
                    <option value="Analyst">Analyst</option>
                    <option value="Developer">Developer</option>
                    <option value="Support">Support</option>
                    <option value="Trial">Trial</option>
                  </select>
                </div>
                
                <div class="mb-10">
                  <label class="form-label fs-6 fw-semibold">Two Step Verification:</label>
                  <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
                    <option></option>
                    <option value="Enabled">Enabled</option>
                  </select>
                </div>
                
                <div class="d-flex justify-content-end">
                  <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                  <button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                </div>
              </div>
            </div> -->
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
            <i class="ki-duotone ki-plus fs-2"></i>Add New Item</button>
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
        <table v-else class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_items">
          <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
              <th class="w-10px pe-2">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_items .form-check-input" value="1" v-model="selectAll" @change="toggleAll" />
                </div>
              </th>
              <th class="min-w-125px">Type</th>
              <th class="min-w-125px">Name</th>
              <th class="min-w-125px">description</th>
              <th class="min-w-125px">Created Date</th>
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
              
              <td v-if="item.parent_id">Item</td>
              <td v-else>Group</td>
              
              <td>{{item.display_name}}</td>
              <td style="font-size:10px;">{{item.descriptions}}</td>
              
              <td>{{item.created_at}}</td>
             

              <td class="text-end">
                 <div class="d-flex flex-center rounded p-4 h-80px mb-1 overlay">
                      <div class="overlay-wrapper text-gray-600">
                          <i @click="getModalEdit(item)" class="ki-duotone ki-notepad-edit fs-2x"><span class="path1"></span><span class="path2"></span></i>
                        </div>

                       

                        <div class="overlay-wrapper text-gray-600">
                          <i @click="deleteItem(item)" class="ki-duotone ki-trash-square fs-2x">
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
    <div class="modal-dialog modal-dialog-centered mw-450px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">{{titleModal}}</h2>
          <!--end::Modal title-->
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-icon-primary"  @click="closeModal">
            <i class="ki-duotone ki-cross fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </div>
          <!--end::Close-->
        </div>


        <div class="modal-body px-5 my-7">
          

          <form id="kt_modal_update_role_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#">
              <!--begin::Scroll-->
              <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_role_header" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px" style="max-height: 134px;">
                <!--begin::Input group-->


                <div class="fv-row mb-7">
                  <label class="required fw-semibold fs-6 mb-2" >Type</label>
                  <select class="form-control" v-model="type_select">
                    <option value="group">Group</option>
                    <option value="item">Item</option>
                  </select>

                </div>



                 <div class="fv-row mb-7" v-if="type_select=='item'">
                  <label class="required fw-semibold fs-6 mb-2">Group</label>
                  <multiselect class="" v-model="formData.parent" tag-placeholder="Select " placeholder="Search ..." label="name" track-by="id" :options="groupItems" :multiple="false" :taggable="false" :options-limit="10" :allow-empty="false" ></multiselect>

                </div>
                


                <div class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid" :class="{'error-border': errors.name}">
                    <label class="fs-5 fw-bold form-label mb-2">
                      <span class="required"> Constant Name</span>
                    </label>
                    <input class="form-control form-control-solid" v-model="formData.name" placeholder="Enter Constant name" name="name" value="">
                    
                </div>
 

                <div class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid" :class="{'error-border': errors.name}">
                    <label class="fs-5 fw-bold form-label mb-2">
                      <span class="required"> Display Name</span>
                    </label>
                    <input class="form-control form-control-solid" v-model="formData.display_name" placeholder="Enter Display name" name="name" value="">
                    
                </div>




                <div class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                    <label class="fs-5 fw-bold form-label mb-2">
                      <span class="required">Descriptions</span>
                    </label>
                    <input class="form-control form-control-solid" v-model="formData.descriptions" placeholder="Enter Descriptions" name="name" value="">
                    
                </div>

            


              </div>
              <!--end::Scroll-->
              <!--begin::Actions-->
              <div class="text-center pt-10">
                <button type="submit" class="btn btn-primary" @click.prevent="addEditItem" :disabled="isLoading">
                <span  class="indicator-label">Submit</span>
                <span  class="indicator-progress">Please wait...
                  <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
                </button>
              </div>
              <!--end::Actions-->
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
import Multiselect from 'vue-multiselect'


export default {
  components: {
    Pagination,Multiselect
  },
    data() {
        return {

            titleModal: '', 
            totalItems: 0, // إجمالي العناصر (يمكن تحديثه بناءً على الاستجابة)
            currentPage: 1,
            selectedItems: [], // Selected items for deletion
            selectAllChecked: false,
            selectAll: false, // To track if all items are selected
            searchQuery: '', // الكلمة المفتاحية للبحث
            isLoading: false, // حالة التحميل
            items: [], 
            searchQuery: '',
            formData: {
                display_name: '',
                parent:{},
                name:'',
                descriptions:''
               
            },
            errors: {},
            groupItems:[],
            type_select:'group',

          

            ItemID: null, // المستخدم الذي يتم تعديله
            URL:'Permission/createItem',
        };
    },

    mounted() {
      this.fetchItems(this.currentPage); // استدعاء جلب المستخدمين عند تحميل الصفحة

    },


   
    methods: {

      



        getModalCreate(){
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.fetchItemsGroup()
            this.titleModal = 'New Item'
        },


        getModalEdit(item){
            this.ItemID = item.id
            this.resetItem();
            this.fetchItemsGroup()
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'Edit Item'
            this.URL = 'Permission/editItem'
            this.getItemById() 
        },


      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Permission/createItem'
            this.ItemID = null
            // this.resetMark();
        },



        resetItem(){
          this.URL = 'Permission/createItem'
          this.formData.display_name=''
          this.formData.name=''
          this.formData.descriptions=''
          this.formData.parent={}

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
            await axios.get('Permission/getAllItems', {
                params: {
                  search: this.searchQuery, 
                  pagination: 1,
                  page:page,
                  admin:0,
                }
              })
                .then(response => {
                    this.items = response.data.items.data;
                    // console.log(this.items)
                    this.isLoading = false;
                    this.totalItems = response.data.items.total


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



      async fetchItemsGroup(page,query = '') {
          this.isLoading = true;
            await axios.get('Permission/getAllItems', {
                params: {
                  pagination: 0,
                  group: 1,
                   admin:0,
                }
              })
                .then(response => {
                    this.groupItems = response.data.items;
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





        addEditItem() {
 

          this.isLoading = true


           if(this.ItemID){
               this.formData.Item_id = this.ItemID;
               this.URL = 'Permission/editItem'
            }

            if(this.formData.parent){
              this.formData.parent_id = this.formData.parent.id
            }
            
 
          axios.post(this.URL,this.formData).then((response)=>{
                this.isLoading = false;
                if(response.data.items){
                    this.swalFunction('success','Done successfully')
                    this.closeModal()
                    this.fetchItems()           

                }else{
                  this.isLoading = false
                   this.swalFunction('error','Error happens')
                }             
            
          }).catch(error => {
              this.swalFunction('error',"Error happens");
              this.isLoading = false
          });

          
        },


      

        async getItemById(){

            axios.get('Permission/getById', { params: { ID: this.ItemID } }).then(response => {
                if(response.data){
                  let data = response.data.items
                   this.formData = data;
                 

                }else{
                    this.swalFunction('error','happens Error')
                }
            }).catch((error)=>{

                    this.swalFunction('error','happens Error')     
            });
        },


      


      deleteItem(item) {
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
            axios.delete(`Permission/deleteItem/${item.id}`)
              .then(() => {
                // إزالة العنصر من القائمة بعد الحذف
                this.items = this.items.filter(i => i.id !== item.id);
                this.ItemID = ''
                // إظهار رسالة النجاح
                swal.fire({
                  text: 'The item has been deleted successfully.', 
                  icon: 'success',
                  confirmButtonText: "Ok",
                  buttonsStyling: false,
                  customClass: {
                    confirmButton: "btn btn-light-primary"
                  }
                });
              })
              .catch(error => {
                console.error(error);
                // عرض رسالة خطأ في حال فشل الطلب
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
        this.selectedItems.forEach(item => {
          this.deleteItem(item);
        });
        this.selectedItems = [];
        this.selectAll = false;
      },


     

      toggleSelectAll() {
      this.selectAllChecked = !this.selectAllChecked;
      // Apply selectAllChecked state to all items and sub-items
      this.formData.child_permissions.forEach(item => {
        item.checked = this.selectAllChecked;
        item.child.forEach(subItem => {
          subItem.checked = this.selectAllChecked;
        });
      });
    },


    toggleSubPermissions(item) {
      // Check/uncheck sub-items when the main permission is toggled
      const checked = item.checked;
      item.child.forEach(subItem => {
        subItem.checked = checked;
      });
      // Update 'Select All' status based on the new individual selections
      this.selectAllChecked = this.items.every(item => item.checked && item.child.every(subItem => subItem.checked));
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