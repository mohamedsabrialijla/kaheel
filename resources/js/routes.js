import { createRouter, createWebHistory } from "vue-router";
import { authApi } from "./axiosUser";
import adminRoutes from "./adminRoutes";
// import userRoutes from './userRoutes';
import publicRoutes from "./publicRoutes";

const routes = [
    {
        path: "/:locale",
        component: {
            template: "<router-view />",
        },
        children: [
            ...publicRoutes,
            ...adminRoutes,
            // ...userRoutes,
        ],
    },
    {
        path: "/",
        redirect: "/en",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const locale = to.params.locale || "en";

    // تحقق من اللغة
    if (!["en", "ar"].includes(locale)) {
        return next("/en");
    }

    localStorage.setItem("locale", locale);
    axios.defaults.headers.common["Accept-Language"] = locale;

    // التحقق فقط لمسارات واجهة المستخدم (app)
    const isAppRoute = to.path.includes("/app");

    if (isAppRoute && to.meta.requiresAuth) {
        const token = localStorage.getItem("authToken");
        if (!token) {
            return next({ path: `/${locale}/login` });
        }

        try {
            const response = await authApi({
                method: "get",
                url: "check-auth",
            });

            if (response.status !== 200) {
                localStorage.removeItem("authToken");
                localStorage.removeItem("token");
                return next({ path: `/${locale}/login` });
            }
        } catch (error) {
            localStorage.removeItem("authToken");
            return next({ path: `/${locale}/login` });
        }
    }

    next();
});

export default router;
