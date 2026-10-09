import axios from "axios";
import router from "./routes";
import store from "./storeUser";

let locale = localStorage.getItem("locale") || "en";
delete axios.defaults.headers.common["X-Requested-With"];

function resolveApiOrigin() {
    if (import.meta.env.VITE_API_ORIGIN) {
        return String(import.meta.env.VITE_API_ORIGIN).replace(/\/$/, "");
    }
    if (typeof window !== "undefined" && window.location?.origin) {
        return window.location.origin;
    }
    return "http://127.0.0.1:8000";
}

function buildUserBaseUrl(loc) {
    return `${resolveApiOrigin()}/${loc}/user/`;
}

axios.defaults.baseURL = buildUserBaseUrl(locale);

axios.defaults.headers.common["Accept-Language"] = locale;



const token = localStorage.getItem("authToken") || localStorage.getItem("token");
if (token && token !== "null" && token !== "undefined") {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
} 




const api = axios.create({
    baseURL: buildUserBaseUrl(locale),
    headers: {
        "Accept-Language": locale,
    },

}); 

function setLanguage(newLocale) {
    locale = newLocale;
    localStorage.setItem("locale", newLocale);

    axios.defaults.headers.common["Accept-Language"] = newLocale;
    api.defaults.headers["Accept-Language"] = newLocale;

    const newBaseURL = buildUserBaseUrl(newLocale);

    axios.defaults.baseURL = newBaseURL;
    api.defaults.baseURL = newBaseURL;
}

function syncAuthToken() {
    const token = localStorage.getItem("authToken") || localStorage.getItem("token");
    const valid = token && token !== "null" && token !== "undefined" && token !== "";

    if (valid) {
        const bearer = `Bearer ${token}`;
        api.defaults.headers.common.Authorization = bearer;
        axios.defaults.headers.common.Authorization = bearer;
    } else {
        delete api.defaults.headers.common.Authorization;
        delete axios.defaults.headers.common.Authorization;
    }

    return valid ? token : null;
}

const authApi = (config = {}) => {
    const token = syncAuthToken();
    config.headers = {
        ...config.headers,
        "Accept-Language": locale,
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
    };
    return api(config);
};

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem("authToken");
            localStorage.removeItem("token");
            syncAuthToken();
            const currentLocale = localStorage.getItem("locale") || locale || "en";
            if (!window.location.pathname.includes("/login")) {
                router.push({ path: `/${currentLocale}/login` });
            }
        }
        return Promise.reject(error);
    }
);

export { api, authApi, setLanguage, syncAuthToken };