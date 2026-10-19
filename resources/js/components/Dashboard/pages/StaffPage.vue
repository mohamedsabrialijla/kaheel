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
                        <th>{{ t.status }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading"><td colspan="4">…</td></tr>
                    <tr v-for="person in staff" :key="person.id">
                        <td>
                            <p class="ka-staff-name">{{ person.name }}</p>
                            <p class="ka-sup-muted">{{ person.email }}</p>
                        </td>
                        <td>{{ person.locked ? t.full : roleName(person) }}</td>
                        <td><span :class="person.is_active ? 'is-on' : 'is-off'">{{ person.is_active ? t.active : t.inactive }}</span></td>
                        <td>
                            <div class="ka-role-actions">
                                <button type="button" class="ka-btn ka-btn-outline ka-btn-inline" :disabled="person.locked" @click="openEdit(person)">{{ t.edit }}</button>
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
                    <label class="ka-field ka-staff-role">
                        <span>{{ t.role }}</span>
                        <select v-model="form.role_id" required>
                            <option disabled value="">{{ t.pickRole }}</option>
                            <option v-for="role in roles" :key="role.id" :value="String(role.id)">{{ locale === "ar" ? role.name_ar : role.name_en }}</option>
                        </select>
                    </label>
                    <p v-if="!roles.length" class="ka-set-error">{{ t.needRole }}</p>
                    <p v-if="formError" class="ka-set-error">{{ formError }}</p>
                    <button type="submit" class="ka-btn ka-btn-primary ka-sup-submit" :disabled="saving || !roles.length">{{ saving ? "…" : t.create }}</button>
                </form>
            </div>
        </div>

        <div v-if="editDialog" class="ka-sup-overlay" @click.self="editDialog = false">
            <div class="ka-sup-dialog ka-role-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="editDialog = false"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                <h2>{{ t.editTitle }}</h2>
                <p class="ka-sup-muted">{{ t.editLead }}</p>
                <div class="ka-role-form">
                    <label class="ka-field ka-staff-role">
                        <span>{{ t.role }}</span>
                        <select v-model="edit.role_id">
                            <option value="">{{ t.none }}</option>
                            <option v-for="role in roles" :key="'e-' + role.id" :value="String(role.id)">{{ locale === "ar" ? role.name_ar : role.name_en }}</option>
                        </select>
                    </label>
                    <label class="ka-check ka-role-active"><input v-model="edit.is_active" type="checkbox">{{ t.active }}</label>
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
    role_id: "",
});

export default {
    data() {
        return {
            staff: [],
            roles: [],
            loading: true,
            saving: false,
            createDialog: false,
            editDialog: false,
            form: blank(),
            edit: { id: null, role_id: "", is_active: true },
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
                    title: "Staff",
                    lead: "Create employees and assign exactly one role from the list.",
                    newEmployee: "New employee",
                    employee: "Employee",
                    role: "Role",
                    status: "Status",
                    active: "Active",
                    inactive: "Inactive",
                    full: "System administrator",
                    disable: "Disable",
                    enable: "Enable",
                    edit: "Edit",
                    createTitle: "Create employee account",
                    createLead: "Pick one role. The role permissions control what this employee can open.",
                    fullName: "Full name",
                    email: "Email",
                    password: "Temporary password",
                    phone: "Phone",
                    job: "Job title",
                    department: "Department",
                    pickRole: "Select a role",
                    none: "No role",
                    needRole: "Create a role first from Roles and permissions.",
                    create: "Create employee",
                    editTitle: "Edit employee role",
                    editLead: "Change the assigned role. Only one role is allowed.",
                    save: "Save",
                };
            }
            return {
                title: "الموظفون",
                lead: "أنشئ موظفاً وأسند له دوراً واحداً فقط من القائمة.",
                newEmployee: "موظف جديد",
                employee: "الموظف",
                role: "الدور",
                status: "الحالة",
                active: "نشط",
                inactive: "موقوف",
                full: "مدير النظام",
                disable: "إيقاف",
                enable: "تفعيل",
                edit: "تعديل",
                createTitle: "إنشاء حساب موظف",
                createLead: "اختر دوراً واحداً. صلاحيات الدور تحدد ما يراه الموظف في القائمة.",
                fullName: "الاسم الكامل",
                email: "البريد الإلكتروني",
                password: "كلمة المرور المؤقتة",
                phone: "الجوال",
                job: "المسمى الوظيفي",
                department: "القسم",
                pickRole: "اختر دوراً",
                none: "بدون دور",
                needRole: "أنشئ دوراً أولاً من صفحة الأدوار والصلاحيات.",
                create: "إنشاء الموظف",
                editTitle: "تعديل دور الموظف",
                editLead: "غيّر الدور المسند. دور واحد فقط لكل موظف.",
                save: "حفظ",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar"
                    ? "الموظفون | FORMA BY AZURE"
                    : "Staff | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        roleName(person) {
            if (!person.role_id) return this.t.none;
            return this.locale === "ar" ? (person.role_name_ar || this.t.none) : (person.role_name_en || this.t.none);
        },
        apply(data) {
            this.staff = data.staff || [];
            this.roles = data.roles || [];
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
                role_id: person.role_id ? String(person.role_id) : "",
                is_active: person.is_active,
            };
            this.editDialog = true;
        },
        async create() {
            this.saving = true;
            this.formError = "";
            try {
                const response = await axios.post("KaheelStaff/createItem", {
                    ...this.form,
                    role_id: this.form.role_id || null,
                });
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
                const response = await axios.post("KaheelStaff/save", {
                    id: this.edit.id,
                    role_id: this.edit.role_id || null,
                    is_active: this.edit.is_active,
                });
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
                    role_id: person.role_id || null,
                    is_active: !person.is_active,
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
