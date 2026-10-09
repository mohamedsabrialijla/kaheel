<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
      dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>"
      style="direction: <?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>;">    <!--begin::Head-->
    <head>
        <title>FORMA BY AZURE – KAHEEL</title>
        <meta charset="utf-8" />
        <meta name="description" content="FORMA BY AZURE – KAHEEL" />
        <meta name="keywords" content="KAHEEL, FORMA BY AZURE"  />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="ar_SA" />
        <meta property="og:type" content="website"  />
        <meta property="og:title" content="FORMA BY AZURE – KAHEEL"  />
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

        <!-- <link id="main-style" href="<?php echo e(url('assets/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" /> -->


        <link href="<?php echo e(url('assets/css/styleCustom.css')); ?>" rel="stylesheet" type="text/css" />

        <script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>

        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/components/Website/app.js']); ?>

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

        <div id="app">

        </div> <!--end::app id for vue js-->


        <!--begin::Javascript-->

        <script>var hostUrl = "assets/";
            window.defaultLocale = "<?php echo e(app()->getLocale()); ?>";
        </script>

        <!--begin::Global Javascript Bundle(mandatory for all pages)-->
        <script src="<?php echo e(url('assets/plugins/global/plugins.bundle.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/scripts.bundle.js')); ?>"></script>
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
        <script src="<?php echo e(url('assets/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>
        <!--end::Vendors Javascript-->
        <!--begin::Custom Javascript(used for this page only)-->
        <script src="<?php echo e(url('assets/js/widgets.bundle.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/widgets.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/apps/chat/chat.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/utilities/modals/create-app.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/utilities/modals/create-campaign.js')); ?>"></script>
        <script src="<?php echo e(url('assets/js/custom/utilities/modals/users-search.js')); ?>"></script>
        <!--end::Custom Javascript-->
        <!--end::Javascript-->
    </body>
    <!--end::Body-->
</html>
<?php /**PATH E:\kaheel\resources\views/basic/landing.blade.php ENDPATH**/ ?>