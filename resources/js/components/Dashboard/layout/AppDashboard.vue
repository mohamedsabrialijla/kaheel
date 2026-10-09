<template>
    <div class="ka-shell">
        <aside class="ka-aside">
            <a class="ka-brand" :href="'/' + locale">
                <span class="ka-forma">FORMA</span><span class="ka-azure">BY AZURE</span>
                <span class="ka-ops">KAHEEL OPERATIONS</span>
            </a>
            <nav class="ka-nav-list">
                <router-link v-for="item in items" :key="item.to" class="ka-nav" :to="'/' + locale + item.to">
                    <span v-html="item.icon"></span>
                    {{ t[item.key] }}
                </router-link>
            </nav>
        </aside>

        <div class="ka-content">
            <header class="ka-top">
                <div class="ka-head-start">
                    <button type="button" class="ka-icon-btn ka-menu-btn" :aria-label="t.menu" @click="open = true">
                        <svg viewBox="0 0 24 24"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                    </button>
                    <div>
                        <p class="ka-admin-title">{{ t.administration }}</p>
                        <p class="ka-admin-role">{{ t.administrator }}</p>
                    </div>
                </div>
                <div class="ka-head-actions">
                    <button type="button" class="ka-icon-btn" aria-label="language" @click="toggleLang">
                        <svg viewBox="0 0 24 24"><path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/></svg>
                    </button>
                    <button type="button" class="ka-icon-btn" :aria-label="t.signOut" @click="logout">
                        <svg viewBox="0 0 24 24"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                    </button>
                </div>
            </header>
            <main class="ka-main">
                <router-view :key="$route.fullPath"></router-view>
            </main>
        </div>

        <div v-if="open" class="ka-backdrop" @click="open = false"></div>
        <aside v-if="open" class="ka-sheet">
            <a class="ka-brand" :href="'/' + locale" @click="open = false">
                <span class="ka-forma">FORMA</span><span class="ka-azure">BY AZURE</span>
                <span class="ka-ops">KAHEEL OPERATIONS</span>
            </a>
            <nav class="ka-nav-list">
                <router-link v-for="item in items" :key="'m-' + item.to" class="ka-nav" :to="'/' + locale + item.to" @click="open = false">
                    <span v-html="item.icon"></span>
                    {{ t[item.key] }}
                </router-link>
            </nav>
        </aside>
    </div>
</template>

<script>
import { mapActions } from "vuex";

const svg = (inner) => `<svg viewBox="0 0 24 24" aria-hidden="true">${inner}</svg>`;

export default {
    data() {
        return {
            open: false,
            items: [
                { key: "overview", to: "/dashboard", icon: svg('<path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>') },
                { key: "orders", to: "/dashboard/orders", icon: svg('<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>') },
                { key: "staff", to: "/dashboard/staff", icon: svg('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/>') },
                { key: "suppliers", to: "/dashboard/suppliers", icon: svg('<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>') },
                { key: "packages", to: "/dashboard/packages", icon: svg('<path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/>') },
                { key: "alternatives", to: "/dashboard/alternatives", icon: svg('<path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/>') },
                { key: "roles", to: "/dashboard/roles", icon: svg('<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>') },
                { key: "settings", to: "/dashboard/settings", icon: svg('<path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/><circle cx="12" cy="12" r="3"/>') },
            ],
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale || "ar";
        },
        t() {
            const ar = {
                menu: "القائمة",
                signOut: "تسجيل الخروج",
                administration: "بوابة الإدارة",
                administrator: "مدير النظام",
                overview: "نظرة عامة",
                orders: "الطلبات",
                staff: "الموظفون",
                suppliers: "الموردون",
                packages: "الباقات",
                alternatives: "البدائل والأسعار",
                roles: "الأدوار والصلاحيات",
                settings: "الإعدادات",
            };
            const en = {
                menu: "Menu",
                signOut: "Sign out",
                administration: "Administration",
                administrator: "System administrator",
                overview: "Overview",
                orders: "Orders",
                staff: "Staff",
                suppliers: "Suppliers",
                packages: "Packages",
                alternatives: "Alternatives",
                roles: "Roles",
                settings: "Settings",
            };
            return this.locale === "ar" ? ar : en;
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler(locale) {
                document.documentElement.lang = locale;
                document.documentElement.dir = locale === "ar" ? "rtl" : "ltr";
                document.body.classList.add("kaheel-admin");
                document.title = locale === "ar"
                    ? "لوحة مؤشرات الإدارة | FORMA BY AZURE"
                    : "Administration analytics | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        document.body.classList.add("kaheel-admin");
        this.fetchUser();
    },
    methods: {
        ...mapActions(["fetchUser"]),
        toggleLang() {
            const next = this.locale === "ar" ? "en" : "ar";
            const path = this.$route.path.replace(/^\/(ar|en)/, "/" + next);
            window.location.assign(path);
        },
        async logout() {
            try {
                await axios.post("AdminAuth/logOut");
            } catch (error) {
                console.log("Logout failed:", error);
            } finally {
                localStorage.removeItem("authToken");
                localStorage.removeItem("token");
                window.location.assign("/" + this.locale + "/admin/login");
            }
        },
    },
};
</script>
