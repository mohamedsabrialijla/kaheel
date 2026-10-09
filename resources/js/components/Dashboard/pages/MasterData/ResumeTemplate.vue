<template>
   
  <!--begin::Post-->
  <div class="content flex-column-fluid" id="kt_content">
     <div v-html="`<style>${formData.content_css}</style>`"></div>

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
              <th class="min-w-125px">Category</th>
              <th class="min-w-125px">Name</th>
              <th class="min-w-125px">Status</th>
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
                  <a href="#" class="text-gray-800 text-hover-primary mb-1">{{item.category.name}}</a>
                </div>
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
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="950px">


             <!--  <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Deaprtment</label>
                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select an Department" v-model="formData.department_id">
                  <option>select</option>
                  <option v-for="itemDepartment in itemsDepartments" :value="itemDepartment.id" >{{itemDepartment.name}}</option>
                  
                </select>              
              </div>  -->


              <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Category</label>
                <multiselect class="" v-model="formData.category" tag-placeholder="Select " placeholder="Search ..." label="name" track-by="id" :options="itemsCategories" :multiple="false" :taggable="false" :options-limit="10" :allow-empty="false" ></multiselect>

              </div>


              <div class="fv-row mb-7">

              <label class="form-label">Tags English</label>
                          <input
                  class="form-control"
                  ref="tagsInput_en"  
                  id="kt_tagify_1"
                />
            </div>



             <div class="fv-row mb-7">

              <label class="form-label">Tags Arabic</label>
                          <input
                  class="form-control"
                  ref="tagsInput_ar"  
                  id="kt_tagify_2"
                />
            </div>
 

          
              <div class="fv-row mb-7" v-for="lang in languages" :key="lang.locale">
                <label class="required fw-semibold fs-6 mb-2" :for="'name_' + lang.locale">{{ lang.name }} Name</label>
                <input type="text" :name="'name_' + lang.locale" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Name" value="" v-model="formData['name_' + lang.lang]"   required />
              </div>

            

              <div class="fv-row mb-7" v-for="lang in languages" :key="lang.locale">
                <label class="required fw-semibold fs-6 mb-2">{{ lang.name }} Description</label>
                <textarea rows="4" id="messageContent" maxlength="100"
                   v-model="formData['description_' + lang.lang]" value=""
                    placeholdr="{{ lang.name }} Description" class="form-control " >     
                </textarea>
              </div>




              <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Template Resume</label>
                <textarea rows="10" name="kt_docs_ckeditor_classic" 
                   v-model="formData.content_html" value=""
                    placeholdr="Template Resume" class="form-control kt_docs_ckeditor_classic_old" >     
                </textarea>

              </div>

              

              <component :is="dynamicComponent"></component>




              <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">CSS Data</label>
                <textarea rows="6"  
                   v-model="formData.content_css" value=""
                    placeholdr="Template Resume CSS" class="form-control" >     
                </textarea>
              </div>



              <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">selected Section</label>
                <textarea rows="6"  
                   v-model="formData.selectedSections" value=""
                    placeholdr="selected Sections" class="form-control" >     
                </textarea>
              </div>





              <div class="fv-row mb-7">
                <!--begin::Label-->
                <label class="d-block fw-semibold fs-6 mb-5">Thumbnail</label>
              
                <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                  <!--begin::Preview existing avatar-->

                  <div class="image-input-wrapper w-125px h-125px" :style="{'background-image': formData.logo ? `url(${formData.logo})` : 'url(/assets/avatar.svg)'}"></div>


                
                  <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                    <i class="ki-duotone ki-pencil fs-7">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                    <input type="file" name="avatar" @change="onFileChange" accept=".png, .jpg, .jpeg, .jfif, .web" />
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
                <div class="form-text">Allowed file types: png, jpg, jpeg , web , .jfif .</div>
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
            itemsCategories: [], 
            itemsTags_en: [], 
            itemsTags_ar: [], 
            ItemsOverall:[],
            searchQuery: '',
            formData: {
                category:'',
                tags_en: [],
                tags_ar: [],
                content_html:'' ,
                content_css:'' ,
                logo: '',
                logo_preview  :'./../assets/media/avatars/300-1.jpg',
            },

            tagsInput_en: "",
            tagsInput_ar: "",

            
            ItemID: null,
            URL:'ResumeTemplate/createItem',


            dynamicComponent: null,



         selectedSections: [
              { key: "personalInfo", column: "right" },
              { key: "summary", column: "right" },
              { key: "education", column: "left" },
              { key: "employmentHistory", column: "left" },
              { key: "skillsList", column: "left" },
              { key: "languagesList", column: "left" },
              { key: "socialLinks", column: "right" },
              { key: "additionalSections", column: "right" },
            ],

            sectionTitles: {
                personalInfo: "Personal Information",
                summary: "Summary",
                employmentHistory: "Employment History",
                education: "Education",
                skillsList: "Skills",
                languagesList: "Languages",
                socialLinks: "social and Links",
                color: "Theme Customization",
                additionalSections: "additional Sections",
            },

            levelWidths: {
              Novice: '20',
              Beginner: '40',
              Skillful: '60',
              Experienced: '80',
              Expert: '100',
            },






            personalInfo: {
                first_name: "John",
                last_name: "MADA",
                job_title: "Software Developer",
                email: "john.doe@example.com",
                mobile: "+123456789",
                address: "123 Main St",
                city: "New York",
                country: "USA",
                postal_code: "10001",
                driving_license: "B",
                nationality: "American",
                place_of_birth: "New York",
                date_of_birth: "1990-01-01",
                logo: "http://127.0.0.1:8000/assets/CVTemplateHtml/img/khaled.jpg",
                previewUrl:
                    "http://127.0.0.1:8000/assets/CVTemplateHtml/img/khaled.jpg",
                summary: "Experienced software developer...",
                employmentHistory: [
                    {
                        job_title: "Frontend Developer",
                        company_name: "Tech Company",
                        duration: "2021-2025",
                        job_description:
                            "Developed and maintained user interfaces.",
                    },

                    {
                        job_title: "Frontend Developer 2",
                        company_name: "Tech Company 2",
                        duration: "2021-2025",
                        job_description:
                            "Developed and maintained user interfaces 2.",
                    },
                ],
                education: [
                    {
                        id: 1,
                        degree: "Bachelor's in Computer Science",
                        school: "University of California",
                        start_date: "2014-09-01",
                        end_date: "2018-06-01",
                        field: "Computer engineering",
                        descriptions:
                            "Studied computer science and software development.",
                    },

                    {
                        id: 1,
                        degree: "Bachelor's in Computer Science2",
                        school: "University of California2",
                        start_date: "2014-09-01",
                        end_date: "2018-06-01",
                        field: "Computer Engineering2",
                        descriptions:
                            "Studied computer science and software development.",
                    },
                ],
            
                skillsList: [{ name: "", level: "" , percentage:""}],
                languagesList: [{ name: "", level: "", percentage:"" }],
                socialLinks: [{ platform: "LinkedIn", url: "https://www.linkedin.com" }],
                additionalSections: [
                    {
                        field: "",
                        start_date: "",
                        end_date: "",
                        description: "",
                    },
                ],

                theme: {
                    // mainBgColor: "#f5deb3",   // page 
                    sidebarBgColor: "", // siderbar 
                    // textColor: "#333333",      // text
                    titleColor: "",      // title
                    barColor: "",        // as skills , languages
                    linkColor: "",        // as skills , languages
                    fontFamily: ""       
                  },
            },




    





        };
    },


    mounted() {
       
        this.fetchItems(this.currentPage);

        

    },



   computed: {
      

    },



  


   
    methods: {

     

     compileTemplate() {

        this.dynamicComponent = {
          template: this.formData.content_html,
          data: () => ({
            personalInfo: this.personalInfo,      
            sectionTitles: this.sectionTitles,
            selectedSections: this.selectedSections,        
          }),
        };

      },


       onFileChange(e) {
        this.logo = e.target.files[0];
            const file = e.target.files[0];
            if (file) {
                this.formData.logo = URL.createObjectURL(file);
            }
        },



    
        getModalCreate(){



        var input1 = document.querySelector("#kt_tagify_1");
        var input2 = document.querySelector("#kt_tagify_2");

        // Initialize Tagify components on the above inputs
        new Tagify(input1);
        new Tagify(input2);


          
          this.URL = 'ResumeTemplate/createItem'
          this.resetItem();
          $('#kt_modal_add_item').modal('show');
          this.titleModal = 'اضافة جديد '
          this.fetchItemsCategories()
          // ClassicEditor.create(document.querySelector('.kt_docs_ckeditor_classic'));

        },

 
        getModalEdit(item){
            var input1 = document.querySelector("#kt_tagify_1");
            var input2 = document.querySelector("#kt_tagify_2");

        // Initialize Tagify components on the above inputs
        new Tagify(input1);
        new Tagify(input2);

            this.ItemID = item.id
            this.resetItem();
            $('#kt_modal_add_item').modal('show');
            this.titleModal = 'تعديل البيانات'
            this.getItemById() 
            this.URL = 'ResumeTemplate/editItem'
            this.fetchItemsCategories()
            // ClassicEditor.create(document.querySelector('.kt_docs_ckeditor_classic'));

        },


      

        closeModal(){
            $('#kt_modal_add_item').modal('hide');
            this.URL = 'Sections/createItem'
            this.ItemID = null
            // this.resetMark();
        },


      

        resetItem(){
          this.itemsTags_ar= [];
          this.itemsTags_en= [];
          this.formData =  {
                content_html:'',
                content_css: '',
                logo:'',
                logo_preview :'./../assets/companies/img/store-logo.jpg'
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
            await axios.get('ResumeTemplate/getAllItems', {
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

       

        async fetchItemsCategories() {
            const typeList = ['category'];
            axios.get('/ItemsCategories/getAllItems?pagination=0', {
                params: {
                    pagination: 0,
                    // type: this.routeSegment
                }
            })
            .then(response => {
              this.ItemsOverall = response.data.items;
                this.itemsCategories = this.ItemsOverall.filter(item => item.type && item.type.name === "category_template");
                // this.itemsTags = this.ItemsOverall.filter(item => item.type && item.type.name === "tag");
            })
            .catch(error => {
                this.swalFunction('error','Error happens')
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

            if(this.formData.category){
              form.append('category_id', this.formData.category.id);

            }



              form.append('content_html', this.formData.content_html);
              form.append('content_css', this.formData.content_css);
              form.append('selectedSections', this.formData.selectedSections);

              form.append('name_ar', this.formData.name_ar);
              form.append('name_en', this.formData.name_en);
              form.append('description_ar', this.formData.description_ar);
              form.append('description_en', this.formData.description_en);
              form.append('tags_en',  this.$refs.tagsInput_en.value);
              form.append('tags_ar',  this.$refs.tagsInput_ar.value);
             

              if (this.logo) {
                form.append('logo', this.logo);
              }
           
            if(this.ItemID != ''){
               form.append('Item_id', this.ItemID);
            }

            // console.log(form)

            axios.post(this.URL,form,config).then((response)=>{
                 this.isLoading = false;
                if(response.data.items){
                    this.swalFunction('success',response.data.message)
                    this.closeModal()
                    this.fetchItems()           

                }else{
                   this.isLoading = false;
                    this.swalFunction('error','Errors happens')
                }             
            
          }).catch(error => {
             this.isLoading = false;
             this.swalFunction('error','Errors happens')
          
        });

       },


        

        async getItemById(){

            axios.get('ResumeTemplate/getById', { params: { ID: this.ItemID } }).then(response => {
                if(response.data){
                  let data = response.data.items
                  let translations = response.data.items.translations
                  let raw = response.data.items.test ;

                  if (typeof raw === 'string') {
                      this.selectedSections = JSON.parse(raw);
                    } else {
                      this.selectedSections = raw; // احتمال يكون JSON فعلاً
                    }



                  this.formData = data;
                   translations.forEach((element) => { 
                              this.formData['name_'+element.locale] = element.name; 
                              this.formData['description_'+element.locale] = element.description; 
                        });

                   this.tagsInput_en = data.tags_en
                   this.tagsInput_ar = data.tags_ar
                   this.$refs.tagsInput_en.value = data.tags_en
                   this.$refs.tagsInput_ar.value = data.tags_ar


                   // this.selectedSections = response.data.items.test;


                           this.compileTemplate();




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
              axios.delete(`ResumeTemplate/deleteItem/${id}`)
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




   watch: {

  'personalInfo.skillsList': {
    handler(newList) {
      const levelMap = {
        Novice: '20%',
        Beginner: '40%',
        Skillful: '60%',
        Experienced: '80%',
        Expert: '100%',
      };

      newList.forEach(skill => {
        skill.percentage = levelMap[skill.level] || '0%';
      });
    },
    deep: true, // لمراقبة التغيير داخل العناصر
    immediate: true, // لتطبيقه أول مرة مباشرة بعد mounted
  },

   'personalInfo.languagesList': {
    handler(newList) {
      const levelMap = {
        Novice: '20%',
        Beginner: '40%',
        Skillful: '60%',
        Experienced: '80%',
        Expert: '100%',
      };

      newList.forEach(skill => {
        skill.percentage = levelMap[skill.level] || '0%';
      });
    },
    deep: true, // لمراقبة التغيير داخل العناصر
    immediate: true, // لتطبيقه أول مرة مباشرة بعد mounted
  }


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

