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
          
           
            <!-- <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
            <i class="ki-duotone ki-exit-up fs-2">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>Export</button> -->
            
            <button @click="getModalCreate()" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_item">
            <i class="ki-duotone ki-plus fs-2"></i>Add User</button>
            <!--end::Add user-->
          </div>
          <!--end::Toolbar-->
          <!--begin::Group actions-->
          <div class="d-flex justify-content-end align-items-center " v-if="selectedItems.length != 0" >
            <div class="fw-bold me-5">
            <span class="me-2" >{{ selectedItems.length }}</span>Selected</div>
            <button type="button" class="btn btn-light-danger margin-5 btn-sm"  @click="actionSelected('delete')">Delete Selected</button>
            <button type="button" class="btn btn-light-success margin-5 btn-sm"  @click="actionSelected('active')">Active</button>
            <button type="button" class="btn btn-light-secondary margin-5 btn-sm"  @click="actionSelected('not_active')">Not Active</button>
            <button type="button" class="btn btn-light-info margin-5 btn-sm"  @click="actionSelected('verfied')">verified</button>
            <button type="button" class="btn btn-light-secondary margin-5 btn-sm"  @click="actionSelected('unverfied')">Un verified</button>
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
              <th class="min-w-125px">User</th>
              <th class="min-w-125px">Status</th>
              <th class="min-w-125px">Verification</th>
              <th class="min-w-125px">ADs</th>
              <th class="min-w-125px">Contact Us</th>
              <th class="text-center min-w-100px">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-semibold">
            <tr v-for="(item, index) in items" :key="item.id"  :class="{ 'highlight-red': item.id == selectedUserId }">
              <td >
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input v-model="selectedItems" :value="item.id" class="form-check-input" type="checkbox"  />
                </div>
              </td>
              <td class="d-flex align-items-center" style="margin-top: 15px;">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                  <a href="#">
                    <div class="symbol-label">
                      <img :src="item.logo" alt="item.name" class="w-100" />
                    </div>
                  </a> 
                </div> 
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                  <div class="d-flex align-items-center gap-2">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1" @click.prevent="openChatWithUser(item)">{{item.name}}</a>
                    <i @click="openChatWithUser(item)" class="ki-duotone ki-message-text-2 fs-4 text-primary cursor-pointer" title="Send Message" style="cursor: pointer;">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                    </i>
                  </div>
                  <span>{{item.email}}</span>
                </div>
                <!--begin::User details-->
              </td>
              

               <td>
                <div v-if="item.status=='active'" class="badge badge-light-success fw-bold">{{item.status}}</div>
                <div v-else class="badge badge-light-danger fw-bold">{{item.status}}</div>

                <div class="rating">
                  <div 
                    v-for="i in 5" 
                    :key="i" 
                    class="rating-label" 
                    :class="{ checked: i <= item.average_rating }"
                  >
                    <i class="ki-solid ki-star fs-6"></i>
                  </div>
                </div>


              </td>


              <td>
                <div v-if="item.verification==1" class="badge badge-light-primary fw-bold">verified</div>
                <div v-else class="badge badge-light-secondary fw-bold">un verified</div>
              </td>


              

              <td>
                <i class="ki-duotone ki-grid-frame fs-2x cursor">
                   <span class="path1"></span>
                   <span class="path2"></span>
                   <span class="path3"></span>
                </i>
              </td>


              <td>
                <i class="ki-duotone ki-badge fs-2x cursor">
                 <span class="path1"></span>
                 <span class="path2"></span>
                 <span class="path3"></span>
                 <span class="path4"></span>
                 <span class="path5"></span>
                </i>
              </td>
             

              <td class="text-end">
                 <div class="d-flex flex-center rounded p-4 h-80px mb-1 overlay">
                      <div class="overlay-wrapper text-gray-600">
                          <i @click="openChatWithUser(item)" class="ki-duotone ki-message-text-2 fs-2x text-primary" title="Send Message" style="cursor: pointer;">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                          </i>
                        </div>

                      <div class="overlay-wrapper text-gray-600">
                          <i @click="getModalEdit(item)" class="ki-duotone ki-notepad-edit fs-2x"><span class="path1"></span><span class="path2"></span></i>
                        </div>

                        <div class="overlay-wrapper text-gray-600">
                          <i @click="getModalEditPassword(item)" class="ki-duotone ki-user-edit fs-2x"><span class="path1"></span><span class="path2"></span></i>
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
    <div class="modal-dialog modal-dialog-centered mw-650px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">Add User</h2>
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



              <div class="fv-row mb-7" style="margin: auto;">
                <!--begin::Label-->
                <label class="d-block fw-semibold fs-6 mb-5">Image</label>
              
                <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                  <!--begin::Preview existing avatar-->

                  <div class="image-input-wrapper w-125px h-125px" :style="{'background-image': formData.logo ? `url(${formData.logo})` : 'url(/assets/avatar.svg)'}"></div>

                
                  <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                    <i class="ki-duotone ki-pencil fs-7">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                    <input type="file" name="avatar" @change="onFileChange" accept=".png, .jpg, .jpeg" />
                    <input type="hidden" name="avatar_remove" />
                  </label>
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                    <i class="ki-duotone ki-cross fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                    <i class="ki-duotone ki-cross fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                </div>
                <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
              </div>
             
             <!-- Full Name -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Full Name</label>
              <div class="col-lg-8">
                <input type="text" 
                       name="name" 
                       class="form-control form-control-solid" 
                       placeholder="Full name" 
                       v-model="formData.name" 
                       required />
              </div>
            </div>

            <!-- Email -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Email</label>
              <div class="col-lg-8">
                <input type="email" 
                       name="email" 
                       class="form-control form-control-solid" 
                       placeholder="example@domain.com" 
                       v-model="formData.email" 
                       required />
              </div>
            </div>

            <!-- Mobile -->
            <div class="row mb-5">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Mobile</label>
              <div class="col-lg-8">
                <input type="text" 
                       name="mobile" 
                       class="form-control form-control-solid" 
                       placeholder="EX:00966556625489" 
                       maxlength="12" 
                       v-model="formData.mobile" 
                       required />
              </div>
            </div>

            <!-- Password -->
            <div class="row mb-5" v-if="!ItemID">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Password</label>
              <div class="col-lg-8">
                <input type="password" 
                       name="password" 
                       class="form-control form-control-solid" 
                       placeholder="Your password" 
                       v-model="formData.password" 
                       required />
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="row mb-5" v-if="!ItemID">
              <label class="required col-lg-4 col-form-label fw-semibold fs-6">Confirm Password</label>
              <div class="col-lg-8">
                <input type="password" 
                       name="confirm_password" 
                       class="form-control form-control-solid" 
                       placeholder="Your confirm password" 
                       v-model="formData.confirm_password" 
                       required />
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



<!--begin::Modal - Add task-->
  <div class="modal fade" id="kt_modal_add_item_password" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
      <!--begin::Modal content-->
      <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header" id="kt_modal_add_user_header">
          <!--begin::Modal title-->
          <h2 class="fw-bold">Edit password</h2>
          <!--end::Modal title-->
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" @click="closeModalPassword">
            <i class="ki-duotone ki-cross fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </div>
          <!--end::Close-->
        </div>


        <div class="modal-body px-5 my-7">
          <form id="kt_modal_add_user_form_password" class="form" action="#">
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">



             


              <div class="fv-row mb-7" >
                <label class="required fw-semibold fs-6 mb-2">Password</label>
                <input type="password" name="password" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Your password" v-model="formData.password" value="" required />
              </div>


              <div class="fv-row mb-7" >
                <label class="required fw-semibold fs-6 mb-2">confirm password</label>
                <input type="password" v-model="formData.confirm_password" name="confirm_password" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Your confirm password"  value="" required />
              </div>


          




            </div>
            <div class="text-center pt-10">
              <button type="submit" class="btn btn-primary" @click.prevent="addEditItemPassword" :disabled="isLoading">
              <span  class="indicator-label">Submit</span>
              <span  class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
              </span>
            </button>

             <button style="margin: 7px;" type="submit" class="btn btn-secondary" @click.prevent="closeModalPassword">
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

export default {
  components: {
    Pagination
  },
    data() {
        return {
            totalItems: 0, // إجمالي العناصر (يمكن تحديثه بناءً على الاستجابة)
            currentPage: 1,
            selectedItems: [], // Selected items for deletion
            selectAll: false, // To track if all items are selected
            searchQuery: '', // الكلمة المفتاحية للبحث
            isLoading: false, // حالة التحميل
            items: [], 
            searchQuery: '',
            formData: {
                name: '',
                email: '',
                password: '',
                logo: '',
                logo_preview  :'./../assets/media/avatars/300-1.jpg',
            },

            formDataPassword: {
                password: '',
                confirm_password: '',
               
            },


            ItemID: null, // المستخدم الذي يتم تعديله
            URL:'Users/createItem',

            selectedUserId: null,
        };
    },

    mounted() {
      this.fetchItems(this.currentPage); 

       this.$nextTick(() => {
            if (this.$route.query.openModal && this.$route.query.userId) {
              this.selectedUserId = Number(this.$route.query.userId);
            }
          });

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
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'اضافة جديد '
        },


        getModalEdit(item){
            this.ItemID = item.id
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'تعديل البيانات'
            this.URL = 'Users/editItem'
            this.getItemById() 
        },


        getModalEditPassword(item){
            this.ItemID = item.id
            this.resetItemPassword();
            $('#kt_modal_add_item_password').modal('show');
            this.titleModal = 'تعديل الباسوورد'
            this.getItemById() 
        },

        openChatWithUser(item) {
            // إرسال بيانات المستخدم إلى Vuex store لفتح المحادثة
            this.$store.commit('setOpenChatWithUser', {
                id: item.id,
                name: item.name,
                avatar: item.logo || '/default-avatar.png'
            });
            console.log('✅ Chat request sent for user:', item.name, item.id);
        },

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Users/createItem'
            this.ItemID = null
            // this.resetMark();
        },


        closeModalPassword(){
            $('#kt_modal_add_item_password').modal('hide');
            this.ItemID = null
        },

        resetItem(){
          this.URL = 'Users/createItem'
          this.formData.mobile=''
          this.formData.email=''
          this.formData.name=''
          this.formData.logo=''
          this.logo_preview ='./../assets/companies/img/store-logo.jpg'

        },



         resetItemPassword(){
          this.formData.password=''
          this.formData.confirm_password=''
          

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
            await axios.get('Users/getAllItems', {
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


        

     

        // إضافة مستخدم جديد
        addEditItem() {

          
          this.isLoading = true;

          const config = {
                  headers: {
                      'content-type': 'multipart/form-data'
                  }
              }

            let form = new FormData();
              form.append('name', this.formData.name);
              form.append('mobile', this.formData.mobile);
              form.append('email', this.formData.email);
              if(this.ItemID == null){
                form.append('password', this.formData.password);
                form.append('confirm_password', this.formData.confirm_password);
              }

              if (this.logo) {
                form.append('logo', this.logo);
              }
           
            if(this.ItemID != ''){
               form.append('Item_id', this.ItemID);
            }
             
          axios.post(this.URL,form,config).then((response)=>{
                 this.isLoading = false;
                if(response.data.items){
                   this.swalFunction('success','Doing successfully')
                    this.closeModal()
                    this.fetchItems()           

                }else{
                    this.swalFunction('error','Error happens')
                }             
            
          }).catch(error => {
                   this.swalFunction('error','Error happens')
                });

          
        },


        addEditItemPassword() {

          
          this.isLoading = true;

          const config = {
                  headers: {
                      'content-type': 'multipart/form-data'
                  }
              }


            let form = new FormData();
           
                form.append('password', this.formData.password);
                form.append('confirm_password', this.formData.confirm_password);
                form.append('Item_id', this.ItemID);
             
          axios.post('Users/EditItemPassword',form,config).then((response)=>{
                 this.isLoading = false;
                if(response.data.items){
                   swal.fire({
                    text: "تم حفظ التغييرات بنجاح",
                    icon: "success",
                    timer: 2000,
                    button: false
                    }); 
                    this.closeModalPassword()
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
                    swal.fire({
                    text: error,
                    icon: 'error',
                    timer: false,
                    button: true
                    });
                });

          
        },


        async getItemById(){

            axios.get('Users/getById', { params: { ID: this.ItemID } }).then(response => {
                if(response.data){
                  let data = response.data.items
                   this.formData = data;

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
            axios.delete(`Users/deleteItem/${id}`)
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
              axios.delete(`Users/deleteAllItem`, {
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


     

        

        
     
    },

  //    watch: {
  //     selectedItems() {
  //       this.selectAll = this.selectedItems.length === this.items.length;
  //     }
  // }
};
</script>


<style type="text/css" scoped>
  .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }


  .btn-sm{
    margin: 5px;
  }

  .cursor{
    cursor: pointer;
  }


  .rating-label {
    padding: 0px;
    background: inherit;

  }


.highlight-red {

    background-color: #e4a92a9c;

  }
</style>