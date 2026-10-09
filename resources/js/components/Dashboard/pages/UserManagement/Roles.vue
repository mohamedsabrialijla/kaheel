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
            <i class="ki-duotone ki-plus fs-2"></i>Add User</button>
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
              <th class="min-w-125px">Role</th>
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
              
              <td>{{item.display_name}}</td>
              
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
    <div class="modal-dialog modal-dialog-centered mw-1000px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">Add User</h2>
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
                


                <div class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid" :class="{'error-border': errors.name}">
                    <label class="fs-5 fw-bold form-label mb-2">
                      <span class="required">Role name</span>
                    </label>
                    <input class="form-control form-control-solid" v-model="formData.display_name" placeholder="Enter a role name" name="name" value="Developer">
                    
                </div>

                <div class="fv-row">
                  <!--begin::Label-->
                  <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
                  <!--end::Label-->
                  <!--begin::Table wrapper-->
                  <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                      <!--begin::Table body-->
                      <tbody class="text-gray-600 fw-semibold">
                        <!--begin::Table row-->
                        <tr>
                          <td class="text-gray-800">Administrator Access 
                          <span class="ms-1" data-bs-toggle="tooltip" aria-label="Allows a full access to the system" data-bs-original-title="Allows a full access to the system" data-kt-initialized="1">
                            <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                              <span class="path1"></span>
                              <span class="path2"></span>
                              <span class="path3"></span>
                            </i>
                          </span></td>
                          <td>
                            <!--begin::Checkbox-->
                            <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                              <input v-model="selectAllChecked" @click="toggleSelectAll" class="form-check-input" type="checkbox" value="" id="kt_roles_select_all">
                              <span class="form-check-label" for="kt_roles_select_all" >Select all Permissions</span>
                            </label>
                            <!--end::Checkbox-->
                          </td>
                        </tr>
                        <!--end::Table row-->
                        <!--begin::Table row-->
                        <tr v-for="(item,index) in formData.child_permissions"  :key="item.id">
                          <!--begin::Label-->
                          <td class="text-gray-800">
                            
                            <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20" >
                                <input class="form-check-input" type="checkbox" value="" name="user_management_read" v-model="item.checked" @change="toggleSubPermissions(item)">
                                <span class="form-check-label" style="width:62px;"> {{item.display_name}} </span>
                              </label>

                          </td>

                        

                          <!--end::Label-->
                          <!--begin::Input group-->
                          <td>
                            <!--begin::Wrapper-->
                            <div class="d-flex">
                              <!--begin::Checkbox-->
                              <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20" v-for="(child,index2) in item.child"  :key="child.id">
                                <input v-model="child.checked" class="form-check-input" type="checkbox" value="" name="user_management_read">
                                <span class="form-check-label">{{child.display_name}}</span>
                              </label>
                              <!--end::Checkbox-->
                             
                            </div>
                            <!--end::Wrapper-->
                          </td>
                          <!--end::Input group-->
                        </tr>
                       
                        <!--end::Table row-->
                      </tbody>
                      <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                  </div>
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

export default {
  components: {
    Pagination
  },
    data() {
        return {

            totalItems: 0, // إجمالي العناصر (يمكن تحديثه بناءً على الاستجابة)
            currentPage: 1,
            selectedItems: [], // Selected items for deletion
            selectAllChecked: false,
            selectAll: false, // To track if all items are selected
            searchQuery: '', // الكلمة المفتاحية للبحث
            isLoading: false, // حالة التحميل
            items: [], 
            itemsPermissions: [],
            searchQuery: '',
            formData: {
                display_name: '',
                child_permissions:{

                }
            },
            errors: {},

          

            ItemID: null, // المستخدم الذي يتم تعديله
            URL:'Roles/createItem',
        };
    },

    mounted() {
      this.fetchItems(this.currentPage); // استدعاء جلب المستخدمين عند تحميل الصفحة

    },


    // created() {
    //       this.fetchItems(); // تحميل العناصر بدون فلترة في البداية

    // },
    methods: {

       onFileChange(e) {
          // حفظ الملف المختار
          this.logo = e.target.files[0];
          this.logo_preview = URL.createObjectURL(this.logo);
        },



        getModalCreate(){
            this.fetchItemsPermissions();
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'اضافة جديد '
        },


        getModalEdit(item){
            this.ItemID = item.id
            this.fetchItemsPermissions();
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'تعديل البيانات'
            this.URL = 'Roles/editItem'
            this.getItemById() 
        },


      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Roles/createItem'
            this.ItemID = null
            // this.resetMark();
        },



        resetItem(){
          this.URL = 'Roles/createItem'
          this.formData.display_name=''
          this.formData.child_permissions={}

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


        // جلب جميع المستخدمين
        async fetchItems(page,query = '') {
          this.currentPage = page;
          this.isLoading = true;
            await axios.get('Roles/getAllItems', {
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





         async fetchItemsPermissions() {
              try {
                const response = await axios.get('/Permission/getAllItems?pagination=0&group=1');
                if (response.data.status) {

                  this.formData.name = "";
                  this.formData.child_permissions = response.data.items.map(permission => ({
                    ...permission,
                    checked: permission.checked,
                    child: permission.child.map(subPermission => ({
                      ...subPermission,
                      checked: subPermission.checked
                    }))
                  }));
                } else {
                  this.swalFunction('error','Error happens .');
                }
              } catch (error) {
                this.swalFunction('error',error)
              }
          },
        

        // إضافة مستخدم جديد
        addEditItem() {
          this.isLoading = true
          const payload = {
            item_id: this.ItemID,
            name: this.formData.display_name,
            permissions: this.formData.child_permissions.map(permission => ({
              id: permission.id,
              name: permission.name,
              checked: permission.checked , 
              child: permission.child.map(subPermission => ({
                id: subPermission.id,
                name: subPermission.name,
                checked: subPermission.checked , 
              }))
            }))
          };

          

 
          axios.post(this.URL,payload).then((response)=>{
                this.isLoading = false;
                if(response.data.items){
                   swal.fire({
                    text: "تم حفظ التغييرات بنجاح",
                    icon: "success",
                    timer: 2000,
                    button: false
                    }); 
                    this.closeModal()
                    this.fetchItems()           

                }else{
                  this.isLoading = false
                    swal.fire({
                    text: response.data.message,
                    icon: 'error',
                    timer: false,
                    button: true
                    });
                }             
            
          }).catch(error => {
              this.swalFunction('error',"Error happens");
              this.isLoading = false
          });

          
        },


      

        async getItemById(){

            axios.get('Roles/getById', { params: { ID: this.ItemID } }).then(response => {
                if(response.data){
                  let data = response.data.items
                   // this.formData = data;
                  this.formData.display_name = data.display_name;
                  this.formData.child_permissions = data.permissions;
                 

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
            axios.delete(`Roles/deleteItem/${item.id}`)
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


<style type="text/css">
  .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }

/*.error-border input {
  border: 1px solid red !important;
}
*/

  .form-check-input:checked {
  border-color: red;  /* Border red for error indication */
}
</style>