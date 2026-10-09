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
            <!-- <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_export_items">
            <i class="ki-duotone ki-exit-up fs-2">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>Export</button> -->
            <!--end::Export-->
            <!--begin::Add user-->
          <!--   <button @click="getModalCreate()" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_item">
            <i class="ki-duotone ki-plus fs-2"></i>Add New</button> -->
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
                #_NO
              </th>
              <th class="min-w-125px">Name</th>
              <th class="min-w-125px">Slug</th>
              <th class="min-w-125px">Joined Date</th>
              <th class="text-center min-w-100px">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-semibold">
            <tr v-for="(item, index) in items" :key="item.id">
              <td >
                {{index + 1}}
              </td>



              <td class="align-items-center" style="margin-top: 15px;">
                <!--begin:: Avatar -->
               
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item.name}}</a>
                  <!-- <span>{{item.code}} </span> -->
                </div>
                <!--begin::User details-->
              </td>
              <td>
                <div class="badge badge-light-success fw-bold">{{item.slug}}</div>
              </td>
              <td>{{item.created_at}}</td>
             

              <td class="text-end">
                 <div class="d-flex flex-center rounded p-4 h-80px mb-1 overlay">
                      <div class="overlay-wrapper text-gray-600">
                          <i @click="getModalEdit(item)" class="ki-duotone ki-notepad-edit fs-2x"><span class="path1"></span><span class="path2"></span></i>
                        </div>

                        


                        <!-- <div class="overlay-wrapper text-gray-600">
                          <i @click="deleteItem(item.id)" class="ki-duotone ki-trash-square fs-2x">
                           <span class="path1"></span>
                           <span class="path2"></span>
                           <span class="path3"></span>
                           <span class="path4"></span>
                          </i>
                        </div> -->
                            
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


 

               <div class="fv-row mb-7" v-for="lang in languages" :key="lang.locale">
                <label class="required fw-semibold fs-6 mb-2" :for="'key_words_' + lang.locale">{{ lang.name }} Keywords</label>
                <input type="text" :name="'key_words_' + lang.locale" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Name" value="" v-model="formData['key_words_' + lang.lang]"   required />
              </div>
             
             
              <div class="fv-row mb-7" v-for="lang in languages" :key="lang.locale">
                <label class="required fw-semibold fs-6 mb-2" :for="'name_' + lang.locale">{{ lang.name }} Name</label>
                <input type="text" :name="'name_' + lang.locale" class="form-control form-control-solid mb-3 mb-lg-0" placeholdr="{{ lang.name }} Name" value="" v-model="formData['name_' + lang.lang]"   required />
              </div>

            

              <div class="fv-row mb-7" v-for="lang in languages" :key="lang.locale">
                <label class="required fw-semibold fs-6 mb-2">{{ lang.name }} Description</label>
                <textarea
                    rows="6"
                    :id="'messageContent_' + lang.lang"
                    maxlength="250"
                    placeholder="Description"
                    class="form-control"
                  ></textarea>
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







             
</template>

<script>
import axios from 'axios';
import Pagination from '../../layout/pagination.vue';
import { mapState } from 'vuex';



export default {
  components: {
    Pagination
  },
    data() {
        return {
            languages: [
        { lang: 'en', name: 'English' },
        { lang: 'ar', name: 'Arabic' },
      ],
            totalItems: 0, // إجمالي العناصر (يمكن تحديثه بناءً على الاستجابة)
            currentPage: 1,
            selectedItems: [], // Selected items for deletion
            selectAll: false, // To track if all items are selected
            searchQuery: '', // الكلمة المفتاحية للبحث
            isLoading: false, // حالة التحميل
            items: [], 
            itemsRoles: [], 
            searchQuery: '',
            formData: {

            },

            editors: {}, 

            
            ItemID: null, // المستخدم الذي يتم تعديله
            URL:'Pages/createItem',
        };
    },


    mounted() {
       
        this.fetchItems(this.currentPage); 

   

    },


   
    methods: {

    
        // getModalCreate(){
        //     this.resetItem();
        //     this.URL = 'Pages/createItem'
        //     $('#kt_modal_add_item').modal('show');
        //     this.titleModal = 'Add Item'

        //       this.languages.forEach((lang) => {
        //         const elementId = '#messageContent_' + lang.lang;

        //         ClassicEditor.create(document.querySelector(elementId))
        //           .then((editor) => {
        //             // تخزين المحرر
        //             this.editors[lang.lang] = editor;

        //             // تحديث formData عند أي تغيير
        //             editor.model.document.on('change:data', () => {
        //               this.formData['description_' + lang.lang] = editor.getData();
        //             });
        //           })
        //           .catch((error) => {
        //             console.error('CKEditor initialization error:', error);
        //           });
        //       });

       // },


        getModalEdit(item){
            this.ItemID = item.id
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'Edit Item'
            this.getItemById() 
            this.URL = 'Pages/editItem'
             // this.languages.forEach((lang) => {
             //    const elementId = '#messageContent_' + lang.lang;

             //    ClassicEditor.create(document.querySelector(elementId))
             //      .then((editor) => {
             //        // تخزين المحرر
             //        this.editors[lang.lang] = editor;

             //        // تحديث formData عند أي تغيير
             //        editor.model.document.on('change:data', () => {
             //          this.formData['description_' + lang.lang] = editor.getData();
             //        });
             //      })
             //      .catch((error) => {
             //        console.error('CKEditor initialization error:', error);
             //      });
             //  });




        },


      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Pages/createItem'
            this.ItemID = null
            // this.resetMark();
        },


      

        resetItem(){
          this.URL = 'Pages/createItem'
          this.formData.code=''
          this.formData =  {
            }
          // this.languages.forEach((element) => { 
          //       this.formData['name_'+element.lang] = ''; 
          //       this.formData['description_'+element.lang] = ''; 
          // });


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
            await axios.get('Pages/getAllItems', {
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
          
          this.isLoading = true;
           
            if(this.ItemID  != ''){
               this.formData.Item_id = this.ItemID;
                this.URL = 'Pages/editItem'

            }
             
          axios.post(this.URL,this.formData).then((response)=>{
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
                    swal.fire({
                    text: response.data.message,
                    icon: 'error',
                    timer: false,
                    button: true
                    });
                }             
            
          }).catch(error => {
                  console.log(error)
                });

          
        },


        
async getItemById() {
    axios.get('Pages/getById', { params: { ID: this.ItemID } }).then(response => {
        if (response.data) {
            let data = response.data.items;
            let translations = response.data.items.translations;
            this.resetItem();

            this.formData = data;

            // إعداد بيانات الترجمة
            translations.forEach((element) => {
                this.formData['name_' + element.locale] = element.name;
                this.formData['key_words_' + element.locale] = element.key_words;
                this.formData[`description_${element.locale}`] = element.description || '';
            });

            this.languages.forEach((lang) => {
                const elementId = '#messageContent_' + lang.lang;
                const editorKey = lang.lang;

                // التحقق إذا كان المحرر موجودًا بالفعل
                if (!this.editors[editorKey]) {
                    ClassicEditor.create(document.querySelector(elementId))
                        .then((editor) => {
                            // تخزين المحرر
                            this.editors[editorKey] = editor;
                            editor.setData(this.formData[`description_${editorKey}`] || '');

                            // تحديث formData عند أي تغيير
                            editor.model.document.on('change:data', () => {
                                this.formData[`description_${editorKey}`] = editor.getData();
                            });
                        })
                        .catch((error) => {
                          console.log(error)
                            // console.error('CKEditor initialization error:', error);
                        });
                } else {
                    // إذا كان المحرر موجودًا بالفعل، فقط قم بتحديث البيانات
                    this.editors[editorKey].setData(this.formData[`description_${editorKey}`] || '');
                }
            });
        } else {
            this.swalFunction('error', 'Error happens');
        }
    }).catch((error) => {
        this.swalFunction('error', error);
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
              axios.delete(`Pages/deleteItem/${id}`)
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
      
     
  },

 
};
</script>


<style type="text/css">
  .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }



</style>