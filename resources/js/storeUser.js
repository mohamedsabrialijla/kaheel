import { createStore } from "vuex";
import { api, authApi, setLanguage, syncAuthToken } from "./axiosUser";
import router from "./routes";
import { isIdentityVerified } from "./utils/userVerification";

function readStoredUser() {
    try {
        const raw = localStorage.getItem("user");
        if (!raw || raw === "null" || raw === "undefined") {
            return null;
        }
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === "object" ? parsed : null;
    } catch {
        return null;
    }
}

const store = createStore({
    state: {
        setting: null,
        // Hydrate from localStorage so nafath_verified survives refresh before fetchUser finishes
        user: readStoredUser(),
        languages: [],
        currentLanguage: "en",
        translations: {},

        adCurrent: null,
        loginRequiredVisible: false,
    },
    mutations: {
        setAdCurrent(state, ad) {
            state.adCurrent = ad;
        },

        showLoginRequired(state) {
            state.loginRequiredVisible = true;
        },

        hideLoginRequired(state) {
            state.loginRequiredVisible = false;
        },

        setUser(state, user) {
            state.user = user ? { ...user } : null;
        },

        patchUser(state, patch) {
            if (!state.user || !patch || typeof patch !== "object") {
                return;
            }
            state.user = { ...state.user, ...patch };
            try {
                localStorage.setItem("user", JSON.stringify(state.user));
            } catch {
                /* ignore */
            }
        },

        setSetting(state, setting) {
            state.setting = setting;
        },

        setLanguages(state, languages) {
            state.languages = languages;
        },

        setCurrentLanguage(state, language) {
            state.currentLanguage = language;
        },

        setTranslations(state, translations) {
            state.translations = translations;
        },
    },
    actions: {
        fetchLanguages({ commit }) {
            return api
                .get("Languages/getAllItems")
                .then((response) => {
                    commit("setLanguages", response.data.items);
                })
                .catch((error) => {
                    console.log("❌ Error fetching languages:", error);
                });
        },

        fetchSetting({ commit }) {
            return api
                .get("basic/getSetting")
                .then((response) => {
                    commit("setSetting", response.data.items);
                })
                .catch((error) => {
                    console.log("❌ Error fetching settings:", error);
                });
        },

        fetchUser({ commit, state }, options = {}) {
            const redirectOnFail = options.redirectOnFail !== false;
            const token =
                localStorage.getItem("token") || localStorage.getItem("authToken");

            if (!token || token === "null" || token === "undefined") {
                return Promise.resolve();
            }

            const loadGetAuth = () =>
                authApi({
                    method: "get",
                    url: "UserAuth/getAuth",
                }).then((response) => {
                    const userData = response.data.items;
                    if (!userData) {
                        return;
                    }
                    userData.userType = "user";
                    commit("setUser", userData);
                    localStorage.setItem("user", JSON.stringify(userData));
                });

            // check-auth is optional; if it fails (non-401), still load getAuth
            return authApi({
                method: "get",
                url: "check-auth",
            })
                .catch((error) => {
                    const status = error?.response?.status;
                    if (status === 401) {
                        throw error;
                    }
                    console.warn(
                        "check-auth failed, continuing with getAuth:",
                        status
                    );
                    return null;
                })
                .then(() => loadGetAuth())
                .catch((error) => {
                    const status = error?.response?.status;
                    if (status !== 401) {
                        console.warn(
                            "fetchUser failed (session kept):",
                            status,
                            error?.response?.data
                        );
                        // Keep hydrated localStorage user so verification state is not lost
                        if (!state.user) {
                            const cached = readStoredUser();
                            if (cached) {
                                commit("setUser", cached);
                            }
                        }
                        return;
                    }
                    localStorage.removeItem("token");
                    localStorage.removeItem("authToken");
                    localStorage.removeItem("user");
                    commit("setUser", null);
                    if (
                        redirectOnFail &&
                        !window.location.pathname.includes("/login")
                    ) {
                        const currentLocale =
                            localStorage.getItem("locale") || "en";
                        router.push({ path: `/${currentLocale}/login` });
                    }
                });
        },

        markNafathVerified({ commit, state }) {
            const base = state.user || readStoredUser();
            if (!base) {
                commit("patchUser", { nafath_verified: true });
                return;
            }
            commit("setUser", { ...base, nafath_verified: true });
            try {
                localStorage.setItem(
                    "user",
                    JSON.stringify({ ...base, nafath_verified: true })
                );
            } catch {
                /* ignore */
            }
        },

        async loadTranslations({ commit, state }, locale = null) {
            const selectedLocale = locale;

            let storedTranslations = localStorage.getItem(
                `translations_${selectedLocale}`
            );

            await setLanguage(locale);

            try {
                const response = await api.get("Languages/translations", {
                    params: { locale: selectedLocale },
                });

                const data = response.data;
                localStorage.setItem(
                    `translations_${selectedLocale}`,
                    JSON.stringify(data)
                );
                commit("setTranslations", data);
            } catch (error) {
                console.log("❌ Error loading translations:", error);
            }
        },

        async changeLanguage({ dispatch, commit }, locale) {
            commit("setCurrentLanguage", locale);
            await dispatch("loadTranslations", locale);
            localStorage.setItem("locale", locale);
        },
    },
    getters: {
        getLanguages(state) {
            return state.languages;
        },
        getUser(state) {
            return state.user;
        },
        getSetting(state) {
            return state.setting;
        },
        isAuthenticated(state) {
            return !!state.user;
        },
        isNafathVerified(state) {
            return isIdentityVerified(state.user);
        },
        getCurrentLanguage(state) {
            return state.currentLanguage;
        },
        getTranslations(state) {
            return state.translations;
        },
    },
});

export default store;
