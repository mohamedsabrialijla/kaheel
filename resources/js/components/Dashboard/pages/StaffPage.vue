<template>
    <div class="ka-roles ka-staff">
        <div class="ka-sup-head">
            <div>
                <p class="ka-sup-kicker">ACCESS CONTROL</p>
                <h1>{{ t.title }}</h1>
                <p class="ka-sup-lead">{{ t.lead }}</p>
            </div>
            <button type="button" class="ka-btn ka-btn-primary" @click="openCreate">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ t.newEmployee }}
            </button>
        </div>

        <div class="ka-staff-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ t.employee }}</th>
                        <th>{{ t.role }}</th>
                        <th>{{ t.permissions }}</th>
                        <th>{{ t.status }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading"><td colspan="5">…</td></tr>
                    <tr v-for="person in staff" :key="person.id">
                        <td>
                            <p class="ka-staff-name">{{ person.name }}</p>
                            <p class="ka-sup-muted">{{ person.email }}</p>
                        </td>
                        <td>{{ roleLabel(person.account_role) }}</td>
                        <td>
                            <span class="ka-staff-count">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                                {{ person.locked ? t.full : person.permissions.length }}
                            </span>
                        </td>
                        <td><span :class="person.is_active ? 'is-on' : 'is-off'">{{ person.is_active ? t.active : t.inactive }}</span></td>
                        <td>
                            <div class="ka-role-actions">
                                <button type="button" class="ka-btn ka-btn-outline ka-btn-inline" :disabled="person.locked" @click="openEdit(person)">{{ t.permissions }}</button>
                                <button type="button" class="ka-btn ka-btn-ghost ka-btn-inline" :disabled="person.locked || saving" @click="toggle(person)">{{ person.is_active ? t.disable : t.enable }}</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-if="error" class="ka-set-error">{{ error }}</p>

        <div v-if="createDialog" class="ka-sup-overlay" @click.self="createDialog = false">
            <div class="ka-sup-dialog ka-role-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="createDialog = false"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                <h2>{{ t.createTitle }}</h2>
                <p class="ka-sup-muted">{{ t.createLead }}</p>
                <form class="ka-role-form" @submit.prevent="create">
                    <label class="ka-field"><span>{{ t.fullName }}</span><input v-model="form.name" required></label>
                    <label class="ka-field"><span>{{ t.email }}</span><input v-model="form.email" type="email" required></label>
                    <label class="ka-field"><span>{{ t.password }}</span><input v-model="form.password" type="password" required></label>
                    <label class="ka-field"><span>{{ t.phone }}</span><input v-model="form.phone"></label>
                    <label class="ka-field"><span>{{ t.job }}</span><input v-model="form.job_title"></label>
                    <label class="ka-field"><span>{{ t.department }}</span><input v-model="form.department"></label>
                    <label class="ka-field">
                        <span>{{ t.role }}</span>
                        <select v-model="form.account_role">
                            <option value="admin_staff">{{ roleLabel('admin_staff') }}</option>
                            <option value="admin">{{ roleLabel('admin') }}</option>
                        </select>
                    </label>
                    <div class="ka-staff-grant">
                        <p>{{ t.granted }}</p>
                        <div class="ka-role-perms">
                            <label v-for="perm in catalog" :key="perm.code" class="ka-role-perm">
                                <input type="checkbox" :checked="form.permissions.includes(perm.code)" @change="toggleCode(form, perm.code, $event.target.checked)">
                                {{ locale === "ar" ? perm.label_ar : perm.label_en }}
                            </label>
                        </div>
                    </div>
                    <p v-if="formError" class="ka-set-error">{{ formError }}</p>
                    <button type="submit" class="ka-btn ka-btn-primary ka-sup-submit" :disabled="saving">{{ saving ? "…" : t.create }}</button>
                </form>
            </div>
        </div>

        <div v-if="editDialog" class="ka-sup-overlay" @click.self="editDialog = false">
            <div class="ka-sup-dialog ka-role-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="editDialog = false"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                <h2>{{ t.editTitle }}</h2>
                <p class="ka-sup-muted">{{ t.editLead }}</p>
                <div class="ka-role-form">
                    <label class="ka-field">
                        <span>{{ t.role }}</span>
                        <select v-model="edit.account_role">
                            <option value="admin_staff">{{ roleLabel('admin_staff') }}</option>
                            <option value="admin">{{ roleLabel('admin') }}</option>
                        </select>
                    </label>
                    <div class="ka-staff-grant">
                        <div class="ka-role-perms">
                            <label v-for="perm in catalog" :key="'e-' + perm.code" class="ka-role-perm">
                                <input type="checkbox" :checked="edit.permissions.includes(perm.code)" @change="toggleCode(edit, perm.code, $event.target.checked)">
                                {{ locale === "ar" ? perm.label_ar : perm.label_en }}
                            </label>
                        </div>
                    </div>
                    <p v-if="formError" class="ka-set-error">{{ formError }}</p>
                    <button type="button" class="ka-btn ka-btn-primary ka-sup-submit" :disabled="saving" @click="saveEdit">{{ saving ? "…" : t.save }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

const blank = () => ({
    name: "",
    email: "",
    password: "",
    phone: "",
    job_title: "",
    department: "",
    account_role: "admin_staff",
    permissions: [],
});

export default {
    data() {
        return {
            staff: [],
            catalog: [],
            loading: true,
            saving: false,
            createDialog: false,
            editDialog: false,
            form: blank(),
            edit: { id: null, account_role: "admin_staff", is_active: true, permissions: [] },
            error: "",
            formError: "",
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        t() {
            if (this.locale === "en") {
                return {
                    title: "Staff & permissions",
                    lead: "Each employee only sees and performs explicitly granted actions.",
                    newEmployee: "New employee",
                    employee: "Employee",
                    role: "Role",
                    permissions: "Permissions",
                    status: "Status",
                    active: "Active",
                    inactive: "Inactive",
                    full: "Full",
                    disable: "Disable",
                    enable: "Enable",
                    createTitle: "Create employee account",
                    createLead: "Choose the role and exact permissions. They can be changed later.",
                    fullName: "Full name",
                    email: "Email",
                    password: "Temporary password",
                    phone: "Phone",
                    job: "Job title",
                    department: "Department",
                    granted: "Granted permissions",
                    create: "Create employee",
                    editTitle: "Edit role and permissions",
                    editLead: "Changes apply immediately and are audit logged.",
                    save: "Save permissions",
                };
            }
            return {
                title: "الموظفون والصلاحيات",
                lead: "كل موظف يرى وينفّذ فقط ما يمنحه مدير النظام.",
                newEmployee: "موظف جديد",
                employee: "الموظف",
                role: "الدور",
                permissions: "الصلاحيات",
                status: "الحالة",
                active: "نشط",
                inactive: "موقوف",
                full: "كامل",
                disable: "إيقاف",
                enable: "تفعيل",
                createTitle: "إنشاء حساب موظف",
                createLead: "حدّد الدور والصلاحيات بدقة. يمكن تعديلها لاحقاً.",
                fullName: "الاسم الكامل",
                email: "البريد الإلكتروني",
                password: "كلمة المرور المؤقتة",
                phone: "الجوال",
                job: "المسمى الوظيفي",
                department: "القسم",
                granted: "الصلاحيات الممنوحة",
                create: "إنشاء الموظف",
                editTitle: "تعديل الدور والصلاحيات",
                editLead: "تطبق التغييرات فور الحفظ وتسجل باسم مدير النظام.",
                save: "حفظ الصلاحيات",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar"
                    ? "الموظفون والصلاحيات | FORMA BY AZURE"
                    : "Staff & permissions | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        roleLabel(role) {
            const labels = {
                admin: this.locale === "ar" ? "مدير النظام" : "System administrator",
                admin_staff: this.locale === "ar" ? "موظف إدارة" : "Admin staff",
            };
            return labels[role] || role;
        },
        apply(data) {
            this.staff = data.staff || [];
            this.catalog = data.catalog || [];
        },
        async load() {
            this.loading = true;
            this.error = "";
            try {
                const response = await axios.get("KaheelStaff/getAllItems");
                this.apply(response.data.items || {});
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر تحميل الموظفين." : "Could not load staff.";
            } finally {
                this.loading = false;
            }
        },
        openCreate() {
            this.form = blank();
            this.formError = "";
            this.createDialog = true;
        },
        openEdit(person) {
            this.formError = "";
            this.edit = {
                id: person.id,
                account_role: person.account_role,
                is_active: person.is_active,
                permissions: [...person.permissions],
            };
            this.editDialog = true;
        },
        toggleCode(target, code, checked) {
            if (checked) {
                if (!target.permissions.includes(code)) target.permissions.push(code);
                return;
            }
            target.permissions = target.permissions.filter((item) => item !== code);
        },
        async create() {
            this.saving = true;
            this.formError = "";
            try {
                const response = await axios.post("KaheelStaff/createItem", this.form);
                if (!response.data.status) {
                    this.formError = response.data.message || (this.locale === "ar" ? "تعذر الإنشاء." : "Could not create.");
                    return;
                }
                this.apply(response.data.items || {});
                this.createDialog = false;
            } catch (error) {
                this.formError = this.locale === "ar" ? "تعذر الإنشاء." : "Could not create.";
            } finally {
                this.saving = false;
            }
        },
        async saveEdit() {
            this.saving = true;
            this.formError = "";
            try {
                const response = await axios.post("KaheelStaff/save", this.edit);
                if (!response.data.status) {
                    this.formError = response.data.message || (this.locale === "ar" ? "تعذر الحفظ." : "Could not save.");
                    return;
                }
                this.apply(response.data.items || {});
                this.editDialog = false;
            } catch (error) {
                this.formError = this.locale === "ar" ? "تعذر الحفظ." : "Could not save.";
            } finally {
                this.saving = false;
            }
        },
        async toggle(person) {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.post("KaheelStaff/save", {
                    id: person.id,
                    account_role: person.account_role,
                    is_active: !person.is_active,
                    permissions: person.permissions,
                });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر التحديث." : "Could not update.");
                    return;
                }
                this.apply(response.data.items || {});
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر التحديث." : "Could not update.";
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>
