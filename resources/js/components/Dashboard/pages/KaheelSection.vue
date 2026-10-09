<template>
    <section>
        <p class="ka-eyebrow">FORMA OPERATIONS</p>
        <h1 class="ka-title">{{ title }}</h1>
    </section>
</template>

<script>
const titles = {
    orders: { ar: "الطلبات", en: "Orders" },
    staff: { ar: "الموظفون", en: "Staff" },
    suppliers: { ar: "الموردون", en: "Suppliers" },
    packages: { ar: "الباقات", en: "Packages" },
    alternatives: { ar: "البدائل والأسعار", en: "Alternatives" },
    roles: { ar: "الأدوار والصلاحيات", en: "Roles" },
    settings: { ar: "الإعدادات", en: "Settings" },
};

export default {
    computed: {
        title() {
            const locale = this.$route.params.locale === "en" ? "en" : "ar";
            const key = this.$route.meta.kaheel || "orders";
            return (titles[key] || titles.orders)[locale];
        },
    },
};
</script>
