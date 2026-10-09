import Landing from "@/components/Website/pages/Landing.vue";
import Login from "@/components/Website/auth/Login.vue";
import Forget from "@/components/Website/auth/Forget.vue";
import Verfied from "@/components/Website/auth/Verfied.vue";
import Register from "@/components/Website/auth/Register.vue";
import AuthLayout from "@/components/Website/auth/AuthLayout.vue";
import LoginAdmin from "@/components/Website/adminAuth/Login.vue";
import AboutUs from "@/components/Website/pages/AboutUs.vue";
import FAQ from "@/components/Website/pages/FAQ.vue";
import Contact from "@/components/Website/pages/Contact.vue";
import Terms from "@/components/Website/pages/Terms.vue";
import CategoryProducts from "@/components/Website/pages/CategoryProducts.vue";
import MyPage from "@/components/Website/pages/MyPage.vue";
import SellerPage from "@/components/Website/pages/SellerPage.vue";
import PayCommission from "@/components/Website/pages/PayCommission.vue";
import PaymentSuccess from "@/components/Website/pages/PaymentSuccess.vue";
import PaymentFailed from "@/components/Website/pages/PaymentFailed.vue";
import AddAd from "@/components/Website/pages/AddAd.vue";
import AdDetails from "@/components/Website/pages/AdDetails.vue";
import OtpVerify from "@/components/Website/auth/OtpVerify.vue";

import userDashboard from "@/components/Website/dashboard/Dashboard.vue";
import userCV from "@/components/Website/dashboard/userCV.vue";
import Create from "@/components/Website/dashboard/Create.vue";

import InvoicePdf from "@/components/Website/pages/InvoicePdf.vue";



export default [
    {
        path: "/:locale/",
        name: "home",
        component: Landing,
        meta: { isHomePage: true },
    },
    {
        path: "/:locale/login",
        component: AuthLayout,
        children: [
            { path: "", component: Login, meta: { hideHeaderFooter: true } },
        ],
    },

    {
        path: "/:locale/forget-password",
        component: AuthLayout,
        children: [
            { path: "", component: Forget, meta: { hideHeaderFooter: true } },
        ],
    },

    {
        path: "/:locale/verfied-message",
        component: AuthLayout,
        children: [
            { path: "", component: Verfied, meta: { hideHeaderFooter: true } },
        ],
    },

    {
        path: "/:locale/signUp",
        component: AuthLayout,
        children: [
            { path: "", component: Register, meta: { hideHeaderFooter: true } },
        ],
    },

    {
        path: "/:locale/otp-verify",
        component: AuthLayout,
        children: [
            {
                path: "",
                component: OtpVerify,
                meta: { hideHeaderFooter: true },
            },
        ],
    },

    {
        path: "/:locale/admin/login",
        component: AuthLayout,
        name: "AdminLogin",
        children: [
            {
                path: "",
                component: LoginAdmin,
                meta: { hideHeaderFooter: true },
            },
        ],
    },

    { path: "/:locale/about-us", component: AboutUs, meta: { isMenu: false } },
    { path: "/:locale/faq", component: FAQ, meta: { isMenu: false } },
    {
        path: "/:locale/contact-us",
        component: Contact,
        meta: { isMenu: false },
    },
    { path: "/:locale/terms", component: Terms, meta: { isMenu: false } },

    {
        path: "/:locale/app/info",
        component: userDashboard,
        meta: { requiresAuth: true, isMenu: true },
    },

    {
        path: "/:locale/app/templates",
        component: userCV,
        meta: { requiresAuth: true, isMenu: true },
    },

    {
        path: "/:locale/app/create-template",
        component: Create,
        name: "CreateTemplate",
        meta: { requiresAuth: true, isMenu: true },
    },

    {
        path: "/:locale/category/:id",
        name: "category-products",
        component: CategoryProducts,
    },

    {
        path: "/:locale/my-page",
        component: MyPage,
        meta: { isMenu: false },
    },


    {
        path: "/:locale/seller-page",
        component: SellerPage,
        meta: { isMenu: false },
    },

    {
        path: "/:locale/pay-commission",
        component: PayCommission,
        meta: { isMenu: false },
    },

    {
        path: "/:locale/add-ad",
        component: AddAd,
        meta: { isMenu: false },
    },

    {
        path: "/:locale/ad-details/:id",
        component: AdDetails,
        meta: { isMenu: false },
    },


    {
      path: '/:locale/payment/success',
      component: PaymentSuccess
    },
    {
      path: '/:locale/payment/failed',
      component: PaymentFailed
    },
 
    {
        path: "/:locale/invoice/:track_id",
        component: InvoicePdf,
        meta: { isMenu: false, hideHeaderFooter: true},
    },




];
