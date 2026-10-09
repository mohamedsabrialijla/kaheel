import "../../bootstrap";
import "../../axios";

import { createApp } from "vue";
import Router from "../../routes.js";
import { createStore } from "vuex";
import store from "../../store.js";
// import Select2 from 'vue3-select2-component';
// import { createI18n } from "vue-i18n";
import AppDashboard from "./layout/AppDashboard.vue";
// import en from "../../../locales/en.json";
// import ar from "../../../locales/ar.json";


// import VRuntimeTemplate from 'v-runtime-template';


// const i18n = createI18n({
//     locale: "en", // Default language
//     fallbackLocale: "en",
//     messages: { en, ar },
// });
const dashboardApp = createApp(AppDashboard);

// dashboardApp.component('v-runtime-template', VRuntimeTemplate);


import HeaderDashboard from "./layout/HeaderDashboard.vue";
import FooterDashboard from "./layout/FooterDashboard.vue";

dashboardApp.component("HeaderDashboard", HeaderDashboard);
dashboardApp.component("FooterDashboard", FooterDashboard);

// dashboardApp.use(i18n);
dashboardApp.use(Router);
dashboardApp.use(store);
// app.component('Select2', Select2)

dashboardApp.mount("#dashboard-app");
