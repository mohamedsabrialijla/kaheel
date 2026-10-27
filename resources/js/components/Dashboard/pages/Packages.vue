<template>
    <div class="ka-packages">
        <div class="ka-sup-head">
            <div>
                <p class="ka-sup-kicker">CATALOGUE</p>
                <h1>{{ t.title }}</h1>
                <p class="ka-sup-lead">{{ t.lead }}</p>
            </div>
            <button v-if="can('packages.create')" type="button" class="ka-btn ka-btn-primary" @click="openPackage(null)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ t.newPackage }}
            </button>
        </div>

        <div class="ka-pkg-grid">
            <p v-if="loading" class="ka-sup-muted">…</p>
            <article v-for="pkg in rows" :key="pkg.id" class="ka-pkg-card">
                <div class="ka-pkg-top">
                    <div>
                        <p class="ka-pkg-code">{{ pkg.code }}</p>
                        <h2>{{ nameOf(pkg) }}</h2>
                        <p class="ka-sup-muted">{{ lineOf(pkg) || "—" }}</p>
                    </div>
                    <span :class="pkg.is_active ? 'is-on' : 'is-off'">{{ pkg.is_active ? t.active : t.disabled }}</span>
                </div>

                <div class="ka-pkg-subhead">
                    <p>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/></svg>
                        {{ t.subs }}
                    </p>
                    <button v-if="can('packages.create')" type="button" class="ka-btn ka-btn-outline ka-btn-inline" @click="openTier(pkg, null)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        {{ t.newTier }}
                    </button>
                </div>

                <ul class="ka-pkg-tiers">
                    <li v-if="!pkg.tiers.length" class="is-empty">{{ t.noTiers }}</li>
                    <li v-for="tier in pkg.tiers" :key="tier.id">
                        <div class="ka-pkg-tier-top">
                            <div>
                                <p class="ka-pkg-tier-name">{{ nameOf(tier) }}</p>
                                <p class="ka-sup-muted">{{ descOf(tier) || "—" }}</p>
                            </div>
                            <span :class="tier.is_active ? 'is-on' : 'is-off'">{{ tier.is_active ? t.active : t.disabled }}</span>
                        </div>
                        <div class="ka-pkg-prices">
                            <div><p>{{ t.excl }}</p><strong>{{ money(tier.price_excl_vat) }}</strong></div>
                            <div><p>{{ t.incl }}</p><strong class="is-primary">{{ money(tier.price_incl_vat) }}</strong></div>
                            <div><p>{{ t.cost }}</p><strong>{{ money(tier.supplier_cost) }}</strong></div>
                        </div>
                        <div class="ka-pkg-actions">
                            <button type="button" class="ka-btn ka-btn-soft" @click="openItems(pkg, tier)">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5h13"/><path d="M13 12h8"/><path d="M13 19h8"/><path d="M3 10a2 2 0 0 0 2 2h3"/><path d="M3 5v12a2 2 0 0 0 2 2h3"/></svg>
                                {{ t.items }} ({{ tier.items.length }})
                            </button>
                            <button v-if="can('packages.edit')" type="button" class="ka-btn ka-btn-ghost" @click="openTier(pkg, tier)">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                {{ t.edit }}
                            </button>
                            <select v-if="can('packages.edit') && pkg.tiers.length > 1" class="ka-pkg-copy" @change="copyItems($event, tier)">
                                <option value="">{{ t.copyFrom }}</option>
                                <option v-for="other in pkg.tiers.filter(row => row.id !== tier.id)" :key="other.id" :value="other.id">{{ nameOf(other) }}</option>
                            </select>
                            <button v-if="can('packages.delete')" type="button" class="ka-btn ka-btn-ghost is-danger" @click="removeTier(tier)">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                {{ t.remove }}
                            </button>
                        </div>
                    </li>
                </ul>

                <p class="ka-pkg-orders">{{ t.orders }}: {{ pkg.ordersCount }}</p>
                <div v-if="can('packages.edit') || can('packages.delete')" class="ka-pkg-actions">
                    <button v-if="can('packages.edit')" type="button" class="ka-btn ka-btn-outline ka-btn-inline" @click="openPackage(pkg)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                        {{ t.editPackage }}
                    </button>
                    <button v-if="can('packages.edit')" type="button" class="ka-btn ka-btn-ghost" @click="togglePackage(pkg)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.77.04"/></svg>
                        {{ pkg.is_active ? t.disable : t.enable }}
                    </button>
                    <button v-if="can('packages.delete')" type="button" class="ka-btn ka-btn-ghost is-danger" @click="removePackage(pkg)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        {{ t.remove }}
                    </button>
                </div>
            </article>
        </div>
        <p v-if="error" class="ka-sup-error">{{ error }}</p>

        <div v-if="packageDialog" class="ka-sup-overlay" @click.self="packageDialog = false">
            <div class="ka-sup-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="packageDialog = false"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                <h2>{{ packageForm.id ? t.editPackage : t.newPackage }}</h2>
                <p class="ka-sup-muted">{{ t.packageLead }}</p>
                <form class="ka-sup-form" @submit.prevent="savePackage">
                    <label class="ka-field"><span>{{ t.code }}</span><input v-model="packageForm.code" required maxlength="40"></label>
                    <label class="ka-field"><span>{{ t.sort }}</span><input v-model="packageForm.sort_order" type="number" min="0"></label>
                    <label class="ka-field"><span>الاسم بالعربية</span><input v-model="packageForm.name_ar" required></label>
                    <label class="ka-field"><span>English name</span><input v-model="packageForm.name_en" required></label>
                    <label class="ka-field"><span>الوصف المختصر</span><input v-model="packageForm.tagline_ar"></label>
                    <label class="ka-field"><span>Tagline</span><input v-model="packageForm.tagline_en"></label>
                    <div class="ka-field ka-span">
                        <span>{{ t.gallery }}</span>
                        <div class="ka-drop" :class="{ 'is-over': galleryOver }" @dragenter.prevent="galleryOver = true" @dragover.prevent="galleryOver = true" @dragleave.prevent="galleryOver = false" @drop.prevent="onGalleryDrop">
                            <button type="button" class="ka-drop-zone" @click="$refs.galleryPick.click()">
                                <strong>{{ t.galleryDrop }}</strong>
                                <span>{{ t.galleryHint }}</span>
                            </button>
                            <div class="ka-drop-list">
                                <div v-for="(item, index) in packageImages" :key="'keep-' + item.id" class="ka-drop-item is-gallery">
                                    <span class="ka-drop-thumb" :style="{ backgroundImage: 'url(' + item.url + ')' }"></span>
                                    <span class="ka-drop-meta">
                                        <input v-model="item.caption_ar" :placeholder="t.captionAr">
                                        <input v-model="item.caption_en" :placeholder="t.captionEn">
                                    </span>
                                    <button type="button" class="ka-drop-x" @click="packageImages.splice(index, 1)">×</button>
                                </div>
                                <div v-for="(item, index) in galleryQueue" :key="item.id" class="ka-drop-item is-gallery">
                                    <span class="ka-drop-thumb" :style="item.preview ? { backgroundImage: 'url(' + item.preview + ')' } : null"></span>
                                    <span class="ka-drop-meta">
                                        <strong>{{ item.file.name }}</strong>
                                        <input v-model="item.caption_ar" :placeholder="t.captionAr">
                                        <input v-model="item.caption_en" :placeholder="t.captionEn">
                                    </span>
                                    <button type="button" class="ka-drop-x" @click="removeQueued(index)">×</button>
                                </div>
                            </div>
                            <input ref="galleryPick" class="ka-drop-input" type="file" accept="image/*,.jpg,.jpeg,.jfif,.png,.webp,.gif,.bmp,.tif,.tiff,.heic,.heif,.avif" multiple @change="queueGallery">
                        </div>
                    </div>
                    <label class="ka-check ka-span"><input v-model="packageForm.is_active" type="checkbox">{{ t.visible }}</label>
                    <p v-if="formError" class="ka-sup-error ka-span">{{ formError }}</p>
                    <button type="submit" class="ka-btn ka-btn-primary ka-sup-submit" :disabled="saving">{{ saving ? "…" : t.savePackage }}</button>
                </form>
            </div>
        </div>

        <div v-if="tierDialog" class="ka-sup-overlay" @click.self="tierDialog = false">
            <div class="ka-sup-dialog" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="tierDialog = false"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                <h2>{{ tierForm.id ? t.editTier : t.newTierTitle }}</h2>
                <p class="ka-sup-muted">{{ t.tierLead }}</p>
                <form class="ka-sup-form" @submit.prevent="saveTier">
                    <label class="ka-field"><span>الاسم بالعربية</span><input v-model="tierForm.name_ar" required></label>
                    <label class="ka-field"><span>English name</span><input v-model="tierForm.name_en" required></label>
                    <label class="ka-field"><span>وصف مختصر</span><input v-model="tierForm.description_ar"></label>
                    <label class="ka-field"><span>Short description</span><input v-model="tierForm.description_en"></label>
                    <label class="ka-field"><span>{{ t.exclField }}</span><input v-model="tierForm.price_excl_vat" type="number" step="0.01" min="0"></label>
                    <label class="ka-field"><span>{{ t.costField }}</span><input v-model="tierForm.supplier_cost" type="number" step="0.01" min="0"></label>
                    <label class="ka-field"><span>{{ t.sort }}</span><input v-model="tierForm.sort_order" type="number" min="0"></label>
                    <label class="ka-check"><input v-model="tierForm.is_active" type="checkbox">{{ t.active }}</label>
                    <p v-if="formError" class="ka-sup-error ka-span">{{ formError }}</p>
                    <button type="submit" class="ka-btn ka-btn-primary ka-sup-submit" :disabled="saving">{{ saving ? "…" : t.saveTier }}</button>
                </form>
            </div>
        </div>

        <div v-if="itemsDialog" class="ka-sup-overlay" @click.self="itemsDialog = false">
            <div class="ka-sup-dialog is-wide" role="dialog" aria-modal="true">
                <button type="button" class="ka-sup-x" aria-label="Close" @click="itemsDialog = false"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                <h2>{{ t.itemsOf }} — {{ nameOf(itemsTier) }}</h2>
                <p class="ka-sup-muted">{{ t.itemsLead }}</p>
                <form class="ka-items-form" @submit.prevent="saveItems">
                <fieldset :disabled="!can('packages.edit')">
                <div class="ka-item" v-for="(item, index) in itemRows" :key="index">
                    <div class="ka-item-grid">
                        <label class="ka-field"><span>{{ t.category }}</span>
                            <select v-model="item.category" required>
                                <option v-for="cat in categories" :key="cat.code" :value="cat.code">{{ locale === "ar" ? cat.ar : cat.en }}</option>
                            </select>
                        </label>
                        <label class="ka-field"><span>{{ t.itemAr }}</span><input v-model="item.name_ar" required></label>
                        <label class="ka-field"><span>{{ t.itemEn }}</span><input v-model="item.name_en" required></label>
                        <label class="ka-field"><span>{{ t.product }}</span><input v-model="item.product_name_ar" required @input="item.product_name_en = item.product_name_ar"></label>
                        <div class="ka-item-split">
                            <label class="ka-field"><span>{{ t.detailAr }}</span><textarea v-model="item.detail_ar" rows="2" required></textarea></label>
                            <label class="ka-field"><span>{{ t.detailEn }}</span><textarea v-model="item.detail_en" rows="2" required></textarea></label>
                        </div>
                        <label class="ka-field"><span>{{ t.qty }}</span><input v-model="item.quantity" type="number" step="0.01" min="0.01" required></label>
                        <label class="ka-field"><span>{{ t.unitAr }}</span><input v-model="item.unit_label_ar" required></label>
                        <label class="ka-field"><span>{{ t.unitEn }}</span><input v-model="item.unit_label_en" required></label>
                        <label class="ka-field"><span>{{ t.extraPrice }}</span><input v-model="item.price_per_unit" type="number" step="0.01" min="0" required></label>
                    </div>
                    <div class="ka-item-foot">
                        <label class="ka-check"><input v-model="item.allow_alternatives" type="checkbox" :disabled="!can('packages.edit')">{{ t.swap }}</label>
                        <div v-if="can('packages.edit')">
                            <button type="button" class="ka-btn ka-btn-ghost" @click="duplicateItem(index)">{{ t.duplicate }}</button>
                            <button type="button" class="ka-btn ka-btn-ghost is-danger" @click="itemRows.splice(index, 1)">{{ t.remove }}</button>
                        </div>
                    </div>
                </div>
                <div v-if="can('packages.edit')" class="ka-pkg-actions">
                    <button type="button" class="ka-btn ka-btn-outline ka-btn-inline" @click="itemRows.push(blankItem())">{{ t.addItem }}</button>
                    <button type="submit" class="ka-btn ka-btn-primary" :disabled="saving">{{ saving ? "…" : t.saveItems }}</button>
                </div>
                <p v-if="formError" class="ka-sup-error">{{ formError }}</p>
                </fieldset>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

const fallbackCategories = [
    { code: "furniture", ar: "أثاث", en: "Furniture" },
    { code: "kitchen", ar: "مطبخ", en: "Kitchen" },
    { code: "wardrobe", ar: "دواليب", en: "Wardrobes" },
    { code: "appliances", ar: "أجهزة", en: "Appliances" },
    { code: "curtains", ar: "ستائر", en: "Curtains" },
    { code: "accessories", ar: "إكسسوارات", en: "Accessories" },
    { code: "soft", ar: "مفروشات", en: "Soft furnishings" },
    { code: "general", ar: "عام", en: "General" },
];

const blankItem = () => ({
    category: "furniture",
    name_ar: "",
    name_en: "",
    product_name_ar: "",
    product_name_en: "",
    detail_ar: "",
    detail_en: "",
    quantity: 1,
    unit_label_ar: "قطعة",
    unit_label_en: "piece",
    price_per_unit: 0,
    allow_alternatives: false,
});

export default {
    data() {
        return {
            rows: [],
            loading: true,
            saving: false,
            error: "",
            formError: "",
            packageDialog: false,
            tierDialog: false,
            itemsDialog: false,
            packageForm: {},
            packageImages: [],
            galleryQueue: [],
            galleryOver: false,
            tierForm: {},
            tierPackageId: null,
            itemsTier: {},
            itemRows: [],
            categories: fallbackCategories,
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        t() {
            return this.locale === "en" ? {
                title: "Package management",
                lead: "Create the main package, then its sub-packages, then the items of each sub-package.",
                newPackage: "New package",
                active: "Active",
                disabled: "Disabled",
                subs: "Sub-packages",
                newTier: "Sub-package",
                noTiers: "No sub-packages yet.",
                excl: "Excl. VAT",
                incl: "Incl. VAT",
                cost: "Supplier cost",
                items: "Items",
                edit: "Edit",
                copyFrom: "Copy items from…",
                remove: "Delete",
                orders: "Orders",
                editPackage: "Edit package",
                disable: "Disable",
                enable: "Enable",
                packageLead: "Prices and items are managed inside sub-packages.",
                code: "Code",
                sort: "Sort order",
                visible: "Package active and visible to customers",
                gallery: "Website gallery",
                galleryDrop: "Drop images here or click to browse",
                galleryHint: "These photos appear on the package page. JPG, PNG, WEBP and similar, several files.",
                captionAr: "Caption (Arabic)",
                captionEn: "Caption (English)",
                savePackage: "Save package",
                editTier: "Edit sub-package",
                newTierTitle: "New sub-package",
                tierLead: "The VAT-inclusive price is calculated automatically (15%).",
                exclField: "Price excl. VAT",
                costField: "Supplier cost",
                saveTier: "Save sub-package",
                itemsOf: "Items of",
                itemsLead: "All item fields are required: category, names, product, details, quantity and unit. The unit price is used when the customer requests extra quantity.",
                itemsRequired: "Fill every field in each item before saving.",
                category: "Category",
                itemAr: "Item name (AR)",
                itemEn: "Item name (EN)",
                product: "Product / model",
                detailAr: "Details (Arabic)",
                detailEn: "Details (English)",
                qty: "Quantity",
                unitAr: "Unit (AR)",
                unitEn: "Unit (EN)",
                extraPrice: "Extra unit price",
                swap: "Customer may swap with an alternative",
                duplicate: "Duplicate",
                addItem: "Add item",
                saveItems: "Save items",
            } : {
                title: "إدارة الباقات",
                lead: "أنشئ الباقة الرئيسية، ثم باقاتها الفرعية، ثم عناصر كل باقة فرعية بأعدادها ومنتجاتها.",
                newPackage: "باقة جديدة",
                active: "مفعّلة",
                disabled: "معطّلة",
                subs: "الباقات الفرعية",
                newTier: "باقة فرعية",
                noTiers: "لا توجد باقات فرعية بعد.",
                excl: "قبل الضريبة",
                incl: "شامل الضريبة",
                cost: "تكلفة المورد",
                items: "العناصر",
                edit: "تعديل",
                copyFrom: "نسخ العناصر من…",
                remove: "حذف",
                orders: "الطلبات",
                editPackage: "تعديل الباقة",
                disable: "تعطيل",
                enable: "تفعيل",
                packageLead: "الأسعار والعناصر تُدار داخل الباقات الفرعية.",
                code: "الرمز",
                sort: "الترتيب",
                visible: "الباقة مفعّلة ومعروضة للعملاء",
                gallery: "معرض صور الموقع",
                galleryDrop: "اسحب الصور هنا أو اضغط للاختيار",
                galleryHint: "هذه الصور تظهر في صفحة الباقة. JPG و PNG و WEBP وما شابه، عدة ملفات.",
                captionAr: "وصف الصورة (عربي)",
                captionEn: "وصف الصورة (إنجليزي)",
                savePackage: "حفظ الباقة",
                editTier: "تعديل الباقة الفرعية",
                newTierTitle: "باقة فرعية جديدة",
                tierLead: "السعر شامل الضريبة يُحتسب تلقائياً (15%).",
                exclField: "السعر قبل الضريبة",
                costField: "تكلفة المورد",
                saveTier: "حفظ الباقة الفرعية",
                itemsOf: "عناصر",
                itemsLead: "جميع حقول العنصر إلزامية: الفئة، الاسم، المنتج، التفاصيل، العدد، ووحدة القياس. سعر الوحدة الإضافية يُستخدم عند طلب العميل زيادة الكمية.",
                itemsRequired: "أكمل كل الحقول في كل عنصر قبل الحفظ.",
                category: "الفئة",
                itemAr: "اسم العنصر (عربي)",
                itemEn: "اسم العنصر (إنجليزي)",
                product: "المنتج / الموديل",
                detailAr: "التفاصيل (عربي)",
                detailEn: "التفاصيل (إنجليزي)",
                qty: "العدد",
                unitAr: "وحدة القياس (عربي)",
                unitEn: "وحدة القياس (إنجليزي)",
                extraPrice: "سعر الوحدة الإضافية",
                swap: "يسمح للعميل باستبداله ببديل",
                duplicate: "تكرار",
                addItem: "إضافة عنصر",
                saveItems: "حفظ العناصر",
            };
        },
    },
    watch: {
        locale: {
            immediate: true,
            handler() {
                document.title = this.locale === "ar" ? "إدارة الباقات | FORMA BY AZURE" : "Package management | FORMA BY AZURE";
            },
        },
    },
    mounted() {
        this.load();
    },
    methods: {
        blankItem,
        nameOf(row) {
            return this.locale === "ar" ? row.name_ar : row.name_en;
        },
        lineOf(row) {
            return this.locale === "ar" ? row.tagline_ar : row.tagline_en;
        },
        descOf(row) {
            return this.locale === "ar" ? row.description_ar : row.description_en;
        },
        money(value) {
            return Number(value || 0).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        fail(error) {
            return error.response?.data?.message || (this.locale === "ar" ? "تعذر الحفظ" : "Could not save");
        },
        async load() {
            this.loading = true;
            try {
                const [packages, cats] = await Promise.all([
                    axios.get("KaheelPackages/getAllItems"),
                    axios.get("KaheelCategories/getAllItems"),
                ]);
                this.rows = packages.data.items || [];
                if (cats.data.items?.length) this.categories = cats.data.items;
            } finally {
                this.loading = false;
            }
        },
        openPackage(pkg) {
            this.formError = "";
            this.galleryQueue.forEach((item) => item.preview && URL.revokeObjectURL(item.preview));
            this.galleryQueue = [];
            this.packageImages = pkg ? (pkg.images || []).map((image) => ({ ...image })) : [];
            this.packageForm = pkg ? {
                id: pkg.id, code: pkg.code, name_ar: pkg.name_ar, name_en: pkg.name_en,
                tagline_ar: pkg.tagline_ar || "", tagline_en: pkg.tagline_en || "",
                sort_order: pkg.sort_order, is_active: !!pkg.is_active,
            } : { code: "", name_ar: "", name_en: "", tagline_ar: "", tagline_en: "", sort_order: this.rows.length + 1, is_active: true };
            this.packageDialog = true;
        },
        onGalleryDrop(event) {
            this.galleryOver = false;
            this.queueGallery({ target: { files: event.dataTransfer?.files || [], value: "" } });
        },
        queueGallery(event) {
            const selected = Array.from(event.target.files || []);
            event.target.value = "";
            const max = 8 * 1024 * 1024;
            const extra = selected.filter((file) => file.type.startsWith("image/") && file.type !== "image/svg+xml" && file.size <= max).map((file) => ({
                id: file.name + ":" + file.size + ":" + file.lastModified + ":" + Math.random(),
                file,
                preview: URL.createObjectURL(file),
                caption_ar: "",
                caption_en: "",
            }));
            this.galleryQueue = this.galleryQueue.concat(extra).slice(0, 20);
        },
        removeQueued(index) {
            const item = this.galleryQueue[index];
            if (item?.preview) URL.revokeObjectURL(item.preview);
            this.galleryQueue = this.galleryQueue.filter((_, i) => i !== index);
        },
        openTier(pkg, tier) {
            this.formError = "";
            this.tierPackageId = pkg.id;
            this.tierForm = tier ? {
                id: tier.id, name_ar: tier.name_ar, name_en: tier.name_en,
                description_ar: tier.description_ar || "", description_en: tier.description_en || "",
                price_excl_vat: tier.price_excl_vat, supplier_cost: tier.supplier_cost,
                sort_order: tier.sort_order, is_active: !!tier.is_active,
            } : { name_ar: "", name_en: "", description_ar: "", description_en: "", price_excl_vat: 0, supplier_cost: 0, sort_order: pkg.tiers.length + 1, is_active: true };
            this.tierDialog = true;
        },
        openItems(pkg, tier) {
            this.formError = "";
            this.tierPackageId = pkg.id;
            this.itemsTier = tier;
            this.itemRows = tier.items.map((item) => ({ ...item }));
            this.itemsDialog = true;
        },
        duplicateItem(index) {
            const copy = { ...this.itemRows[index] };
            delete copy.id;
            this.itemRows.splice(index + 1, 0, copy);
        },
        async savePackage() {
            this.saving = true;
            this.formError = "";
            try {
                const body = new FormData();
                if (this.packageForm.id) body.append("id", this.packageForm.id);
                body.append("code", this.packageForm.code || "");
                body.append("name_ar", this.packageForm.name_ar || "");
                body.append("name_en", this.packageForm.name_en || "");
                body.append("tagline_ar", this.packageForm.tagline_ar || "");
                body.append("tagline_en", this.packageForm.tagline_en || "");
                body.append("sort_order", this.packageForm.sort_order ?? 0);
                body.append("is_active", this.packageForm.is_active ? "1" : "0");
                body.append("sync_images", "1");
                this.packageImages.forEach((image) => {
                    body.append("keep_ids[]", image.id);
                    body.append("caption_ar[" + image.id + "]", image.caption_ar || "");
                    body.append("caption_en[" + image.id + "]", image.caption_en || "");
                });
                this.galleryQueue.forEach((item, index) => {
                    body.append("images[]", item.file);
                    body.append("new_caption_ar[" + index + "]", item.caption_ar || "");
                    body.append("new_caption_en[" + index + "]", item.caption_en || "");
                });
                await axios.post("KaheelPackages/savePackage", body);
                this.galleryQueue.forEach((item) => item.preview && URL.revokeObjectURL(item.preview));
                this.galleryQueue = [];
                this.packageDialog = false;
                await this.load();
            } catch (error) {
                this.formError = this.fail(error);
            } finally {
                this.saving = false;
            }
        },
        async saveTier() {
            this.saving = true;
            this.formError = "";
            try {
                await axios.post("KaheelPackages/saveTier", { ...this.tierForm, package_id: this.tierPackageId });
                this.tierDialog = false;
                await this.load();
            } catch (error) {
                this.formError = this.fail(error);
            } finally {
                this.saving = false;
            }
        },
        itemComplete(item) {
            const text = ["category", "name_ar", "name_en", "product_name_ar", "detail_ar", "detail_en", "unit_label_ar", "unit_label_en"];
            return text.every((key) => String(item[key] || "").trim() !== "")
                && Number(item.quantity) > 0
                && item.price_per_unit !== "" && item.price_per_unit !== null && Number(item.price_per_unit) >= 0;
        },
        async saveItems() {
            this.saving = true;
            this.formError = "";
            const items = this.itemRows.map((item) => ({
                ...item,
                product_name_en: String(item.product_name_en || item.product_name_ar || "").trim(),
            }));
            if (items.some((item) => !this.itemComplete(item))) {
                this.formError = this.t.itemsRequired;
                this.saving = false;
                return;
            }
            try {
                await axios.post("KaheelPackages/saveItems", {
                    tier_id: this.itemsTier.id,
                    package_id: this.tierPackageId,
                    items,
                });
                this.itemsDialog = false;
                await this.load();
            } catch (error) {
                this.formError = this.fail(error);
            } finally {
                this.saving = false;
            }
        },
        async togglePackage(pkg) {
            await axios.post("KaheelPackages/togglePackage", { id: pkg.id, is_active: !pkg.is_active });
            await this.load();
        },
        async removePackage(pkg) {
            if (!window.confirm(this.t.remove + " " + this.nameOf(pkg) + "?")) return;
            await axios.post("KaheelPackages/deletePackage", { id: pkg.id });
            await this.load();
        },
        async removeTier(tier) {
            if (!window.confirm(this.t.remove + " " + this.nameOf(tier) + "?")) return;
            await axios.post("KaheelPackages/deleteTier", { id: tier.id });
            await this.load();
        },
        async copyItems(event, tier) {
            const from = event.target.value;
            event.target.value = "";
            if (!from) return;
            await axios.post("KaheelPackages/copyItems", { from_tier_id: from, to_tier_id: tier.id });
            await this.load();
        },
    },
};
</script>
