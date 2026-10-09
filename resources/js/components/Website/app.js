import "../../bootstrap";
import "../../axiosUser";

import { createApp } from "vue";
import Router from "../../routes.js";
import store from "../../storeUser.js";

import App from "./layout/App.vue";
import UserNameLink from "./common/UserNameLink.vue";
import VerifiedBadge from "./common/VerifiedBadge.vue";

import HeaderWebsite from "./layout/Header.vue";
import FooterWebsite from "./layout/Footer.vue";

const app = createApp(App);

app.component("headerWebsite", HeaderWebsite);
app.component("footerWebsite", FooterWebsite);
app.component("UserNameLink", UserNameLink);
app.component("VerifiedBadge", VerifiedBadge);

const savedLocale = localStorage.getItem("locale") || window.defaultLocale || "en";

store.dispatch("loadTranslations", savedLocale).then(() => {
    // ضبط اتجاه الصفحة
    if (savedLocale === "ar") {
        document.documentElement.setAttribute("dir", "rtl");
        document.documentElement.setAttribute("lang", "ar");
        document.body.classList.add("rtl");
    } else {
        document.documentElement.setAttribute("dir", "ltr");
        document.documentElement.setAttribute("lang", "en");
        document.body.classList.remove("rtl");
    }

    
    app.use(store);

    app.use(Router);

    app.mount("#app");
});
