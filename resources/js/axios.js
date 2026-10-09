import axios from "axios";
import router from "./routes";
import store from "./store";

function resolveApiOrigin() {
    if (import.meta.env.VITE_API_ORIGIN) {
        return String(import.meta.env.VITE_API_ORIGIN).replace(/\/$/, "");
    }
    if (typeof window !== "undefined" && window.location?.origin) {
        return window.location.origin;
    }
    return "http://127.0.0.1:8000";
}

function buildWebBaseUrl(loc) {
    return `${resolveApiOrigin()}/${loc}/web/`;
}

// إعداد قاعدة URL
axios.defaults.baseURL = buildWebBaseUrl(localStorage.getItem("locale") || "en");

delete axios.defaults.headers.common["X-Requested-With"];

// إعداد رمز التفويض
const token = localStorage.getItem("authToken");
if (token) {
    axios.defaults.headers.common["authorization"] = `Bearer ${token}`;
} else {
    console.warn("Authorization token is missing.");
}
let locale = localStorage.getItem("locale") || "en";


 

const api = axios.create({
    baseURL: buildWebBaseUrl(locale),
    headers: {
        "Accept-Language": locale,
    },
});
 
function setLanguage(newLocale) {
    locale = newLocale;
    localStorage.setItem("locale", newLocale);

    axios.defaults.headers.common["Accept-Language"] = newLocale;
    api.defaults.headers["Accept-Language"] = newLocale;

    const newBaseURL = buildWebBaseUrl(newLocale);
    axios.defaults.baseURL = newBaseURL;
    api.defaults.baseURL = newBaseURL;
}
 
// إعداد اللغة
axios.defaults.headers.common["Accept-Language"] = locale;

// اعتراض استجابات Axios
axios.interceptors.response.use(
    (response) => {
        return response; // تمرير الاستجابة الأصلية
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            // الحصول على المسار الحالي
            const currentPath = router.currentRoute.value.path;

            // التحقق مما إذا كان الرابط يحتوي على "/admin"
            if (currentPath.includes("/dashboard")) {
                const dashLocale = currentPath.split("/")[1] || locale || "ar";
                window.location.assign(`/${dashLocale}/admin/login`);
            } else {
                router.push({ path: `/${locale}/login` });
            }
        }
        return Promise.reject(error); // تمرير الخطأ للمعالجة
    }
);

export { api, setLanguage };
 