<template>
    <div class="ka-page">
        <header>
            <p class="ka-eyebrow">FORMA OPERATIONS INTELLIGENCE</p>
            <h1 class="ka-title">{{ t.title }}</h1>
            <p class="ka-lead">{{ t.lead }}</p>
        </header>

        <p v-if="loading" class="ka-sup-muted">{{ t.loading }}</p>
        <p v-else-if="error" class="ka-ord-error">{{ error }}</p>

        <section class="ka-kpis">
            <article v-for="card in kpis" :key="card.label" class="ka-kpi">
                <span v-html="card.icon"></span>
                <p class="ka-kpi-value">{{ card.value }}</p>
                <p class="ka-kpi-label">{{ card.label }}</p>
                <p class="ka-kpi-hint">{{ card.hint }}</p>
            </article>
        </section>

        <section class="ka-split">
            <article class="ka-panel">
                <h2 class="ka-section-title">{{ t.salesTitle }}</h2>
                <svg class="ka-chart" viewBox="0 0 640 280" role="img">
                    <line v-for="y in [44, 92, 140, 188]" :key="y" x1="56" :y1="y" x2="620" :y2="y" stroke="var(--border)" stroke-dasharray="3 3" />
                    <line x1="56" y1="24" x2="56" y2="220" stroke="currentColor" />
                    <line x1="56" y1="220" x2="620" y2="220" stroke="#C7783E" stroke-width="2" />
                    <text x="8" y="48">{{ chartMaxLabel }}</text>
                    <text x="16" y="216">0</text>
                    <polyline class="ka-line is-sales" fill="none" :points="linePoints('sales')" />
                    <polyline class="ka-line is-cost" fill="none" :points="linePoints('cost')" />
                    <polyline class="ka-line is-profit" fill="none" :points="linePoints('profit')" />
                    <circle v-for="(point, index) in monthDots('sales')" :key="'s' + index" :cx="point.x" :cy="point.y" r="3.5" fill="#C7783E" />
                    <text v-for="(month, index) in months" :key="month.key" :x="monthX(index)" y="246" text-anchor="middle">{{ monthLabel(month.key) }}</text>
                </svg>
                <div class="ka-series">
                    <span><i class="ka-dot" style="background:#C7783E"></i>{{ t.sales }}</span>
                    <span><i class="ka-dot" style="background:#8F6A4B"></i>{{ t.cost }}</span>
                    <span><i class="ka-dot" style="background:#5C6B63"></i>{{ t.profit }}</span>
                </div>
            </article>
            <article class="ka-panel">
                <h2 class="ka-section-title">{{ t.mixTitle }}</h2>
                <svg class="ka-chart" viewBox="0 0 320 280" role="img">
                    <circle cx="160" cy="130" r="72" fill="none" stroke="var(--border)" stroke-width="36" />
                    <circle
                        v-for="slice in donutSlices"
                        :key="slice.code"
                        class="ka-donut"
                        cx="160"
                        cy="130"
                        r="72"
                        fill="none"
                        stroke-width="36"
                        :stroke="slice.color"
                        :stroke-dasharray="slice.dash"
                        :stroke-dashoffset="slice.offset"
                        transform="rotate(-90 160 130)"
                    />
                    <circle cx="160" cy="130" r="48" fill="var(--background)" />
                    <text x="160" y="126" text-anchor="middle" font-size="13">{{ mixTotalLabel }}</text>
                    <text x="160" y="146" text-anchor="middle">{{ t.mixCenter }}</text>
                </svg>
                <ul class="ka-legend">
                    <li v-for="row in mixRows" :key="row.code">
                        <span class="ka-dot" :style="{ background: row.color }"></span>
                        {{ mixName(row) }}
                        <em>{{ mixShare(row) }}</em>
                    </li>
                </ul>
            </article>
        </section>

        <section class="ka-split-even">
            <article class="ka-panel">
                <h2 class="ka-section-title">{{ t.stagesTitle }}</h2>
                <div class="ka-bars">
                    <div v-for="stage in stages" :key="stage.code" class="ka-bar-col">
                        <span>{{ stage.count }}</span>
                        <span class="ka-bar-track"><span class="ka-bar-fill" :style="{ height: barHeight(stage.count) }"></span></span>
                        <span>{{ locale === 'en' ? stage.en : stage.ar }}</span>
                    </div>
                </div>
            </article>
            <article class="ka-panel">
                <h2 class="ka-section-title">{{ t.slaTitle }}</h2>
                <ul class="ka-sla">
                    <li v-for="row in slaRows" :key="row.key" :class="{ 'is-warn': row.warn }">
                        <div>
                            <p>{{ row.label }}</p>
                            <p class="hint">{{ row.target }}</p>
                        </div>
                        <div class="ka-end">
                            <strong>{{ row.value }}</strong>
                            <p class="hint">{{ row.note }}</p>
                        </div>
                    </li>
                </ul>
            </article>
        </section>

        <section class="ka-panel">
            <h2 class="ka-section-title">
                <svg viewBox="0 0 24 24"><path d="m16 16 2 2 4-4"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/></svg>
                {{ t.suppliersTitle }}
            </h2>
            <div class="ka-table-wrap">
                <table class="ka-table wide">
                    <thead>
                        <tr>
                            <th v-for="col in supplierCols" :key="col">{{ col }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!suppliers.length">
                            <td class="ka-empty" :colspan="supplierCols.length">{{ t.noData }}</td>
                        </tr>
                        <tr v-for="row in suppliers" :key="row.id">
                            <td>{{ locale === 'en' ? row.name_en : row.name_ar }}</td>
                            <td>{{ row.orders }}</td>
                            <td>{{ money(row.sales) }}</td>
                            <td>{{ money(row.cost) }}</td>
                            <td>{{ num(row.rate) }}%</td>
                            <td>{{ money(row.commission) }}</td>
                            <td>{{ pct(row.on_time) }}</td>
                            <td>{{ days(row.deviation) }}</td>
                            <td>{{ row.late }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ka-panel">
            <h2 class="ka-section-title">
                <svg viewBox="0 0 24 24"><path d="M11 17h3v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3a3.16 3.16 0 0 0 2-2h1a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-1a5 5 0 0 0-2-4V3a4 4 0 0 0-3.2 1.6l-.3.4H11a6 6 0 0 0-6 6v1a5 5 0 0 0 2 4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z"/><path d="M16 10h.01"/><path d="M2 8v1a2 2 0 0 0 2 2h1"/></svg>
                {{ t.savingsTitle }}
            </h2>
            <div class="ka-table-wrap">
                <table class="ka-table mid">
                    <thead>
                        <tr>
                            <th>{{ t.unitType }}</th>
                            <th>{{ t.beds }}</th>
                            <th v-for="pack in catalog" :key="'h-' + pack.code">{{ packName(pack) }}</th>
                            <th>{{ t.customerSaving }}</th>
                            <th>{{ t.choiceImpact }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!catalog.length">
                            <td class="ka-empty" :colspan="4">{{ t.noData }}</td>
                        </tr>
                        <tr v-for="row in units" :key="row.type">
                            <td>{{ row.type }}</td>
                            <td>{{ row.beds }}</td>
                            <td v-for="pack in catalog" :key="pack.code + '-' + row.type">{{ priceCell(pack, row.type) }}</td>
                            <td class="ka-primary">{{ savingCell(row.type) }}</td>
                            <td>{{ impactCell(row.type) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ka-panel">
            <h2 class="ka-section-title">{{ t.packagesTitle }}</h2>
            <p v-if="!catalog.length" class="muted">{{ t.noData }}</p>
            <div v-else class="ka-mix">
                <article v-for="pack in catalog" :key="pack.code">
                    <h3>{{ packName(pack) }}</h3>
                    <p class="muted">{{ t.ordersLabel }}: {{ pack.orders || 0 }}</p>
                    <p>{{ t.revenueLabel }}: {{ money(pack.sales || 0) }}</p>
                    <p class="muted">{{ t.costLabel }}: {{ money(pack.cost || 0) }}</p>
                </article>
            </div>
        </section>
    </div>
</template>

<script>
import axios from "axios";

const icon = (inner) => `<svg viewBox="0 0 24 24" aria-hidden="true">${inner}</svg>`;
const RING = 2 * Math.PI * 72;

export default {
    data() {
        return {
            loading: true,
            error: "",
            stats: {
                kpis: {},
                months: [],
                mix: [],
                stages: [],
                sla: [],
                suppliers: [],
                packages: [],
            },
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        ar() {
            return this.locale === "ar";
        },
        k() {
            return this.stats.kpis || {};
        },
        months() {
            return this.stats.months || [];
        },
        mixRows() {
            return (this.stats.mix || []).filter((row) => Number(row.value) > 0);
        },
        mixTotal() {
            return this.mixRows.reduce((sum, row) => sum + Number(row.value || 0), 0);
        },
        mixTotalLabel() {
            return this.mixTotal ? this.compact(this.mixTotal) : "0";
        },
        stages() {
            return this.stats.stages || [];
        },
        suppliers() {
            return this.stats.suppliers || [];
        },
        catalog() {
            return this.stats.packages || [];
        },
        chartMax() {
            const values = this.months.flatMap((month) => [month.sales, month.cost, month.profit]);
            const max = Math.max(0, ...values.map((value) => Number(value || 0)));
            return max > 0 ? max : 1;
        },
        chartMaxLabel() {
            return this.compact(this.chartMax);
        },
        donutSlices() {
            let offset = 0;
            const total = this.mixTotal || 1;
            return this.mixRows.map((row) => {
                const ratio = Number(row.value || 0) / total;
                const slice = {
                    code: row.code,
                    color: row.color,
                    dash: `${ratio * RING} ${RING}`,
                    offset: -offset * RING,
                };
                offset += ratio;
                return slice;
            });
        },
        t() {
            if (this.ar) {
                return {
                    title: "لوحة مؤشرات الإدارة",
                    lead: "المبيعات والتكاليف والهوامش وأداء الموردين ومستويات الخدمة، محتسبة آلياً من الطلبات وأوامر التوريد.",
                    loading: "جاري تحميل المؤشرات…",
                    salesTitle: "المبيعات مقابل التكلفة والربح",
                    mixTitle: "توزيع المشتريات على البنود",
                    mixCenter: "المزيج",
                    stagesTitle: "مراحل الطلبات",
                    slaTitle: "مستويات الخدمة SLA",
                    suppliersTitle: "أداء الموردين",
                    savingsTitle: "التوفير للعميل وأثره على الربح",
                    packagesTitle: "مزيج الباقات",
                    sales: "المبيعات",
                    cost: "التكلفة",
                    profit: "الربح",
                    noData: "لا توجد بيانات بعد",
                    ordersLabel: "الطلبات",
                    revenueLabel: "المبيعات",
                    costLabel: "التكلفة",
                    currency: "ريال",
                    collected: "إجمالي المبيعات المحصلة",
                    vat: "شاملة الضريبة",
                    supplierCost: "تكلفة الموردين",
                    net: "صافي الربح",
                    commission: "عمولة المنصة من الموردين",
                    perRate: "حسب نسبة كل مورد",
                    orders: "الطلبات",
                    customers: "العملاء",
                    onTime: "الالتزام بمدة التسليم",
                    late: "طلبات متأخرة",
                    unitType: "نوع الوحدة",
                    beds: "غرف",
                    customerSaving: "توفير العميل",
                    choiceImpact: "أثر الاختيار",
                    slaOnTime: "الالتزام بمدة التسليم",
                    slaDesign: "مراجعة التصميم واعتماده (أيام)",
                    slaAdmin: "مراجعة الإدارة (أيام)",
                    slaSupplyStart: "بدء التوريد بعد الاعتماد (أيام)",
                    slaSupply: "مدة التوريد (أيام)",
                    slaClose: "التسليم والإغلاق (أيام)",
                    targetPct: "المستهدف: ",
                    targetDays: "المستهدف: ",
                    daysUnit: " يوم",
                    vsTarget: "مقابل المستهدف",
                    met: "ضمن المستهدف",
                    missed: "خارج المستهدف",
                };
            }
            return {
                title: "Administration analytics",
                lead: "Sales, cost, margin, supplier performance and SLA, computed from orders and supply assignments.",
                loading: "Loading analytics…",
                salesTitle: "Sales vs cost and profit",
                mixTitle: "Purchase mix by category",
                mixCenter: "Mix",
                stagesTitle: "Order stages",
                slaTitle: "Service levels (SLA)",
                suppliersTitle: "Supplier performance",
                savingsTitle: "Customer savings and profit impact",
                packagesTitle: "Package mix",
                sales: "Sales",
                cost: "Cost",
                profit: "Profit",
                noData: "No data yet",
                ordersLabel: "Orders",
                revenueLabel: "Revenue",
                costLabel: "Cost",
                currency: "SAR",
                collected: "Collected sales",
                vat: "VAT included",
                supplierCost: "Supplier cost",
                net: "Net profit",
                commission: "Supplier commission",
                perRate: "Per supplier rate",
                orders: "Orders",
                customers: "Customers",
                onTime: "On-time delivery",
                late: "Late orders",
                unitType: "Unit type",
                beds: "Bedrooms",
                customerSaving: "Customer saving",
                choiceImpact: "Profit impact",
                slaOnTime: "On-time delivery",
                slaDesign: "Design review & approval (days)",
                slaAdmin: "Admin review (days)",
                slaSupplyStart: "Supply start after approval (days)",
                slaSupply: "Supply duration (days)",
                slaClose: "Handover & closing (days)",
                targetPct: "Target: ",
                targetDays: "Target: ",
                daysUnit: " days",
                vsTarget: "vs target",
                met: "On target",
                missed: "Off target",
            };
        },
        kpis() {
            const k = this.k;
            const margin = (k.margin_sales != null || k.margin_cost != null)
                ? (this.ar
                    ? `هامش على البيع ${this.pct(k.margin_sales)} · على التكلفة ${this.pct(k.margin_cost)}`
                    : `Margin on sales ${this.pct(k.margin_sales)} · on cost ${this.pct(k.margin_cost)}`)
                : (this.ar ? "هامش على البيع — · على التكلفة —" : "Margin on sales — · on cost —");
            return [
                { icon: icon('<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>'), value: this.money(k.collected), label: this.t.collected, hint: this.t.vat },
                { icon: icon('<path d="M13.744 17.736a6 6 0 1 1-7.48-7.48"/><path d="M15 6h1v4"/><path d="m6.134 14.768.866-.5 2 3.464"/><circle cx="16" cy="8" r="6"/>'), value: this.money(k.cost), label: this.t.supplierCost, hint: (this.ar ? "أوامر التوريد: " : "Purchase orders: ") + this.int(k.purchase_orders) },
                { icon: icon('<path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/>'), value: this.money(k.profit), label: this.t.net, hint: margin },
                { icon: icon('<line x1="19" x2="5" y1="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>'), value: this.money(k.commission), label: this.t.commission, hint: this.t.perRate },
                { icon: icon('<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>'), value: this.int(k.orders), label: this.t.orders, hint: this.ar ? `نشطة ${this.int(k.active)} · مغلقة ${this.int(k.closed)}` : `${this.int(k.active)} active · ${this.int(k.closed)} closed` },
                { icon: icon('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/>'), value: this.int(k.customers), label: this.t.customers, hint: (this.ar ? "متوسط قيمة الطلب " : "Avg order ") + this.money(k.avg_order) },
                { icon: icon('<line x1="10" x2="14" y1="2" y2="2"/><line x1="12" x2="15" y1="14" y2="11"/><circle cx="12" cy="14" r="8"/>'), value: this.pct(k.on_time), label: this.t.onTime, hint: (this.ar ? "متوسط الانحراف " : "Avg deviation ") + this.days(k.deviation) },
                { icon: icon('<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>'), value: this.int(k.late), label: this.t.late, hint: (this.ar ? "جاهزة للتسليم " : "Ready ") + this.int(k.ready) },
            ];
        },
        slaRows() {
            const labels = {
                on_time: this.t.slaOnTime,
                design: this.t.slaDesign,
                admin: this.t.slaAdmin,
                supply_start: this.t.slaSupplyStart,
                supply: this.t.slaSupply,
                close: this.t.slaClose,
            };
            return (this.stats.sla || []).map((row) => {
                const isPct = row.unit === "pct";
                const value = row.value == null ? "—" : (isPct ? this.pct(row.value) : (row.value + this.t.daysUnit));
                const warn = row.value != null && (isPct ? row.value < row.target : row.value > row.target);
                return {
                    key: row.key,
                    label: labels[row.key] || row.key,
                    target: (isPct ? this.t.targetPct : this.t.targetDays) + row.target + (isPct ? "%" : this.t.daysUnit),
                    value,
                    note: row.value == null ? this.t.noData : (warn ? this.t.missed : this.t.met),
                    warn,
                };
            });
        },
        supplierCols() {
            return this.ar
                ? ["المورد", "الطلبات", "المبيعات", "التكلفة", "النسبة", "العمولة", "الالتزام", "الانحراف", "متأخرة"]
                : ["Supplier", "Orders", "Revenue", "Cost", "Rate", "Commission", "On-time", "Deviation", "Late"];
        },
        units() {
            return [
                { type: "A", beds: 1 },
                { type: "B", beds: 2 },
                { type: "C", beds: 2 },
                { type: "G", beds: 2 },
                { type: "D", beds: 3 },
                { type: "F", beds: 3 },
            ];
        },
    },
    mounted() {
        this.loadStats();
    },
    methods: {
        async loadStats() {
            this.loading = true;
            this.error = "";
            try {
                const response = await axios.get("KaheelOverview/stats");
                this.stats = response.data.items || this.stats;
            } catch (error) {
                this.error = this.t.noData;
            } finally {
                this.loading = false;
            }
        },
        packName(pack) {
            return this.ar ? (pack.name_ar || pack.code) : (pack.name_en || pack.code);
        },
        mixName(row) {
            return this.ar ? row.ar : row.en;
        },
        mixShare(row) {
            if (!this.mixTotal) return "";
            return this.pct((Number(row.value) / this.mixTotal) * 100);
        },
        unitTier(pack, type) {
            const letter = String(type || "").toUpperCase();
            const tiers = pack.tiers || [];
            return tiers.find((tier) => {
                const hay = `${tier.name_ar || ""} ${tier.name_en || ""}`.toUpperCase();
                return hay.includes("نوع " + letter)
                    || hay.includes("TYPE " + letter)
                    || hay.includes("TYPES " + letter)
                    || hay.includes("· " + letter)
                    || hay.includes(" " + letter + " ")
                    || hay.includes(" " + letter + "·")
                    || hay.endsWith(" " + letter);
            }) || null;
        },
        unitPrice(pack, type) {
            const match = this.unitTier(pack, type);
            return match ? Number(match.price_incl_vat || 0) : null;
        },
        unitCost(pack, type) {
            const match = this.unitTier(pack, type);
            return match ? Number(match.supplier_cost || 0) : null;
        },
        priceCell(pack, type) {
            const price = this.unitPrice(pack, type);
            return price == null ? "—" : this.num(price);
        },
        savingCell(type) {
            const prices = this.catalog.map((pack) => this.unitPrice(pack, type)).filter((value) => value != null);
            if (prices.length < 2) return "—";
            return this.num(Math.max(...prices) - Math.min(...prices));
        },
        impactCell(type) {
            const rows = this.catalog.map((pack) => {
                const price = this.unitPrice(pack, type);
                const cost = this.unitCost(pack, type);
                if (price == null) return null;
                return { price, cost: cost || 0, margin: price - (cost || 0) };
            }).filter(Boolean);
            if (rows.length < 2) return "—";
            const high = rows.reduce((best, row) => (row.price > best.price ? row : best));
            const low = rows.reduce((best, row) => (row.price < best.price ? row : best));
            return this.num(low.margin - high.margin);
        },
        monthX(index) {
            const n = this.months.length || 1;
            const span = n > 1 ? 540 / (n - 1) : 0;
            return 70 + index * span;
        },
        monthY(value) {
            return 220 - (Number(value || 0) / this.chartMax) * 176;
        },
        linePoints(key) {
            return this.months.map((month, index) => this.monthX(index) + "," + this.monthY(month[key])).join(" ");
        },
        monthDots(key) {
            return this.months.map((month, index) => ({ x: this.monthX(index), y: this.monthY(month[key]) }));
        },
        monthLabel(key) {
            const [year, month] = String(key).split("-");
            const date = new Date(Number(year), Number(month) - 1, 1);
            return date.toLocaleDateString(this.ar ? "ar-SA" : "en-GB", { month: "short" });
        },
        barHeight(count) {
            const max = Math.max(1, ...this.stages.map((stage) => Number(stage.count || 0)));
            return ((Number(count || 0) / max) * 100) + "%";
        },
        int(value) {
            return Number(value || 0).toLocaleString("en-US");
        },
        num(value) {
            return Number(value || 0).toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
        money(value) {
            return this.num(value) + " " + this.t.currency;
        },
        compact(value) {
            const amount = Number(value || 0);
            if (amount >= 1000000) return (amount / 1000000).toFixed(1) + "M";
            if (amount >= 1000) return (amount / 1000).toFixed(amount >= 10000 ? 0 : 1) + "k";
            return String(Math.round(amount));
        },
        pct(value) {
            return value == null || value === "" ? "—" : Number(value).toLocaleString("en-US", { maximumFractionDigits: 1 }) + "%";
        },
        days(value) {
            return value == null || value === "" ? "—" : Number(value).toLocaleString("en-US", { maximumFractionDigits: 1 }) + this.t.daysUnit;
        },
    },
};
</script>
