<template>
    <div class="ka-roles">
        <div class="ka-sup-head">
            <div>
                <p class="ka-sup-kicker">ACCESS CONTROL</p>
                <h1>{{ t.title }}</h1>
                <p class="ka-sup-lead">{{ t.lead }}</p>
            </div>
            <button type="button" class="ka-btn ka-btn-primary" @click="openRole(null)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ t.newRole }}
            </button>
        </div>

        <div class="ka-role-grid">
            <p v-if="loading" class="ka-sup-muted">…</p>
            <article v-for="role in roles" :key="role.id" class="ka-role-card">
                <div class="ka-role-top">
                    <h2>{{ locale === "ar" ? role.name_ar : role.name_en }}</h2>
                    <span :class="role.is_active ? 'is-on' : 'is-off'">{{ role.is_active ? t.active : t.disabled }}</span>
                </div>
                <p class="ka-role-meta">{{ t.permissions }}: {{ role.permissions.length }} · {{ t.members }}: {{ role.members }}</p>
                <ul class="ka-role-tags">
                    <li v-for="code in role.permissions.slice(0, 6)" :key="code">{{ labelOf(code) }}</li>
                </ul>
                <div class="ka-role-actions">
                    <button type="button" class="ka-btn ka-btn-outline ka-btn-inline" @click="openRole(role)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                        {{ t.edit }}
                    </button>
                    <button type="button" class="ka-btn ka-btn-ghost ka-btn-inline is-danger" @click="remove(role)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        {{ t.remove }}
                    </button>
                </div>
            </article>
        </div>

        <section class="ka-role-staff">
            <h2>{{ t.assign }}</h2>
            <ul>
                <li v-for="person in staff" :key="person.id">
                    <div>
                        <p>{{ person.name }}</p>
                        <p class="ka-sup-muted">{{ person.email }}</p>
                    </div>
                    <select :value="person.role_id || ''" :disabled="person.locked" @change="assign(person, $event.target.value)">
                        <option value="">{{ t.none }}</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ locale === "ar" ? role.name_ar : role.name_en }}</option>
                    </select>
                </li>
            </ul>
            <p v-if="error" class="ka-set-error">{{ error }}</p>
        </section>

        <div v-if="dialog" class="ka-sup-overlay" @click.self="dialog = false">
            <div class="ka-sup-dialog ka-role-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="dialog = false">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                <h2>{{ form.id ? t.editRole : t.newRole }}</h2>
                <p class="ka-sup-muted">{{ t.dialogLead }}</p>
                <form class="ka-role-form" @submit.prevent="save">
                    <label class="ka-field"><span>الاسم بالعربية</span><input v-model="form.name_ar" required></label>
                    <label class="ka-field"><span>English name</span><input v-model="form.name_en" required></label>
                    <div class="ka-role-perms">
                        <label v-for="perm in catalog" :key="perm.code" class="ka-role-perm">
                            <input type="checkbox" :checked="form.permissions.includes(perm.code)" @change="toggle(perm.code, $event.target.checked)">
                            {{ locale === "ar" ? perm.label_ar : perm.label_en }}
                        </label>
                    </div>
                    <label class="ka-check ka-role-active"><input v-model="form.is_active" type="checkbox">{{ t.activeRole }}</label>
                    <p v-if="formError" class="ka-set-error">{{ formError }}</p>
                    <button type="submit" class="ka-btn ka-btn-primary" :disabled="saving">{{ saving ? "…" : t.save }}</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

const blank = () => ({
    id: null,
    name_ar: "",
    name_en: "",
    is_active: true,
    permissions: [],
});

export default {
    data() {
        return {
            roles: [],
            catalog: [],
            staff: [],
            loading: true,
            dialog: false,
            form: blank(),
            saving: false,
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
                    title: "Roles and permissions",
                    lead: "Create permission sets and assign them to admin staff; edits apply to members instantly.",
                    newRole: "New role",
                    active: "Active",
                    disabled: "Disabled",
                    permissions: "Permissions",
                    members: "Members",
                    edit: "Edit",
                    remove: "Delete",
                    assign: "Assign roles to staff",
                    none: "No role",
                    editRole: "Edit role",
                    dialogLead: "Pick the permissions this role grants.",
                    activeRole: "Role active",
                    save: "Save role",
                };
            }
            return {
                title: "الأدوار والصلاحيات",
                lead: "أنشئ أدواراً بصلاحيات محددة وأسندها لموظفي الإدارة؛ أي تعديل على الدور ينعكس فوراً على أعضائه.",
                newRole: "دور جديد",
                active: "مفعّل",
                disabled: "معطّل",
                permissions: "الصلاحيات",
                members: "الأعضاء",
                edit: "تعديل",
                remove: "حذف",
                assign: "إسناد الأدوار للموظفين",
                none: "بدون دور",
                editRole: "تعديل الدور",
                dialogLead: "اختر الصلاحيات التي يمنحها هذا الدور.",
                activeRole: "الدور مفعّل",
                save: "حفظ الدور",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar"
                    ? "الأدوار والصلاحيات | FORMA BY AZURE"
                    : "Roles and permissions | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        labelOf(code) {
            const perm = this.catalog.find((row) => row.code === code);
            if (!perm) return code;
            return this.locale === "ar" ? perm.label_ar : perm.label_en;
        },
        apply(data) {
            this.roles = data.roles || [];
            this.catalog = data.catalog || [];
            this.staff = data.staff || [];
        },
        async load() {
            this.loading = true;
            this.error = "";
            try {
                const response = await axios.get("KaheelRoles/getAllItems");
                this.apply(response.data.items || {});
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر تحميل الأدوار." : "Could not load roles.";
            } finally {
                this.loading = false;
            }
        },
        openRole(role) {
            this.formError = "";
            this.form = role
                ? { id: role.id, name_ar: role.name_ar, name_en: role.name_en, is_active: !!role.is_active, permissions: [...role.permissions] }
                : blank();
            this.dialog = true;
        },
        toggle(code, checked) {
            if (checked) {
                if (!this.form.permissions.includes(code)) this.form.permissions.push(code);
                return;
            }
            this.form.permissions = this.form.permissions.filter((item) => item !== code);
        },
        async save() {
            this.saving = true;
            this.formError = "";
            try {
                const response = await axios.post("KaheelRoles/save", this.form);
                if (!response.data.status) {
                    this.formError = response.data.message || (this.locale === "ar" ? "تعذر الحفظ." : "Could not save.");
                    return;
                }
                this.apply(response.data.items || {});
                this.dialog = false;
            } catch (error) {
                this.formError = this.locale === "ar" ? "تعذر الحفظ." : "Could not save.";
            } finally {
                this.saving = false;
            }
        },
        async remove(role) {
            this.error = "";
            try {
                const response = await axios.post("KaheelRoles/delete", { id: role.id });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر الحذف." : "Could not delete.");
                    return;
                }
                this.apply(response.data.items || {});
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر الحذف." : "Could not delete.";
            }
        },
        async assign(person, roleId) {
            this.error = "";
            try {
                const response = await axios.post("KaheelRoles/assign", {
                    user_id: person.id,
                    role_id: roleId || null,
                });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر الإسناد." : "Could not assign.");
                    return;
                }
                this.apply(response.data.items || {});
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر الإسناد." : "Could not assign.";
            }
        },
    },
};
</script>
