import { createStore } from 'vuex';
import axios from 'axios'; // تأكد من استيراد axios هنا

const store = createStore({
    state: {
        setting: null,
        user: null,
        languages: [],
        projectItem: JSON.parse(localStorage.getItem('projectItem')) || null, // استرداد البيانات من localStorage
        currentLanguage: 'en',
        translations: {},
        openChatWithUser: null, // { id, name, avatar } - لفتح محادثة مع مستخدم معين
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },

        setSetting(state, setting) {
            state.setting = setting;
        },


        setLanguages(state, languages) {

            state.languages = languages;

        },

        setprojectItem(state, projectItem) {
            state.projectItem = projectItem;
            localStorage.setItem('projectItem', JSON.stringify(projectItem)); // حفظ في localStorage
        },

          setCurrentLanguage(state, language) {
            state.currentLanguage = language;
         },

         setTranslations(state, translations) {
            state.translations = translations; // تعيين الترجمات
        },
        
        setOpenChatWithUser(state, userData) {
            state.openChatWithUser = userData; // { id, name, avatar } أو null
        },
    },
    actions: {
        // login({ commit }, user) {
        //     commit('setUser', user);
        // },
        // logout({ commit }) {
        //     commit('setUser', null);
        // },
        fetchLanguages({ commit }) {
            return axios.get('Languages/getAllItems') // تأكد من أن الرابط صحيح
                .then(response => {
                    commit('setLanguages', response.data.items);
                })
                .catch(error => {
                    console.log("Error fetching languages:", error);
                });
        },

         async loadTranslations({ commit, state }, locale = null) {

            const selectedLocale = locale ;

            let storedTranslations = localStorage.getItem(`translations_${selectedLocale}`);

            if (storedTranslations) {
                commit('setTranslations', JSON.parse(storedTranslations));
                return;
            }

            try {
                const response = await api.get("Languages/translations", {
                    params: { locale: selectedLocale }
                });

                const data = response.data;
                localStorage.setItem(`translations_${selectedLocale}`, JSON.stringify(data));
                commit('setTranslations', data);
            } catch (error) {
                console.log("❌ Error loading translations:", error);
            }
        },


        async changeLanguage({ dispatch, commit }, locale) {
            commit('setCurrentLanguage', locale);
            await dispatch('loadTranslations', locale); // تحميل الترجمات
            localStorage.setItem('locale', locale); // حفظ اللغة في localStorage
        },

        fetchUser({ commit }) {
            return axios.get('AdminAuth/getAuth') 
                .then(response => {
                    const userData = response.data.items;
                    // إضافة userType للتمييز بين Admin و User
                    userData.userType = 'admin';
                    commit('setUser', userData);
                    // حفظ في localStorage للاستخدام في الشات
                    localStorage.setItem('user', JSON.stringify(userData));
                    console.log('✅ Admin user data saved to localStorage with userType: admin');
                })
                .catch(error => {
                    console.log("Error fetching User auth:", error);
                });
        },

         fetchSetting({ commit }) {

            return axios.get('Settings/getById')  
                .then(response => {
                    commit('setSetting', response.data.items);
                 
                })
                .catch(error => {
                    console.log("Error fetching User auth:", error);
                });
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
        getCurrentLanguage(state) {
            return state.currentLanguage;
        },

        getprojectItem(state) {
            return state.projectItem;
        },
    },
});

export default store;
