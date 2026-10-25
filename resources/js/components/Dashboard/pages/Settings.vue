<template>
    <div class="ka-settings">
        <p class="ka-sup-kicker">SYSTEM CONFIGURATION</p>
        <h1>{{ t.title }}</h1>
        <p class="ka-sup-lead">{{ t.lead }}</p>

        <p v-if="loading" class="ka-sup-muted">…</p>
        <div v-else class="ka-set-list">
            <label v-for="row in rows" :key="row.key" class="ka-set-row">
                <span>
                    <span class="ka-set-label">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 17H5"/><path d="M19 7h-9"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
                        {{ locale === "ar" ? row.label_ar : row.label_en }}
                    </span>
                    <span class="ka-set-key">{{ row.key }}</span>
                </span>
                <input v-model="values[row.key]" type="text" :disabled="!can('settings.edit')">
            </label>
        </div>

        <p v-if="error" class="ka-set-error">{{ error }}</p>
        <p v-if="saved" class="ka-set-ok">{{ t.saved }}</p>
        <button v-if="can('settings.edit')" type="button" class="ka-btn ka-btn-primary ka-set-save" :disabled="saving || loading" @click="save">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/></svg>
            {{ saving ? "…" : t.save }}
        </button>
    </div>
</template>

<script>
import axios from "axios";

const numeric = ["delivery_working_days", "deposit_percent"];

export default {
    data() {
        return {
            rows: [],
            values: {},
            loading: true,
            saving: false,
            saved: false,
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
                    title: "System settings",
                    lead: "Shared operational values used in the customer journey.",
                    save: "Save changes",
                    saved: "Settings saved.",
                };
            }
            return {
                title: "إعدادات النظام",
                lead: "القيم التشغيلية المشتركة في رحلة العميل.",
                save: "حفظ التغييرات",
                saved: "تم حفظ الإعدادات.",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar"
                    ? "إعدادات النظام | FORMA BY AZURE"
                    : "System settings | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        async load() {
            this.loading = true;
            this.error = "";
            try {
                const response = await axios.get("KaheelSettings/getAllItems");
                this.rows = response.data.items || [];
                this.values = Object.fromEntries(this.rows.map((row) => [row.key, row.value == null ? "" : String(row.value)]));
            } catch (error) {
                this.rows = [];
                this.error = this.locale === "ar" ? "تعذر تحميل الإعدادات." : "Could not load settings.";
            } finally {
                this.loading = false;
            }
        },
        async save() {
            this.saving = true;
            this.saved = false;
            this.error = "";
            try {
                const response = await axios.post("KaheelSettings/save", {
                    settings: this.rows.map((row) => ({
                        key: row.key,
                        value: numeric.includes(row.key) ? Number(this.values[row.key]) : this.values[row.key],
                    })),
                });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر الحفظ." : "Could not save.");
                    return;
                }
                this.rows = response.data.items || this.rows;
                this.values = Object.fromEntries(this.rows.map((row) => [row.key, row.value == null ? "" : String(row.value)]));
                this.saved = true;
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر الحفظ." : "Could not save.";
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>
