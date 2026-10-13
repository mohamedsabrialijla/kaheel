<template>
    <div class="ka-orders">
        <template v-if="!orderId">
            <div class="ka-sup-head">
                <div>
                    <p class="ka-sup-kicker">ORDER OPERATIONS</p>
                    <h1>{{ t.title }}</h1>
                    <p class="ka-sup-lead">{{ t.lead }}</p>
                </div>
            </div>
            <div class="ka-ord-filters">
                <label class="ka-field ka-ord-search">
                    <span class="ka-sr">{{ t.search }}</span>
                    <input v-model="query" :placeholder="t.search">
                </label>
                <label class="ka-field">
                    <span class="ka-sr">{{ t.stage }}</span>
                    <select v-model="stage">
                        <option value="all">{{ t.allStages }}</option>
                        <option v-for="item in stages" :key="item.code" :value="item.code">{{ label(item) }}</option>
                    </select>
                </label>
            </div>
            <div class="ka-table-wrap ka-ord-table">
                <table class="ka-table wide">
                    <thead>
                        <tr>
                            <th>{{ t.order }}</th>
                            <th>{{ t.customer }}</th>
                            <th>{{ t.amount }}</th>
                            <th>{{ t.stage }}</th>
                            <th>{{ t.progress }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading"><td class="ka-empty" colspan="6">…</td></tr>
                        <tr v-else-if="!filtered.length"><td class="ka-empty" colspan="6">{{ t.empty }}</td></tr>
                        <tr v-for="row in filtered" :key="row.id">
                            <td>
                                <p class="ka-ord-strong">{{ row.number }}</p>
                                <p class="ka-sup-muted">{{ date(row.created_at) }}</p>
                            </td>
                            <td>
                                <p class="ka-ord-strong">{{ row.customer_name }}</p>
                                <p class="ka-sup-muted">{{ row.unit_no }} · {{ row.compound_name }}</p>
                            </td>
                            <td class="ka-ord-strong">{{ money(row.amount) }} <span class="ka-sup-muted">SAR</span></td>
                            <td><span class="ka-ord-badge" :class="'is-' + row.status">{{ locale === 'en' ? row.status_en : row.status_ar }}</span></td>
                            <td><progress-track :percent="row.percent" :step="row.step" :label="t.progress"></progress-track></td>
                            <td><button type="button" class="ka-btn ka-btn-outline" @click="open(row.id)">{{ t.details }}</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template v-else-if="order">
            <div class="ka-ord-hero">
                <div>
                    <button type="button" class="ka-btn ka-btn-ghost" @click="back">{{ t.all }}</button>
                    <h1>{{ order.number }}</h1>
                    <p class="ka-sup-lead">{{ order.customer_name }} · {{ order.unit_no }} · {{ order.compound_name }}</p>
                </div>
                <span class="ka-ord-badge" :class="'is-' + order.status">{{ locale === 'en' ? order.status_en : order.status_ar }}</span>
            </div>
            <section class="ka-ord-card">
                <progress-track :percent="order.percent" :step="order.step" :label="t.progress" wide></progress-track>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.info }}</h2>
                <div class="ka-ord-grid">
                    <div><p>{{ t.customerName }}</p><strong>{{ order.customer_name }}</strong></div>
                    <div><p>{{ t.username }}</p><strong>{{ order.username }}</strong></div>
                    <div><p>{{ t.project }}</p><strong>{{ order.compound_name }}</strong></div>
                    <div><p>{{ t.unit }}</p><strong>{{ order.unit_no }}<template v-if="order.area"> · {{ order.area }} m²</template></strong></div>
                    <div><p>{{ t.bedrooms }}</p><strong>{{ order.bedrooms || '—' }}</strong></div>
                    <div><p>{{ t.package }}</p><strong>{{ locale === 'en' ? order.package_en : order.package_ar }}</strong></div>
                    <div><p>{{ t.tier }}</p><strong>{{ locale === 'en' ? order.tier_en : order.tier_ar }}</strong></div>
                    <div><p>{{ t.created }}</p><strong>{{ date(order.created_at) }}</strong></div>
                    <div><p>{{ t.designStatus }}</p><strong>{{ designLabel }}</strong></div>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.financials }}</h2>
                <div class="ka-ord-grid">
                    <div><p>{{ t.excl }}</p><strong>{{ money(order.price_excl) }} SAR</strong></div>
                    <div><p>{{ t.vat }}</p><strong>{{ money(order.vat) }} SAR</strong></div>
                    <div><p>{{ t.packageTotal }}</p><strong>{{ money(order.price_incl) }} SAR</strong></div>
                    <div><p>{{ t.addons }}</p><strong>{{ money(order.addons_total) }} SAR</strong></div>
                    <div><p>{{ t.finalTotal }}</p><strong>{{ money(order.final_total) }} SAR</strong></div>
                    <div><p>{{ t.deposit }}</p><strong>{{ money(order.deposit_amount) }} SAR · {{ order.deposit_paid ? t.paidState : t.unpaid }}</strong></div>
                    <div><p>{{ t.paid }}</p><strong>{{ money(order.paid) }} SAR</strong></div>
                    <div><p>{{ t.remaining }}</p><strong>{{ money(order.remaining) }} SAR</strong></div>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.paymentSection }}</h2>
                <p v-if="!order.payment_method" class="ka-sup-muted">{{ t.noPayments }}</p>
                <template v-else>
                    <div class="ka-ord-grid">
                        <div><p>{{ t.method }}</p><strong>{{ methodLabel }}</strong></div>
                        <div><p>{{ t.amount }}</p><strong>{{ money(order.deposit_amount) }} SAR</strong></div>
                        <div><p>{{ t.payState }}</p><strong>{{ payStateLabel }}</strong></div>
                    </div>
                    <a v-if="latestReceipt" class="ka-ord-file" :href="latestReceipt.url" target="_blank" rel="noopener noreferrer">{{ t.viewReceipt }} — {{ latestReceipt.name }}</a>
                    <p v-if="order.payment_status === 'approved'" class="ka-ord-ok">{{ t.paymentOk }}</p>
                    <p v-if="order.payment_note" class="ka-ord-error">{{ order.payment_note }}</p>
                    <div v-if="order.status === 'final_payment' && order.final_payment_status !== 'approved'" class="ka-ord-pay-actions">
                        <p class="ka-sup-muted">{{ order.final_payment_status === 'pending' ? t.finalPending : t.finalWait }}</p>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="confirmFinal">{{ t.confirmFinal }}</button>
                    </div>
                    <p v-if="order.final_payment_status === 'approved'" class="ka-ord-ok">{{ t.finalOk }}</p>
                    <div v-if="order.payment_status === 'pending'" class="ka-ord-pay-actions">
                        <label class="ka-field"><span>{{ t.rejectNote }}</span><textarea v-model="paymentNote" rows="3" :placeholder="t.rejectPlaceholder"></textarea></label>
                        <div class="ka-ord-actions">
                            <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="reviewPayment('approved')">{{ t.approvePay }}</button>
                            <button type="button" class="ka-btn ka-btn-outline" :disabled="saving || !paymentNote.trim()" @click="reviewPayment('rejected')">{{ t.rejectPay }}</button>
                        </div>
                    </div>
                </template>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.customerPlan }}</h2>
                <p v-if="!plans.length" class="ka-sup-muted">{{ t.noPlan }}</p>
                <a v-for="file in plans" :key="'plan-' + file.id" class="ka-ord-file" :href="file.url" target="_blank" rel="noopener noreferrer">{{ file.name }}</a>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.workspace }}</h2>
                <p class="ka-ord-hint">{{ hint }}</p>
                <p v-if="!canOperate && order.payment_method" class="ka-ord-hint">{{ t.confirmFirst }}</p>

                <h3>{{ t.designFiles }}</h3>
                <p v-if="!designFiles.length" class="ka-sup-muted">{{ t.none }}</p>
                <a v-for="file in designFiles" :key="file.id" class="ka-ord-file" :href="file.url" target="_blank" rel="noopener noreferrer">{{ file.name }}</a>
                <h3>{{ t.otherDocs }}</h3>
                <button type="button" class="ka-btn ka-btn-outline" @click="contractOpen = !contractOpen">{{ t.contract }}</button>
                <pre v-if="contractOpen" class="ka-ord-contract">{{ order.contract_body }}</pre>
                <a v-for="file in plans" :key="file.id" class="ka-ord-file" :href="file.url" target="_blank" rel="noopener noreferrer">{{ file.name }}</a>
                <a v-for="file in handovers" :key="'h' + file.id" class="ka-ord-file" :href="file.url" target="_blank" rel="noopener noreferrer">{{ file.name }}</a>

                <div v-if="canUpload && canOperate" class="ka-ord-uploads">
                    <label class="ka-field">
                        <span>{{ t.uploadDesign }}</span>
                        <input type="file" accept="image/*,application/pdf" multiple @change="upload($event, 'design')">
                        <span class="ka-sup-muted">{{ t.designLimit }}</span>
                    </label>
                    <label class="ka-field">
                        <span>{{ t.uploadHandover }}</span>
                        <input type="file" accept="image/*,application/pdf" @change="upload($event, 'handover')">
                    </label>
                </div>

                <h3>{{ t.components }}</h3>
                <p v-if="!order.components.length" class="ka-sup-muted">{{ t.none }}</p>
                <div v-for="item in order.components" :key="item.id" class="ka-ord-block">
                    <div class="ka-ord-line">
                        <strong>{{ locale === 'en' ? item.name_en : item.name_ar }}</strong>
                        <span class="ka-sup-muted">{{ item.base_qty }} {{ unitOf(item) }} · {{ money(item.price_per_unit) }} SAR/{{ unitOf(item) }}</span>
                    </div>
                    <p v-if="Number(item.extra_cost) > 0" class="ka-ord-extra">{{ t.extra }}: +{{ money(item.extra_cost) }} SAR</p>
                </div>

                <div v-if="order.status === 'design_preparation'" class="ka-ord-editor">
                    <article v-for="(item, index) in drafts" :key="index" class="ka-comp-card">
                        <div class="ka-comp-grid">
                            <label class="ka-field"><span>{{ t.category }}</span>
                                <select :value="item.category" @change="setCategory(item, $event.target.value)">
                                    <option v-for="cat in categories" :key="cat.code" :value="cat.code">{{ locale === 'en' ? cat.en : cat.ar }}</option>
                                </select>
                            </label>
                            <span class="ka-comp-spacer"></span>
                            <label class="ka-field"><span>{{ t.nameAr }}</span><input v-model="item.name_ar"></label>
                            <label class="ka-field"><span>English name</span><input v-model="item.name_en"></label>
                            <label class="ka-field"><span>{{ t.unitAr }}</span><input v-model="item.unit_label_ar"></label>
                            <label class="ka-field"><span>{{ t.unitEn }}</span><input v-model="item.unit_label_en"></label>
                            <label class="ka-field"><span>{{ t.qty }}</span><input v-model.number="item.base_qty" type="number" min="0"></label>
                            <label class="ka-field"><span>{{ t.unitPrice }}</span><input v-model.number="item.price_per_unit" type="number" min="0"></label>
                        </div>
                        <div class="ka-comp-alts">
                            <p class="ka-comp-alts-title">{{ t.altsTitle }} <span class="ka-sup-muted">{{ t.categoryOf }}: {{ categoryName(item.category) }}</span></p>
                            <div v-for="(option, optionIndex) in item.options" :key="optionIndex" class="ka-comp-alt">
                                <label class="ka-field"><span>{{ t.altAr }}</span><input v-model="option.name_ar"></label>
                                <label class="ka-field"><span>Alternative</span><input v-model="option.name_en"></label>
                                <label class="ka-field"><span>{{ t.delta }}</span><input v-model.number="option.price_delta" type="number"></label>
                                <button type="button" class="ka-btn ka-btn-ghost" @click="item.options.splice(optionIndex, 1)">{{ t.remove }}</button>
                            </div>
                            <div class="ka-ord-actions">
                                <select class="ka-ord-select" @change="insertAlt($event, item)">
                                    <option value="">{{ t.insertAlt }}</option>
                                    <option v-for="alt in altsFor(item.category)" :key="alt.id" :value="alt.id">{{ (locale === 'en' ? alt.name_en : alt.name_ar) + ' (' + deltaText(alt.price_delta) + ')' }}</option>
                                </select>
                                <button type="button" class="ka-btn ka-btn-outline" @click="item.options.push({ name_ar: '', name_en: '', price_delta: 0 })">{{ t.manualAlt }}</button>
                                <button type="button" class="ka-btn ka-btn-ghost" @click="drafts.splice(index, 1)">{{ t.removeComponent }}</button>
                            </div>
                            <p v-if="!altsFor(item.category).length" class="ka-sup-muted">{{ t.noAlts }}</p>
                        </div>
                    </article>
                    <div class="ka-ord-actions">
                        <button type="button" class="ka-btn ka-btn-outline" @click="loadItems">{{ t.loadItems }}</button>
                        <button type="button" class="ka-btn ka-btn-outline" @click="addDraft">{{ t.addComponent }}</button>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="!drafts.length || saving" @click="saveComponents">{{ t.saveComponents }}</button>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving || !order.design_ready" @click="sendDesign">{{ t.sendDesign }}</button>
                    </div>
                </div>

                <h3>{{ t.suppliers }}</h3>
                <p v-if="!order.assignments.length" class="ka-sup-muted">{{ t.none }}</p>
                <div v-for="row in order.assignments" :key="row.id" class="ka-ord-block">
                    <div class="ka-ord-line">
                        <strong>{{ locale === 'en' ? row.supplier_name_en : row.supplier_name_ar }}</strong>
                        <span class="ka-sup-muted">{{ locale === 'en' ? row.scope_en : row.scope_ar }}</span>
                        <span>{{ money(row.cost) }} SAR</span>
                    </div>
                    <p class="ka-sup-muted">{{ t.expected }}: {{ row.expected_date || '—' }}<template v-if="row.delivered_at"> · {{ t.delivered }}: {{ row.delivered_at }}</template></p>
                    <label v-if="order.status === 'in_supply' && !row.delivered_at" class="ka-field ka-ord-date">
                        <span>{{ t.delivered }}</span>
                        <input type="date" @change="markDelivered(row.id, $event.target.value)">
                    </label>
                </div>
                <div v-if="canAssign" class="ka-ord-editor">
                    <article v-for="(row, index) in supplierDrafts" :key="index" class="ka-ord-form">
                        <label class="ka-field"><span>{{ t.supplier }}</span>
                            <select v-model="row.supplier_id">
                                <option value="">{{ t.pickSupplier }}</option>
                                <option v-for="supplier in order.suppliers" :key="supplier.id" :value="supplier.id">{{ locale === 'en' ? supplier.name_en : supplier.name_ar }}</option>
                            </select>
                        </label>
                        <label class="ka-field"><span>{{ t.scope }}</span><input v-model="row.scope_ar"></label>
                        <label class="ka-field"><span>{{ t.cost }}</span><input v-model.number="row.cost" type="number" min="0"></label>
                        <label class="ka-field"><span>{{ t.expected }}</span><input v-model="row.expected_date" type="date"></label>
                    </article>
                    <div class="ka-ord-actions">
                        <button type="button" class="ka-btn ka-btn-outline" @click="supplierDrafts.push({ supplier_id: '', scope_ar: '', cost: 0, expected_date: '' })">{{ t.addSupplier }}</button>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="assignSuppliers">{{ t.assign }}</button>
                    </div>
                </div>

                <div class="ka-ord-thread">
                    <h3>{{ t.thread }}</h3>
                    <p v-if="!order.messages.length" class="ka-sup-muted">{{ t.noMessages }}</p>
                    <article v-for="message in order.messages" :key="message.id" class="ka-ord-msg" :class="{ 'is-admin': message.role === 'admin' }">
                        <p class="ka-sup-muted">{{ message.name || t.admin }} · {{ date(message.created_at) }}</p>
                        <p>{{ message.body }}</p>
                    </article>
                    <p v-if="threadClosed" class="ka-ord-hint">{{ t.threadClosed }}</p>
                    <template v-else-if="canOperate && order.status !== 'deposit_pending'">
                        <label class="ka-field"><span>{{ t.note }}</span><textarea v-model="messageBody" rows="3" :placeholder="t.write"></textarea></label>
                        <div class="ka-ord-actions">
                            <button type="button" class="ka-btn ka-btn-primary" :disabled="saving || !messageBody.trim()" @click="sendMessage">{{ t.send }}</button>
                            <button v-if="order.design_status === 'rejected'" type="button" class="ka-btn ka-btn-outline" :disabled="saving || !order.design_ready" @click="sendDesign">{{ t.resubmit }}</button>
                        </div>
                    </template>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.history }}</h2>
                <p v-if="!order.events.length" class="ka-sup-muted">{{ t.noHistory }}</p>
                <article v-for="event in order.events" :key="event.id" class="ka-ord-block">
                    <div class="ka-ord-line">
                        <strong>{{ stageName(event.from_status) }} → {{ stageName(event.to_status) }}</strong>
                        <span class="ka-sup-muted">{{ event.actor_name || '—' }} · {{ date(event.created_at) }}</span>
                    </div>
                    <p v-if="event.note" class="ka-sup-muted">{{ event.note }}</p>
                </article>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.advance }}</h2>
                <template v-if="order.next_status">
                    <label class="ka-field"><span>{{ t.updateNote }}</span><textarea v-model="advanceNote" rows="3"></textarea></label>
                    <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="advance">{{ t.moveTo }} {{ locale === 'en' ? order.next_en : order.next_ar }}</button>
                </template>
                <p v-else-if="order.gate_ar" class="ka-sup-muted">{{ locale === 'en' ? order.gate_en : order.gate_ar }}</p>
                <p v-else class="ka-sup-muted">{{ order.status === 'deposit_pending' ? t.waitDeposit : t.complete }}</p>
            </section>
        </template>
        <p v-else-if="loading" class="ka-sup-muted">…</p>
        <p v-if="error" class="ka-ord-error">{{ error }}</p>
    </div>
</template>

<script>
const stages = [
    { code: "deposit_pending", ar: "بانتظار العربون", en: "Awaiting deposit" },
    { code: "admin_review", ar: "قيد مراجعة الإدارة", en: "Under review" },
    { code: "design_preparation", ar: "قيد إعداد التصميم", en: "Design in preparation" },
    { code: "design_review", ar: "بانتظار مراجعة العميل", en: "Awaiting your review" },
    { code: "final_payment", ar: "بانتظار السداد النهائي", en: "Awaiting final payment" },
    { code: "final_approval", ar: "الموافقة النهائية", en: "Final approval" },
    { code: "in_supply", ar: "قيد التنفيذ والتوريد", en: "In production" },
    { code: "supplied", ar: "تم التوريد", en: "Supplied" },
    { code: "closed", ar: "مغلق", en: "Closed" },
];

const categories = [
    { code: "furniture", ar: "الأثاث", en: "Furniture", unitAr: "قطعة", unitEn: "piece" },
    { code: "kitchen", ar: "المطبخ", en: "Kitchen", unitAr: "متر طولي", unitEn: "linear meter" },
    { code: "wardrobe", ar: "الدواليب", en: "Wardrobes", unitAr: "متر طولي", unitEn: "linear meter" },
    { code: "appliances", ar: "الأجهزة", en: "Appliances", unitAr: "جهاز", unitEn: "unit" },
    { code: "tv", ar: "الشاشات والتلفزيون", en: "TV & screens", unitAr: "جهاز", unitEn: "unit" },
    { code: "curtains", ar: "الستائر", en: "Curtains", unitAr: "متر", unitEn: "meter" },
    { code: "accessories", ar: "الإكسسوارات", en: "Accessories", unitAr: "قطعة", unitEn: "piece" },
    { code: "soft", ar: "المفروشات", en: "Soft furnishings", unitAr: "قطعة", unitEn: "piece" },
    { code: "lighting", ar: "الإنارة", en: "Lighting", unitAr: "نقطة", unitEn: "point" },
    { code: "general", ar: "عام", en: "General", unitAr: "قطعة", unitEn: "piece" },
];

const blankComponent = () => ({
    category: "furniture",
    name_ar: "",
    name_en: "",
    unit_label_ar: "قطعة",
    unit_label_en: "unit",
    base_qty: 1,
    price_per_unit: 0,
    extra_cost: 0,
    options: [],
});

const ProgressTrack = {
    props: ["percent", "step", "label", "wide"],
    template: `
        <div class="ka-progress" :class="{ 'is-wide': wide }">
            <div class="ka-progress-top"><span>{{ label }}</span><strong>{{ percent }}%</strong></div>
            <div class="ka-track"><div class="ka-track-fill" :style="{ width: percent + '%' }"></div><span v-for="index in 8" :key="index" class="ka-dot" :class="{ 'is-on': index <= step }"></span></div>
        </div>
    `,
};

export default {
    components: { ProgressTrack },
    data() {
        return {
            rows: [],
            order: null,
            query: "",
            stage: "all",
            stages,
            categories,
            loading: true,
            saving: false,
            error: "",
            drafts: [],
            supplierDrafts: [],
            messageBody: "",
            advanceNote: "",
            paymentNote: "",
            contractOpen: false,
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        orderId() {
            return this.$route.params.id || null;
        },
        t() {
            const en = this.locale === "en";
            return en ? {
                title: "Order management",
                lead: "Review orders and plans, prepare designs and pricing, then track suppliers until closing.",
                search: "Search order, customer or unit",
                allStages: "All stages",
                order: "Order",
                customer: "Customer / unit",
                amount: "Amount",
                stage: "Stage",
                progress: "Progress",
                details: "Details",
                empty: "No orders yet.",
                all: "All orders",
                info: "Order information",
                customerName: "Customer",
                username: "Username",
                project: "Project",
                unit: "Unit",
                bedrooms: "Bedrooms",
                package: "Package",
                tier: "Sub-package",
                created: "Created",
                designStatus: "Design status",
                financials: "Financials",
                excl: "Excl. VAT",
                vat: "VAT",
                packageTotal: "Package total",
                addons: "Add-ons",
                finalTotal: "Final total",
                deposit: "Deposit",
                paidState: "paid",
                unpaid: "unpaid",
                paid: "Paid",
                remaining: "Remaining",
                payments: "Payments",
                noPayments: "No payment yet.",
                paymentSection: "Payment",
                method: "Method",
                payState: "Payment status",
                viewReceipt: "View receipt",
                paymentOk: "Payment confirmed. You can continue the rest of the order.",
                rejectNote: "Rejection note for the customer",
                rejectPlaceholder: "Write why the payment was rejected",
                approvePay: "Confirm payment",
                rejectPay: "Reject payment",
                customerPlan: "Plan uploaded by the customer",
                noPlan: "The customer has not uploaded a plan.",
                confirmFirst: "Confirm the payment before the rest of the workflow.",
                finalWait: "Confirm the final payment was received before the order can move on.",
                finalPending: "The customer sent the final payment. Confirm it to continue.",
                confirmFinal: "Confirm final payment",
                finalOk: "Final payment confirmed.",
                workspace: "Design, documents and suppliers",
                designFiles: "Design files and versions",
                otherDocs: "Other order documents",
                contract: "Signed furnishing agreement",
                uploadDesign: "Upload the design (images or PDF)",
                designLimit: "Several files are allowed. Each file can be up to 25 MB.",
                uploadHandover: "Upload handover report",
                components: "Design components and pricing",
                extra: "Customer change",
                category: "Category",
                categoryOf: "Category",
                nameAr: "Arabic name",
                unitAr: "Unit label",
                unitEn: "Unit label (English)",
                qty: "Base quantity",
                unitPrice: "Price per extra unit",
                altsTitle: "Alternatives shown to the customer",
                noAlts: "No active alternatives for this category — add them in the Alternatives page.",
                extraCost: "Extra cost",
                altAr: "Alternative (Arabic)",
                delta: "Price delta",
                remove: "Remove",
                insertAlt: "Insert from library",
                manualAlt: "Manual alternative",
                removeComponent: "Remove component",
                loadItems: "Load sub-package items",
                addComponent: "Add component",
                saveComponents: "Save components",
                sendDesign: "Send design to customer",
                suppliers: "Suppliers and delivery",
                expected: "Expected",
                delivered: "Delivered",
                supplier: "Supplier",
                pickSupplier: "Select supplier",
                scope: "Scope",
                cost: "Cost",
                addSupplier: "Add supplier",
                assign: "Assign and notify",
                thread: "Order discussion",
                noMessages: "No messages yet.",
                threadClosed: "Discussion is closed. The log stays available.",
                note: "Note",
                write: "Write your note…",
                send: "Send",
                resubmit: "Resubmit design",
                history: "Stage history",
                noHistory: "No history yet.",
                advance: "Advance stage",
                updateNote: "Update note",
                moveTo: "Move to:",
                waitDeposit: "Waiting for the customer deposit. The stage cannot move before payment.",
                complete: "This order workflow is complete.",
                none: "None yet.",
                admin: "Admin",
            } : {
                title: "إدارة الطلبات",
                lead: "راجع الطلب والمخطط، جهّز التصميم وأسعاره، تابع الموردين والتوريد حتى الإغلاق.",
                search: "ابحث برقم الطلب أو العميل أو الوحدة",
                allStages: "كل المراحل",
                order: "الطلب",
                customer: "العميل / الوحدة",
                amount: "القيمة",
                stage: "المرحلة",
                progress: "تقدم الطلب",
                details: "التفاصيل",
                empty: "لا توجد طلبات بعد.",
                all: "كل الطلبات",
                info: "بيانات الطلب",
                customerName: "العميل",
                username: "اسم المستخدم",
                project: "المشروع",
                unit: "الوحدة",
                bedrooms: "عدد الغرف",
                package: "الباقة",
                tier: "الباقة الفرعية",
                created: "تاريخ الإنشاء",
                designStatus: "حالة التصميم",
                financials: "الملخص المالي",
                excl: "قبل الضريبة",
                vat: "الضريبة",
                packageTotal: "قيمة الباقة",
                addons: "التعديلات",
                finalTotal: "الإجمالي النهائي",
                deposit: "العربون",
                paidState: "مدفوع",
                unpaid: "غير مدفوع",
                paid: "المدفوع",
                remaining: "المتبقي",
                payments: "الإيصالات",
                noPayments: "لا توجد عملية دفع بعد.",
                paymentSection: "الدفع",
                method: "طريقة الدفع",
                payState: "حالة الدفع",
                viewReceipt: "عرض الوصل",
                paymentOk: "تم تأكيد الدفع. يمكنك متابعة باقي الإجراءات.",
                rejectNote: "ملاحظة الرفض وتظهر للعميل",
                rejectPlaceholder: "اكتب سبب رفض الدفع",
                approvePay: "تأكيد الدفع",
                rejectPay: "رفض الدفع",
                customerPlan: "المخطط الذي رفعه العميل",
                noPlan: "لم يرفع العميل مخططاً.",
                confirmFirst: "أكّد الدفع أولاً قبل متابعة باقي الإجراءات.",
                finalWait: "أكّد استلام السداد النهائي قبل نقل الطلب.",
                finalPending: "العميل أرسل السداد النهائي. أكّده للمتابعة.",
                confirmFinal: "تأكيد السداد النهائي",
                finalOk: "تم تأكيد السداد النهائي.",
                workspace: "التصميم والمستندات والموردون",
                designFiles: "ملفات التصميم ونسخه",
                otherDocs: "مستندات الطلب الأخرى",
                contract: "عقد خدمات التأثيث الموقّع",
                uploadDesign: "رفع التصميم (صورة أو PDF)",
                designLimit: "يمكن رفع عدة صور أو ملفات، وحتى 25 ميغابايت لكل ملف.",
                uploadHandover: "رفع محضر الاستلام",
                components: "مكوّنات التصميم وتسعيرها",
                extra: "تعديل العميل",
                category: "التصنيف",
                categoryOf: "التصنيف",
                nameAr: "الاسم بالعربية",
                unitAr: "الوحدة (متر/قطعة)",
                unitEn: "الوحدة بالإنجليزية",
                qty: "الكمية الأساسية",
                unitPrice: "سعر الوحدة الإضافية",
                altsTitle: "البدائل المعروضة للعميل",
                noAlts: "لا توجد بدائل مفعّلة لهذا التصنيف في المكتبة — أضفها من صفحة «البدائل والأسعار».",
                extraCost: "تكلفة إضافية",
                altAr: "بديل بالعربية",
                delta: "فرق السعر",
                remove: "حذف",
                insertAlt: "إدراج بديل من المكتبة",
                manualAlt: "بديل يدوي",
                removeComponent: "حذف المكوّن",
                loadItems: "تحميل عناصر الباقة الفرعية",
                addComponent: "إضافة مكوّن",
                saveComponents: "حفظ المكوّنات",
                sendDesign: "إرسال التصميم للعميل",
                suppliers: "الموردون والتوريد",
                expected: "المتوقع",
                delivered: "تاريخ التوريد",
                supplier: "المورد",
                pickSupplier: "اختر المورد",
                scope: "نطاق العمل",
                cost: "التكلفة",
                addSupplier: "إضافة مورد",
                assign: "إسناد وإرسال تنبيه",
                thread: "مناقشة الطلب",
                noMessages: "لا توجد رسائل بعد.",
                threadClosed: "أُغلقت المناقشة. السجل يبقى متاحاً للاطلاع.",
                note: "ملاحظة",
                write: "اكتب ملاحظتك على الطلب…",
                send: "إرسال",
                resubmit: "إعادة إرسال التصميم للاعتماد",
                history: "سجل المراحل",
                noHistory: "لا يوجد سجل بعد.",
                advance: "تحديث المرحلة",
                updateNote: "ملاحظة التحديث",
                moveTo: "نقل إلى:",
                waitDeposit: "بانتظار دفع العربون من العميل. لا يمكن نقل المرحلة قبل الدفع.",
                complete: "اكتملت دورة هذا الطلب.",
                none: "لا يوجد بعد.",
                admin: "الإدارة",
            };
        },
        filtered() {
            const q = this.query.trim().toLowerCase();
            return this.rows.filter((row) => {
                const stageOk = this.stage === "all" || row.status === this.stage;
                const text = `${row.number} ${row.customer_name} ${row.unit_no} ${row.compound_name}`.toLowerCase();
                return stageOk && (!q || text.includes(q));
            });
        },
        plans() {
            return (this.order?.files || []).filter((file) => file.kind === "plan" || !file.kind);
        },
        designFiles() {
            return (this.order?.files || []).filter((file) => file.kind === "design");
        },
        handovers() {
            return (this.order?.files || []).filter((file) => file.kind === "handover");
        },
        receipts() {
            return (this.order?.files || []).filter((file) => file.kind === "receipt");
        },
        canUpload() {
            return this.order && !["deposit_pending", "closed"].includes(this.order.status);
        },
        canOperate() {
            return this.order && this.order.payment_status === "approved";
        },
        latestReceipt() {
            const files = this.receipts;
            return files.length ? files[files.length - 1] : null;
        },
        methodLabel() {
            if (this.order?.payment_method === "visa") return this.locale === "en" ? "Visa" : "فيزا";
            if (this.order?.payment_method === "receipt") return this.locale === "en" ? "Receipt upload" : "رفع إيصال";
            return "—";
        },
        payStateLabel() {
            const map = {
                pending: this.locale === "en" ? "Waiting for confirmation" : "بانتظار التأكيد",
                approved: this.locale === "en" ? "Confirmed" : "تم التأكيد",
                rejected: this.locale === "en" ? "Rejected" : "مرفوض",
            };
            return map[this.order?.payment_status] || "—";
        },
        canAssign() {
            return this.order && ["final_approval", "in_supply"].includes(this.order.status);
        },
        threadClosed() {
            return this.order && (this.order.status === "closed" || this.order.design_status === "approved");
        },
        designLabel() {
            const map = {
                none: this.locale === "en" ? "Not sent" : "لم يُرسل",
                sent: this.locale === "en" ? "Awaiting customer approval" : "بانتظار اعتماد العميل",
                approved: this.locale === "en" ? "Design approved" : "التصميم معتمد",
                rejected: this.locale === "en" ? "Design rejected — awaiting revision" : "التصميم مرفوض — بانتظار التعديل",
            };
            return map[this.order?.design_status] || map.none;
        },
        hint() {
            const ar = {
                deposit_pending: "الطلب محفوظ وبانتظار العربون. تظهر هنا البيانات والمستندات فقط.",
                admin_review: "راجع الطلب والمخطط والإيصال، ثم انقل الطلب لإعداد التصميم.",
                design_preparation: "ارفع نسخة التصميم، حمّل عناصر الباقة، سعّرها ثم أرسلها للعميل.",
                design_review: "التصميم عند العميل. ناقشه هنا، وبعد الاعتماد انقل الطلب للسداد النهائي.",
                final_payment: "بانتظار السداد النهائي. المتبقي ظاهر في الملخص المالي.",
                final_approval: "أسند الموردين ونطاق عمل كل واحد، ثم انقل الطلب للتنفيذ.",
                in_supply: "تابع التوريد، سجّل تاريخ التسليم، وارفع محضر الاستلام.",
                supplied: "تم التوريد. راجع المحضر ثم أغلق الطلب.",
                closed: "الطلب مغلق. السجل والملفات تبقى للاطلاع.",
            };
            const en = {
                deposit_pending: "The order is saved and waiting for the deposit. Only its details and documents are shown.",
                admin_review: "Review the order, plan and receipt, then move it to design preparation.",
                design_preparation: "Upload a design version, load the sub-package items, price them, then send them to the customer.",
                design_review: "The design is with the customer. Discuss it here, then move the order to final payment after approval.",
                final_payment: "Waiting for the final payment. The balance is in the financial summary.",
                final_approval: "Assign suppliers and the scope of each, then move the order into production.",
                in_supply: "Track delivery, record the delivered date, and upload the handover report.",
                supplied: "The order has been supplied. Review the handover, then close it.",
                closed: "The order is closed. The log and files stay available.",
            };
            return (this.locale === "en" ? en : ar)[this.order?.status] || "";
        },
    },
    watch: {
        "$route.fullPath"() {
            this.load();
        },
    },
    created() {
        this.load();
    },
    methods: {
        label(item) {
            return this.locale === "en" ? item.en : item.ar;
        },
        stageName(code) {
            const item = stages.find((row) => row.code === code);
            if (!item) return "—";
            return this.locale === "en" ? item.en : item.ar;
        },
        money(value) {
            return Number(value || 0).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        date(value) {
            if (!value) return "—";
            return new Date(value).toLocaleDateString(this.locale === "en" ? "en-GB" : "ar-EG", { day: "numeric", month: "long", year: "numeric" });
        },
        open(id) {
            this.$router.push(`/${this.locale}/dashboard/orders/${id}`);
        },
        back() {
            this.$router.push(`/${this.locale}/dashboard/orders`);
        },
        async load() {
            this.loading = true;
            this.error = "";
            try {
                if (this.orderId) {
                    const response = await axios.get("KaheelOrders/show", { params: { id: this.orderId } });
                    this.order = response.data.items;
                    this.drafts = (this.order.components || []).map((item) => ({ ...item, options: item.options ? item.options.map((option) => ({ ...option })) : [] }));
                    this.supplierDrafts = [];
                } else {
                    const response = await axios.get("KaheelOrders/getAllItems");
                    this.rows = response.data.items || [];
                    this.order = null;
                }
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "Could not load orders." : "تعذر تحميل الطلبات.");
            } finally {
                this.loading = false;
            }
        },
        apply(response) {
            this.order = response.data.items;
            this.drafts = (this.order.components || []).map((item) => ({ ...item, options: item.options ? item.options.map((option) => ({ ...option })) : [] }));
        },
        async post(url, payload) {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.post(url, payload);
                if (response.data.status === false) {
                    this.error = response.data.message;
                    return;
                }
                this.apply(response);
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "The action failed." : "تعذر تنفيذ الإجراء.");
            } finally {
                this.saving = false;
            }
        },
        reviewPayment(decision) {
            this.post("KaheelOrders/reviewPayment", {
                id: this.order.id,
                decision,
                note: decision === "rejected" ? this.paymentNote.trim() : "",
            });
            if (decision === "rejected") this.paymentNote = "";
        },
        confirmFinal() {
            this.post("KaheelOrders/confirmFinal", { id: this.order.id });
        },
        advance() {
            this.post("KaheelOrders/advance", { id: this.order.id, to_status: this.order.next_status, note: this.advanceNote });
            this.advanceNote = "";
        },
        async loadItems() {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.get("KaheelOrders/packageItems", { params: { id: this.order.id } });
                const items = response.data.items || [];
                if (!items.length) {
                    this.error = this.locale === "en" ? "No items defined in this order's sub-package." : "لا توجد عناصر معرّفة في الباقة الفرعية لهذا الطلب.";
                    return;
                }
                this.drafts = items.map((item) => ({
                    ...blankComponent(),
                    ...item,
                    options: item.allow_alternatives ? this.altsFor(item.category).map((alt) => ({ name_ar: alt.name_ar, name_en: alt.name_en, price_delta: Number(alt.price_delta) })) : [],
                }));
            } catch (error) {
                this.error = error.response?.data?.message || "";
            } finally {
                this.saving = false;
            }
        },
        addDraft() {
            this.drafts.push(blankComponent());
        },
        unitOf(item) {
            return this.locale === "en" ? item.unit_label_en : item.unit_label_ar;
        },
        categoryName(code) {
            const item = categories.find((row) => row.code === code);
            if (!item) return code || "—";
            return this.locale === "en" ? item.en : item.ar;
        },
        setCategory(item, code) {
            item.category = code;
            const cat = categories.find((row) => row.code === code);
            if (!cat) return;
            item.unit_label_ar = cat.unitAr;
            item.unit_label_en = cat.unitEn;
        },
        deltaText(value) {
            const amount = Number(value || 0);
            const text = this.money(amount);
            return amount > 0 ? `+${text}` : text;
        },
        altsFor(category) {
            return (this.order?.alternatives || []).filter((alt) => alt.category === category);
        },
        insertAlt(event, item) {
            const alt = (this.order.alternatives || []).find((row) => String(row.id) === event.target.value);
            event.target.value = "";
            if (alt) item.options.push({ name_ar: alt.name_ar, name_en: alt.name_en, price_delta: Number(alt.price_delta) });
        },
        saveComponents() {
            this.post("KaheelOrders/saveComponents", { id: this.order.id, components: this.drafts });
        },
        sendDesign() {
            this.post("KaheelOrders/sendDesign", { id: this.order.id });
        },
        assignSuppliers() {
            const rows = this.supplierDrafts.filter((row) => row.supplier_id && row.scope_ar);
            if (!rows.length) return;
            this.post("KaheelOrders/assignSuppliers", { id: this.order.id, assignments: rows });
        },
        markDelivered(id, date) {
            if (date) this.post("KaheelOrders/markDelivered", { id: this.order.id, assignment_id: id, delivered_at: date });
        },
        sendMessage() {
            const body = this.messageBody.trim();
            if (!body) return;
            this.messageBody = "";
            this.post("KaheelOrders/message", { id: this.order.id, body });
        },
        async upload(event, kind) {
            const selected = Array.from(event.target.files || []).slice(0, 10);
            event.target.value = "";
            if (!selected.length) return;
            const max = 25 * 1024 * 1024;
            if (selected.some((file) => file.size > max)) {
                this.error = this.locale === "en" ? "Each file must be 25 MB or less." : "كل ملف يجب أن يكون 25 ميغابايت أو أقل.";
                return;
            }
            this.saving = true;
            this.error = "";
            try {
                for (const file of selected) {
                    const body = new FormData();
                    body.append("id", this.order.id);
                    body.append("kind", kind);
                    body.append("file", file);
                    const response = await axios.post("KaheelOrders/upload", body);
                    if (response.data.status === false) {
                        this.error = response.data.message;
                        return;
                    }
                    this.apply(response);
                }
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "The file was not uploaded." : "لم يُرفع الملف.");
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>
