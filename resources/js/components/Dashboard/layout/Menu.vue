<template>
   
        <div id="kt_aside" class="aside card" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_toggle">
            <!--begin::Aside menu-->
            <div class="aside-menu flex-column-fluid px-4">
                <!--begin::Aside Menu-->
                <div class="hover-scroll-overlay-y mh-100 my-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="{default: '#kt_aside_footer', lg: '#kt_header, #kt_aside_footer'}" data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu" data-kt-scroll-offset="{default: '5px', lg: '75px'}">
                    <!--begin::Menu-->
                    <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_aside_menu" data-kt-menu="true">
                        <!--begin:Menu item-->

                        
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item pt-5">
                            <!--begin:Menu content-->
                            <div class="menu-content">
                                <span class="menu-heading fw-bold fs-6 " style="text-align: center;" >Dashboard </span>
                            </div>
                            <!--end:Menu content-->
                        </div>
                    



                        <div class="menu-item pt-5">




                            <div class="menu-content">

                                <router-link :to="`/${locale}/dashboard/user_management/admins`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-element-8">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Admins </router-link>
                            </div>




                            <div class="menu-content">

                                <router-link :to="`/${locale}/dashboard/user_management/roles`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-element-8">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Roles </router-link>
                            </div>




                            <div class="menu-content">

                                <router-link :to="`/${locale}/dashboard/user_management/permissions`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-element-8">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Permissions </router-link>
                            </div>






                            <div class="menu-content">

                                <router-link :to="`/${locale}/dashboard/master_data/category/country`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-element-8">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Countries </router-link>
                            </div>


                            <div class="menu-content">

                                <router-link :to="`/${locale}/dashboard/master_data/category/city`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-element-8">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Cities </router-link>
                            </div>


                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/master_data/category/category`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-abstract-28">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Categories </router-link>
                            </div>






                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/master_data/category/brand`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-abstract-28">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Brands </router-link>
                            </div>


                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/master_data/category/banner`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-abstract-28">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Banner </router-link>
                            </div>
                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/master_data/category/payments`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-abstract-28">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                    </i>
                                Payments </router-link>
                            </div>



                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/master_data/category/report`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                List Reasons </router-link>
                            </div>

                           

                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/user_management/users`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-profile-user">
                                         <span class="path1"></span>
                                         <span class="path2"></span>
                                         <span class="path3"></span>
                                         <span class="path4"></span>
                                        </i>
                                Users </router-link>
                            </div>




                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/hr/employees`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Employers </router-link>
                            </div>



                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/ads/ad`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Ads </router-link>
                            </div>


                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/setting/contact`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Contacts Us </router-link>
                            </div>



                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/setting/report`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Reports </router-link>
                            </div>


                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/setting/notifications`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Notifications </router-link>
                            </div>




                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/orders/order`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Orders </router-link>
                            </div>



                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/setting/pages`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Pages List </router-link>
                            </div>




                            <div class="menu-content">
                                <router-link :to="`/${locale}/dashboard/setting/edit`" class="menu-heading fw-bold fs-6 " style="text-align: center;" >
                                    <i class="ki-duotone ki-user-tick">
                                     <span class="path1"></span>
                                     <span class="path2"></span>
                                     <span class="path3"></span>
                                    </i>
                                Website Settings </router-link>
                            </div>









                    









                        </div>

























                    







                      
              












                    <!--end::Menu-->
                    </div>


                </div>
               
            </div>

        </div>    


</template>

<script>
    export default {
       name : 'Menu',

       computed: {
    locale() {
        return this.$route.params.locale;
    }
}
    }
</script>
