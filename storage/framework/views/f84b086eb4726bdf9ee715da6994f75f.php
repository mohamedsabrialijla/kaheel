<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
      dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>"
      style="direction: <?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>;">

    <!--begin::Head-->
    <head>
        <title>لوحة مؤشرات الإدارة | FORMA BY AZURE</title>
        <meta charset="utf-8" />
        <meta name="description" content="The most advanced Tailwind CSS & Bootstrap 5 Admin Theme with 40 unique prebuilt layouts on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel versions. Grab your copy now and get life-time updates for free." />
        <meta name="keywords" content="tailwind, tailwindcss, metronic, bootstrap, bootstrap 5, angular, VueJs, React, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel starter kits, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes" />
        <meta property="og:url" content="https://keenthemes.com/metronic" />
        <meta property="og:site_name" content="Metronic by Keenthemes" />
        <link rel="canonical" href="http://preview.keenthemes.comindex.html" />
        <link rel="shortcut icon" href="<?php echo e(url('assets/media/logos/fav.png')); ?>" />
        <!--begin::Fonts(mandatory for all pages)-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Vendor Stylesheets(used for this page only)-->
        <link href="<?php echo e(url('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(url('assets/plugins/custom/datatables/datatables.bundle.css')); ?>" rel="stylesheet" type="text/css" />
        <!--end::Vendor Stylesheets-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="<?php echo e(url('assets/plugins/global/plugins.bundle.css')); ?>" rel="stylesheet" type="text/css" />

        <?php if(app()->getLocale() === 'ar'): ?>
            <link id="main-style" href="<?php echo e(url('assets/css/style.bundle.rtl.css')); ?>" rel="stylesheet" type="text/css" />
        <?php else: ?>
            <link id="main-style" href="<?php echo e(url('assets/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" />

        <?php endif; ?>
        <link href="<?php echo e(url('assets/css/styleCustom.css')); ?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Instrument+Serif&family=Work+Sans:wght@400;500;600;700&display=swap">
        <link href="<?php echo e(asset('kaheel/admin.css')); ?>?v=21" rel="stylesheet" type="text/css" />

        <!--end::Global Stylesheets Bundle-->
        <script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>


            <?php echo app('Illuminate\Foundation\Vite')(['resources/js/components/Dashboard/appDashboard.js']); ?>
        <style type="text/css">
            .indicator-progress {
              display: none;
            }
            [data-kt-indicator=on] > .indicator-progress {
              display: inline-block;
            }
            .overlay-wrapper i{
              cursor: pointer;
            }

            .ki-trash-square{
                color: #e31616c2;
            }
            .ki-notepad-edit{
              color: #5375d5;
            }

        </style>

    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed">
        <!--begin::Theme mode setup on page load-->
        <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }
        </script>


        <!--end::Theme mode setup on page load-->
        <!--begin::Main-->
        <!--begin::Root-->

        <div id="dashboard-app" >

        </div> <!--end::app id for vue js-->


        <!--begin::Javascript-->
        <script>var hostUrl = "assets/";</script>
        <!--begin::Global Javascript Bundle(mandatory for all pages)-->
        <script src="<?php echo e(url('assets/plugins/global/plugins.bundle.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/scripts.bundle.js')); ?>"></script>
        <script src="<?php echo e(url('assets/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>

        <!--end::Global Javascript Bundle-->
        <!--begin::Vendors Javascript(used for this page only)-->
        <script src="<?php echo e(url('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')); ?>"></script>
        <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
        <!--end::Vendors Javascript-->

<!--         <script src="<?php echo e(url('assets/js/custom/apps/user-management/users/list/table.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/apps/user-management/users/list/export-users.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/apps/user-management/users/list/add.js')); ?>"></script>
 -->
        <!--begin::Custom Javascript(used for this page only)-->
        <script src="<?php echo e(url('assets/js/widgets.bundle.js')); ?>"></script>
        <!-- <script src="<?php echo e(url('assets/js/custom/widgets.js')); ?>"></script> -->
        <script src="<?php echo e(url('assets/js/custom/apps/chat/chat.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/utilities/modals/create-app.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/utilities/modals/create-campaign.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/utilities/modals/users-search.js')); ?>"></script>
        <script src="<?php echo e(url('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js')); ?>"></script>

        <!--end::Custom Javascript-->
        <!--end::Javascript-->
        </body>
    <!--end::Body-->
</html>
<?php /**PATH E:\kaheel\resources\views/basic/dashboard.blade.php ENDPATH**/ ?>