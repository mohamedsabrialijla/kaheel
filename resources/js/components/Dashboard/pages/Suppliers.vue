<template>
    <div class="ka-suppliers">
        <div class="ka-sup-head">
            <div>
                <p class="ka-sup-kicker">SUPPLY NETWORK</p>
                <h1>{{ t.title }}</h1>
                <p class="ka-sup-lead">{{ t.lead }}</p>
            </div>
            <button type="button" class="ka-btn ka-btn-primary" @click="openCreate">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ t.create }}
            </button>
        </div>

        <div class="ka-sup-grid">
            <p v-if="loading" class="ka-sup-muted">…</p>
            <article v-for="row in rows" :key="row.id" class="ka-sup-card">
                <div class="ka-sup-card-top">
                    <svg class="ka-sup-truck" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                        <path d="M15 18H9"/>
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                        <circle cx="17" cy="18" r="2"/>
                        <circle cx="7" cy="18" r="2"/>
                    </svg>
                    <span :class="row.is_active ? 'is-on' : 'is-off'">{{ row.is_active ? t.active : t.inactive }}</span>
                </div>
                <h2>{{ nameOf(row) }}</h2>
                <p class="ka-sup-muted">{{ row.contact_name || "—" }}</p>
                <p class="ka-sup-rate">{{ t.rate }}: <span>{{ Number(row.commission_rate || 0) }}%</span></p>
                <div class="ka-sup-contact">
                    <p>{{ row.email || "—" }}</p>
                    <p class="ka-sup-muted">{{ row.phone || "—" }}</p>
                </div>
                <button type="button" class="ka-btn ka-btn-outline" @click="openEdit(row)">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                        <path d="m15 5 4 4"/>
                    </svg>
                    {{ t.edit }}
                </button>
            </article>
        </div>

        <div v-if="dialog" class="ka-sup-overlay" @click.self="close">
            <div class="ka-sup-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="close">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                <h2>{{ editing ? t.editTitle : t.createTitle }}</h2>
                <p class="ka-sup-muted">{{ t.dialogLead }}</p>
                <form class="ka-sup-form" @submit.prevent="save">
                    <label class="ka-field">
                        <span>الاسم بالعربية</span>
                        <input v-model="form.name_ar" required maxlength="255">
                    </label>
                    <label class="ka-field">
                        <span>English name</span>
                        <input v-model="form.name_en" required maxlength="255">
                    </label>
                    <label class="ka-field">
                        <span>{{ t.contact }}</span>
                        <input v-model="form.contact_name" maxlength="255">
                    </label>
                    <label class="ka-field">
                        <span>{{ t.phone }}</span>
                        <input v-model="form.phone" maxlength="40">
                    </label>
                    <label class="ka-field">
                        <span>{{ t.rateField }}</span>
                        <input v-model="form.commission_rate" type="number" step="0.1" min="0" max="100">
                    </label>
                    <label v-if="editing" class="ka-check">
                        <input v-model="form.is_active" type="checkbox">
                        {{ t.activeCheck }}
                    </label>
                    <template v-else>
                        <label class="ka-field">
                            <span>{{ t.email }}</span>
                            <input v-model="form.email" type="email" required maxlength="255">
                        </label>
                        <label class="ka-field">
                            <span>{{ t.password }}</span>
                            <input v-model="form.password" type="password" required minlength="6">
                        </label>
                    </template>
                    <p v-if="error" class="ka-sup-error">{{ error }}</p>
                    <button type="submit" class="ka-btn ka-btn-primary ka-sup-submit" :disabled="saving">
                        {{ saving ? "…" : (editing ? t.save : t.submitCreate) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

const blank = () => ({
    name_ar: "",
    name_en: "",
    contact_name: "",
    phone: "",
    commission_rate: 0,
    email: "",
    password: "",
    is_active: true,
});

export default {
    data() {
        return {
            rows: [],
            loading: true,
            dialog: false,
            editing: null,
            form: blank(),
            saving: false,
            error: "",
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        t() {
            if (this.locale === "en") {
                return {
                    title: "Suppliers",
                    lead: "Supplier records, access accounts and the platform rate used in reports.",
                    create: "New supplier",
                    active: "Active",
                    inactive: "Inactive",
                    rate: "Platform rate",
                    edit: "Edit",
                    editTitle: "Edit supplier",
                    createTitle: "Create supplier and account",
                    dialogLead: "The platform rate applies to assigned order cost and appears in reports.",
                    contact: "Contact name",
                    phone: "Phone",
                    rateField: "Platform rate %",
                    activeCheck: "Supplier active",
                    email: "Login email",
                    password: "Temporary password",
                    save: "Save changes",
                    submitCreate: "Create supplier",
                };
            }
            return {
                title: "الموردون",
                lead: "سجل المورد وحساب دخوله ونسبة المنصة المحتسبة في التقارير.",
                create: "مورد جديد",
                active: "نشط",
                inactive: "موقوف",
                rate: "نسبة المنصة",
                edit: "تعديل",
                editTitle: "تعديل المورد",
                createTitle: "إنشاء مورد وحساب دخول",
                dialogLead: "نسبة المنصة تُحتسب على تكلفة الطلبات المسندة وتظهر في التقارير.",
                contact: "اسم المسؤول",
                phone: "الجوال",
                rateField: "نسبة المنصة %",
                activeCheck: "المورد نشط",
                email: "بريد الدخول",
                password: "كلمة المرور المؤقتة",
                save: "حفظ التعديلات",
                submitCreate: "إنشاء المورد",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar"
                    ? "الموردون | FORMA BY AZURE"
                    : "Suppliers | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        nameOf(row) {
            return this.locale === "ar" ? row.name_ar : row.name_en;
        },
        async load() {
            this.loading = true;
            try {
                const response = await axios.get("KaheelSuppliers/getAllItems");
                this.rows = response.data.items || [];
            } catch (error) {
                this.rows = [];
            } finally {
                this.loading = false;
            }
        },
        openCreate() {
            this.editing = null;
            this.form = blank();
            this.error = "";
            this.dialog = true;
        },
        openEdit(row) {
            this.editing = row;
            this.form = {
                ...blank(),
                name_ar: row.name_ar || "",
                name_en: row.name_en || "",
                contact_name: row.contact_name || "",
                phone: row.phone || "",
                commission_rate: Number(row.commission_rate || 0),
                is_active: !!row.is_active,
            };
            this.error = "";
            this.dialog = true;
        },
        close() {
            if (this.saving) return;
            this.dialog = false;
        },
        async save() {
            this.saving = true;
            this.error = "";
            const payload = {
                name_ar: this.form.name_ar,
                name_en: this.form.name_en,
                contact_name: this.form.contact_name,
                phone: this.form.phone,
                commission_rate: this.form.commission_rate,
            };
            try {
                if (this.editing) {
                    await axios.post("KaheelSuppliers/editItem", {
                        ...payload,
                        id: this.editing.id,
                        is_active: this.form.is_active,
                    });
                } else {
                    await axios.post("KaheelSuppliers/createItem", {
                        ...payload,
                        email: this.form.email,
                        password: this.form.password,
                    });
                }
                this.dialog = false;
                await this.load();
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "ar" ? "تعذر الحفظ" : "Could not save");
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>
