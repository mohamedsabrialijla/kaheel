<template>
    <div class="ka-alt">
        <p class="ka-sup-kicker">ALTERNATIVES LIBRARY</p>
        <h1>{{ t.title }}</h1>

        <div class="ka-alt-help">
            <p class="ka-alt-help-title">{{ t.how }}</p>
            <ol class="ka-alt-steps">
                <li>{{ t.step1 }}</li>
                <li>{{ t.step2 }}</li>
                <li>{{ t.step3 }}</li>
            </ol>
            <p class="ka-alt-note">{{ t.note }}</p>
            <p class="ka-alt-note">{{ t.library }}</p>
        </div>

        <div v-if="can('alternatives.create') || can('alternatives.delete')" class="ka-alt-add">
            <template v-if="can('alternatives.create')">
            <p class="ka-alt-help-title">{{ t.addCategoryTitle }}</p>
            <div class="ka-alt-form">
                <label class="ka-field"><span>{{ t.categoryAr }}</span><input v-model="categoryForm.name_ar" :placeholder="locale === 'ar' ? 'مثل: الحمام' : 'e.g. Bathroom'"></label>
                <label class="ka-field"><span>{{ t.categoryEn }}</span><input v-model="categoryForm.name_en" placeholder="Bathroom"></label>
            </div>
            <button type="button" class="ka-btn ka-btn-outline ka-alt-add-btn" :disabled="saving || !categoryForm.name_ar.trim() || !categoryForm.name_en.trim()" @click="addCategory">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ saving ? "…" : t.addCategory }}
            </button>
            </template>
            <ul class="ka-alt-cats" v-if="categories.length">
                <li v-for="cat in categories" :key="cat.code">
                    <span>{{ locale === "ar" ? cat.ar : cat.en }}</span>
                    <button v-if="can('alternatives.delete')" type="button" class="ka-btn ka-btn-ghost ka-btn-inline is-danger" :disabled="saving" @click="removeCategory(cat)">{{ t.deleteCategory }}</button>
                </li>
            </ul>
            <p v-if="categoryError" class="ka-set-error">{{ categoryError }}</p>
            <p v-else-if="categoryOk" class="ka-ord-ok">{{ categoryOk }}</p>
        </div>

        <div v-if="can('alternatives.create')" class="ka-alt-add">
            <p class="ka-alt-help-title">{{ t.addTitle }}</p>
            <div class="ka-alt-form">
                <label class="ka-field"><span>{{ t.category }}</span>
                    <select v-model="form.category">
                        <option v-for="cat in categories" :key="cat.code" :value="cat.code">{{ locale === "ar" ? cat.ar : cat.en }}</option>
                    </select>
                </label>
                <label class="ka-field"><span>{{ t.nameAr }}</span><input v-model="form.name_ar" :placeholder="locale === 'ar' ? 'تلفزيون سامسونج 65 بوصة' : '...'"></label>
                <label class="ka-field"><span>{{ t.nameEn }}</span><input v-model="form.name_en" placeholder='Samsung 65" TV'></label>
                <label class="ka-field"><span>{{ t.delta }}</span><input v-model.number="form.price_delta" type="number"></label>
            </div>
            <button type="button" class="ka-btn ka-btn-primary ka-alt-add-btn" :disabled="saving || !form.name_ar.trim() || !form.name_en.trim()" @click="add">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ saving ? "…" : t.add }}
            </button>
            <p v-if="error" class="ka-set-error">{{ error }}</p>
        </div>

        <div class="ka-alt-filters">
            <span>{{ t.filter }}</span>
            <button type="button" class="ka-btn ka-btn-inline" :class="filter === 'all' ? 'ka-btn-primary' : 'ka-btn-outline'" @click="filter = 'all'">{{ t.all }}</button>
            <button v-for="cat in categories" :key="cat.code" type="button" class="ka-btn ka-btn-inline" :class="filter === cat.code ? 'ka-btn-primary' : 'ka-btn-outline'" @click="filter = cat.code">{{ locale === "ar" ? cat.ar : cat.en }}</button>
        </div>

        <p v-if="loading" class="ka-sup-muted">{{ t.loading }}</p>
        <p v-else-if="!groups.length" class="ka-alt-empty">{{ t.empty }}</p>
        <div v-else class="ka-alt-groups">
            <section v-for="group in groups" :key="group.code" class="ka-alt-group">
                <header>
                    <h2>{{ categoryName(group.code) }}</h2>
                    <span>{{ group.rows.length }} {{ t.altWord }}</span>
                </header>
                <div v-for="row in group.rows" :key="row.id" class="ka-alt-row">
                    <template v-if="editing && editing.id === row.id">
                        <div class="ka-alt-edit-names">
                            <input v-model="editing.name_ar">
                            <input v-model="editing.name_en">
                        </div>
                        <input v-model.number="editing.price_delta" type="number">
                        <div class="ka-alt-row-actions">
                            <button type="button" class="ka-btn ka-btn-primary ka-btn-inline" :disabled="saving" @click="saveEdit(row)">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                {{ t.save }}
                            </button>
                            <button type="button" class="ka-btn ka-btn-ghost ka-btn-inline" @click="editing = null">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <template v-else>
                        <div>
                            <p class="ka-alt-name">{{ locale === "ar" ? row.name_ar : row.name_en }}</p>
                            <p class="ka-sup-muted">{{ locale === "ar" ? row.name_en : row.name_ar }}</p>
                        </div>
                        <div>
                            <p class="ka-alt-price" :class="priceClass(row.price_delta)">{{ money(row.price_delta) }} {{ t.sar }}</p>
                            <p class="ka-sup-muted">{{ priceNote(row.price_delta) }}</p>
                        </div>
                        <div v-if="can('alternatives.edit')" class="ka-alt-row-actions">
                            <button type="button" class="ka-btn ka-btn-outline ka-btn-inline" @click="startEdit(row)">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                {{ t.edit }}
                            </button>
                            <button type="button" class="ka-btn ka-btn-inline" :class="row.is_active ? 'ka-btn-soft' : 'ka-btn-outline'" :disabled="saving" @click="toggle(row)">
                                {{ row.is_active ? t.disable : t.enable }}
                            </button>
                        </div>
                    </template>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import axios from "axios";

const fallbackCategories = [
    { code: "furniture", ar: "الأثاث", en: "Furniture" },
    { code: "kitchen", ar: "المطبخ", en: "Kitchen" },
    { code: "wardrobe", ar: "الدواليب", en: "Wardrobes" },
    { code: "appliances", ar: "الأجهزة", en: "Appliances" },
    { code: "tv", ar: "الشاشات والتلفزيون", en: "TV & screens" },
    { code: "curtains", ar: "الستائر", en: "Curtains" },
    { code: "accessories", ar: "الإكسسوارات", en: "Accessories" },
    { code: "soft", ar: "المفروشات", en: "Soft furnishings" },
    { code: "lighting", ar: "الإنارة", en: "Lighting" },
];

export default {
    data() {
        return {
            categories: fallbackCategories,
            rows: [],
            loading: true,
            saving: false,
            error: "",
            categoryError: "",
            categoryOk: "",
            filter: "all",
            editing: null,
            form: { category: "tv", name_ar: "", name_en: "", price_delta: 0 },
            categoryForm: { name_ar: "", name_en: "" },
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        activeCount() {
            return this.rows.filter((row) => row.is_active).length;
        },
        groups() {
            const grouped = new Map();
            this.rows.forEach((row) => {
                if (this.filter !== "all" && row.category !== this.filter) return;
                if (!grouped.has(row.category)) grouped.set(row.category, []);
                grouped.get(row.category).push(row);
            });
            return [...grouped.entries()].map(([code, rows]) => ({ code, rows }));
        },
        t() {
            const library = this.locale === "ar"
                ? `المكتبة الحالية: ${this.rows.length} بديل، منها ${this.activeCount} مفعّل ويظهر للعملاء.`
                : `Library: ${this.rows.length} alternatives, ${this.activeCount} active and visible to customers.`;
            if (this.locale === "en") {
                return {
                    title: "Alternatives & pricing library",
                    how: "How do alternatives work?",
                    step1: "Add ready-made alternatives per category (e.g. Samsung 65\" TV) and set the price difference versus the package default.",
                    step2: "While preparing an order design, pick a component then insert alternatives from this library in one click.",
                    step3: "The customer sees the alternatives inside the design form; choosing one adds the difference to the final invoice.",
                    note: "Price difference is in SAR excluding VAT: positive adds cost, zero is a same-price swap, negative is a discount.",
                    library,
                    addTitle: "Add a new alternative",
                    addCategoryTitle: "Add a new category",
                    addCategory: "Add category",
                    deleteCategory: "Delete",
                    deleteCategoryAsk: "Delete this category?",
                    categoryAr: "Category name (AR)",
                    categoryEn: "Category name (EN)",
                    category: "Category",
                    nameAr: "Arabic name",
                    nameEn: "English name",
                    delta: "Price difference (SAR)",
                    add: "Add to library",
                    filter: "Filter category:",
                    all: "All",
                    loading: "Loading…",
                    empty: "No alternatives in this category yet.",
                    altWord: "alternatives",
                    sar: "SAR",
                    edit: "Edit",
                    save: "Save",
                    extra: "Customer pays extra",
                    discount: "Deducted from package",
                    same: "Same-price swap",
                    disable: "Active — disable",
                    enable: "Disabled — enable",
                };
            }
            return {
                title: "مكتبة البدائل والأسعار",
                how: "كيف تعمل البدائل؟",
                step1: "تضيف هنا البدائل الجاهزة لكل تصنيف (مثال: تلفزيون سامسونج 65 بوصة) وتحدد فرق سعره عن الخيار الأساسي في الباقة.",
                step2: "عند إعداد تصميم أي طلب، تختار المكوّن ثم تُدرج البدائل من هذه المكتبة بضغطة واحدة بدل كتابتها يدوياً.",
                step3: "العميل يرى البدائل داخل نموذج التصميم، وعند اختياره بديلاً يُضاف فرق السعر تلقائياً إلى فاتورته النهائية.",
                note: "فرق السعر بالريال وبدون ضريبة: موجب يعني تكلفة إضافية على العميل، صفر يعني بديل بنفس السعر، وسالب يعني خصم.",
                library,
                addTitle: "إضافة بديل جديد",
                addCategoryTitle: "إضافة تصنيف جديد",
                addCategory: "إضافة التصنيف",
                deleteCategory: "حذف",
                deleteCategoryAsk: "حذف هذا التصنيف؟",
                categoryAr: "اسم التصنيف بالعربية",
                categoryEn: "اسم التصنيف بالإنجليزية",
                category: "التصنيف",
                nameAr: "اسم البديل بالعربية",
                nameEn: "اسم البديل بالإنجليزية",
                delta: "فرق السعر (ريال)",
                add: "إضافة إلى المكتبة",
                filter: "عرض التصنيف:",
                all: "الكل",
                loading: "جارٍ التحميل…",
                empty: "لا توجد بدائل في هذا التصنيف بعد.",
                altWord: "بديل",
                sar: "ريال",
                edit: "تعديل",
                save: "حفظ",
                extra: "يدفعها العميل إضافة على الباقة",
                discount: "خصم من قيمة الباقة",
                same: "بديل بنفس السعر",
                disable: "مفعّل — إيقاف",
                enable: "موقوف — تفعيل",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar"
                    ? "مكتبة البدائل والأسعار | FORMA BY AZURE"
                    : "Alternatives & pricing library | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        categoryName(code) {
            const cat = this.categories.find((item) => item.code === code);
            if (!cat) return code;
            return this.locale === "ar" ? cat.ar : cat.en;
        },
        money(value) {
            const number = Number(value) || 0;
            const text = new Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
            return number > 0 ? `+${text}` : text;
        },
        priceClass(value) {
            const number = Number(value) || 0;
            if (number > 0) return "is-plus";
            if (number < 0) return "is-minus";
            return "is-zero";
        },
        priceNote(value) {
            const number = Number(value) || 0;
            if (number > 0) return this.t.extra;
            if (number < 0) return this.t.discount;
            return this.t.same;
        },
        async load() {
            this.loading = true;
            try {
                const [alts, cats] = await Promise.all([
                    axios.get("KaheelAlternatives/getAllItems"),
                    axios.get("KaheelCategories/getAllItems"),
                ]);
                this.rows = alts.data.items || [];
                if (cats.data.items?.length) this.categories = cats.data.items;
                if (!this.categories.some((row) => row.code === this.form.category) && this.categories[0]) {
                    this.form.category = this.categories[0].code;
                }
            } catch (error) {
                this.rows = [];
            } finally {
                this.loading = false;
            }
        },
        async removeCategory(cat) {
            if (!window.confirm(this.t.deleteCategoryAsk)) return;
            this.saving = true;
            this.categoryError = "";
            this.categoryOk = "";
            try {
                const response = await axios.post("KaheelCategories/delete", { code: cat.code, id: cat.id });
                if (!response.data.status) {
                    this.categoryError = response.data.message || (this.locale === "ar" ? "تعذر حذف التصنيف." : "Could not delete the category.");
                    return;
                }
                this.categories = response.data.items || [];
                if (this.filter === cat.code) this.filter = "all";
                if (this.form.category === cat.code && this.categories[0]) this.form.category = this.categories[0].code;
                this.categoryOk = response.data.message || (this.locale === "ar" ? "تم حذف التصنيف." : "Category deleted.");
            } catch (error) {
                this.categoryError = error.response?.data?.message || (this.locale === "ar" ? "تعذر حذف التصنيف." : "Could not delete the category.");
            } finally {
                this.saving = false;
            }
        },
        async addCategory() {
            this.saving = true;
            this.categoryError = "";
            this.categoryOk = "";
            try {
                const response = await axios.post("KaheelCategories/save", { ...this.categoryForm });
                if (!response.data.status) {
                    this.categoryError = response.data.message || (this.locale === "ar" ? "تعذر إضافة التصنيف." : "Could not add the category.");
                    return;
                }
                this.categories = response.data.items || this.categories;
                const last = this.categories[this.categories.length - 1];
                if (last) this.form.category = last.code;
                this.categoryForm = { name_ar: "", name_en: "" };
                this.categoryOk = response.data.message || (this.locale === "ar" ? "تم إضافة التصنيف." : "Category added.");
            } catch (error) {
                this.categoryError = error.response?.data?.message || (this.locale === "ar" ? "تعذر إضافة التصنيف." : "Could not add the category.");
            } finally {
                this.saving = false;
            }
        },
        async add() {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.post("KaheelAlternatives/save", { ...this.form, is_active: true });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر الإضافة." : "Could not add.");
                    return;
                }
                this.rows = response.data.items || [];
                this.form = { category: this.form.category, name_ar: "", name_en: "", price_delta: 0 };
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر الإضافة." : "Could not add.";
            } finally {
                this.saving = false;
            }
        },
        startEdit(row) {
            this.editing = { id: row.id, name_ar: row.name_ar, name_en: row.name_en, price_delta: Number(row.price_delta) };
        },
        async saveEdit(row) {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.post("KaheelAlternatives/save", {
                    id: row.id,
                    category: row.category,
                    name_ar: this.editing.name_ar,
                    name_en: this.editing.name_en,
                    price_delta: this.editing.price_delta,
                    is_active: row.is_active,
                });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر الحفظ." : "Could not save.");
                    return;
                }
                this.rows = response.data.items || [];
                this.editing = null;
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر الحفظ." : "Could not save.";
            } finally {
                this.saving = false;
            }
        },
        async toggle(row) {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.post("KaheelAlternatives/save", {
                    id: row.id,
                    category: row.category,
                    name_ar: row.name_ar,
                    name_en: row.name_en,
                    price_delta: row.price_delta,
                    is_active: !row.is_active,
                });
                if (!response.data.status) {
                    this.error = response.data.message || (this.locale === "ar" ? "تعذر التحديث." : "Could not update.");
                    return;
                }
                this.rows = response.data.items || [];
            } catch (error) {
                this.error = this.locale === "ar" ? "تعذر التحديث." : "Could not update.";
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>
