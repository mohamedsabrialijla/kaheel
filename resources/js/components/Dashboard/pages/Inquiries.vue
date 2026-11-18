<template>
    <div class="ka-packages">
        <div class="ka-sup-head">
            <div>
                <p class="ka-sup-kicker">CONTACT</p>
                <h1>{{ t.title }}</h1>
                <p class="ka-sup-lead">{{ t.lead }}</p>
            </div>
        </div>

        <p v-if="loading" class="ka-sup-muted">…</p>
        <p v-else-if="loadError" class="ka-sup-muted" style="color:#b42318">{{ loadError }}</p>
        <p v-else-if="!rows.length" class="ka-sup-muted">{{ t.empty }}</p>
        <div v-else class="ka-table-wrap">
            <table class="ka-table wide">
                <thead>
                    <tr>
                        <th>{{ t.when }}</th>
                        <th>{{ t.name }}</th>
                        <th>{{ t.phone }}</th>
                        <th>{{ t.type }}</th>
                        <th>{{ t.message }}</th>
                        <th v-if="can('inquiries.delete')"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in rows" :key="row.id">
                        <td>{{ when(row.created_at) }}</td>
                        <td class="ka-primary">{{ row.name }}</td>
                        <td dir="ltr">
                            <a :href="'https://wa.me/' + digits(row.phone)" target="_blank" rel="noopener">{{ row.phone }}</a>
                        </td>
                        <td>{{ locale === 'en' ? row.customer_type_en : row.customer_type_ar }}</td>
                        <td style="white-space:pre-wrap;max-width:28rem">{{ row.message }}</td>
                        <td v-if="can('inquiries.delete')">
                            <button type="button" class="ka-btn ka-btn-ghost ka-btn-inline is-danger" @click="remove(row)">{{ t.delete }}</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            rows: [],
            loading: true,
            loadError: "",
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        t() {
            return this.locale === "en" ? {
                title: "Contact messages",
                lead: "Inquiries submitted from the public contact form.",
                empty: "No messages yet.",
                when: "Date",
                name: "Name",
                phone: "Mobile",
                type: "Customer type",
                message: "Message",
                delete: "Delete",
            } : {
                title: "رسائل التواصل",
                lead: "الرسائل المرسلة من نموذج تواصل معنا في الموقع.",
                empty: "لا رسائل بعد.",
                when: "التاريخ",
                name: "الاسم",
                phone: "الجوال",
                type: "نوع العميل",
                message: "الرسالة",
                delete: "حذف",
            };
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        digits(phone) {
            const raw = String(phone || "").replace(/\D/g, "");
            if (raw.startsWith("0") && raw.length === 10) return "966" + raw.slice(1);
            return raw;
        },
        when(value) {
            if (!value) return "";
            return new Date(value).toLocaleString(this.locale === "en" ? "en-GB" : "ar-EG", {
                day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit",
            });
        },
        async load() {
            this.loading = true;
            this.loadError = "";
            try {
                const response = await axios.get("KaheelInquiries/getAllItems");
                this.rows = response.data.items || [];
            } catch (error) {
                this.rows = [];
                this.loadError = error.response?.data?.message || (this.locale === "ar" ? "تعذر جلب الرسائل" : "Could not load messages");
            } finally {
                this.loading = false;
            }
        },
        async remove(row) {
            if (!confirm(this.locale === "ar" ? "حذف هذه الرسالة؟" : "Delete this message?")) return;
            await axios.post("KaheelInquiries/delete", { id: row.id });
            this.rows = this.rows.filter((item) => item.id !== row.id);
        },
    },
};
</script>
