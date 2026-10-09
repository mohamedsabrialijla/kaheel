<template>
    <div class="ka-page">
        <header>
            <p class="ka-eyebrow">FORMA OPERATIONS INTELLIGENCE</p>
            <h1 class="ka-title">{{ t.title }}</h1>
            <p class="ka-lead">{{ t.lead }}</p>
        </header>

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
                    <line v-for="y in [40, 90, 140, 190]" :key="y" x1="48" :y1="y" x2="620" :y2="y" stroke="var(--border)" stroke-dasharray="3 3" />
                    <line x1="48" y1="24" x2="48" y2="220" stroke="currentColor" />
                    <line x1="48" y1="220" x2="620" y2="220" stroke="#C7783E" stroke-width="2" />
                    <text x="8" y="44">3600k</text>
                    <text x="16" y="216">0k</text>
                    <text v-for="(month, index) in months" :key="month" :x="90 + index * 95" y="246" text-anchor="middle">{{ month }}</text>
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
                    <circle cx="160" cy="130" r="95" fill="none" stroke="var(--border)" stroke-width="40" />
                    <circle cx="160" cy="130" r="55" fill="var(--background)" />
                </svg>
                <ul class="ka-legend">
                    <li v-for="row in categories" :key="row.name">
                        <span class="ka-dot" :style="{ background: row.color }"></span>{{ row.name }}
                    </li>
                </ul>
            </article>
        </section>

        <section class="ka-split-even">
            <article class="ka-panel">
                <h2 class="ka-section-title">{{ t.stagesTitle }}</h2>
                <div class="ka-bars">
                    <div v-for="stage in stages" :key="stage" class="ka-bar-col">
                        <span>0</span>
                        <span class="ka-bar-track"><span class="ka-bar-fill"></span></span>
                        <span>{{ stage }}</span>
                    </div>
                </div>
            </article>
            <article class="ka-panel">
                <h2 class="ka-section-title">{{ t.slaTitle }}</h2>
                <ul class="ka-sla">
                    <li v-for="row in sla" :key="row.label">
                        <div>
                            <p>{{ row.label }}</p>
                            <p class="hint">{{ row.target }}</p>
                        </div>
                        <div class="ka-end">
                            <strong>—</strong>
                            <p class="hint">{{ t.noData }}</p>
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
                        <tr>
                            <td class="ka-empty" :colspan="supplierCols.length">{{ t.noData }}</td>
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
                            <th v-for="col in savingCols" :key="col">{{ col }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in units" :key="row.type">
                            <td>{{ row.type }}</td>
                            <td>{{ row.beds }}</td>
                            <td>{{ num(row.vip) }}</td>
                            <td>{{ num(row.classic) }}</td>
                            <td class="ka-primary">{{ num(row.vip - row.classic) }}</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ka-panel">
            <h2 class="ka-section-title">{{ t.packagesTitle }}</h2>
            <div class="ka-mix">
                <article v-for="pack in packages" :key="pack">
                    <h3>{{ pack }}</h3>
                    <p class="muted">{{ t.ordersLabel }}: 0</p>
                    <p>{{ t.revenueLabel }}: {{ money(0) }}</p>
                    <p class="muted">{{ t.costLabel }}: {{ money(0) }}</p>
                </article>
            </div>
        </section>
    </div>
</template>

<script>
const icon = (inner) => `<svg viewBox="0 0 24 24" aria-hidden="true">${inner}</svg>`;

export default {
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        ar() {
            return this.locale === "ar";
        },
        months() {
            return Array.from({ length: 6 }, (_, index) => {
                const date = new Date();
                date.setMonth(date.getMonth() - (5 - index));
                return date.getFullYear() + "-" + String(date.getMonth() + 1).padStart(2, "0");
            });
        },
        t() {
            if (this.ar) {
                return {
                    title: "لوحة مؤشرات الإدارة",
                    lead: "المبيعات والتكاليف والهوامش وأداء الموردين ومستويات الخدمة، محتسبة آلياً من الطلبات وأوامر الشراء.",
                    salesTitle: "المبيعات مقابل التكلفة والربح",
                    mixTitle: "توزيع المشتريات على البنود",
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
                    purchaseOrders: "أوامر الشراء: " + this.num(0),
                    net: "صافي الربح",
                    margin: "هامش على البيع — · على التكلفة —",
                    commission: "عمولة المنصة من الموردين",
                    perRate: "حسب نسبة كل مورد",
                    orders: "الطلبات",
                    active: "نشطة 0 · مغلقة 0",
                    customers: "العملاء",
                    avgOrder: "متوسط قيمة الطلب —",
                    onTime: "الالتزام بمدة التسليم",
                    deviation: "متوسط الانحراف —",
                    late: "طلبات متأخرة",
                    ready: "جاهزة للتسليم 0",
                };
            }
            return {
                title: "Administration analytics",
                lead: "Sales, cost, margin, supplier performance and SLA, computed from orders and purchase orders.",
                salesTitle: "Sales vs cost and profit",
                mixTitle: "Purchase mix by category",
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
                purchaseOrders: "Purchase orders: " + this.num(0),
                net: "Net profit",
                margin: "Margin on sales — · on cost —",
                commission: "Supplier commission",
                perRate: "Per supplier rate",
                orders: "Orders",
                active: "0 active · 0 closed",
                customers: "Customers",
                avgOrder: "Avg order —",
                onTime: "On-time delivery",
                deviation: "Avg deviation —",
                late: "Late orders",
                ready: "0 ready",
            };
        },
        kpis() {
            return [
                { icon: icon('<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>'), value: this.money(0), label: this.t.collected, hint: this.t.vat },
                { icon: icon('<path d="M13.744 17.736a6 6 0 1 1-7.48-7.48"/><path d="M15 6h1v4"/><path d="m6.134 14.768.866-.5 2 3.464"/><circle cx="16" cy="8" r="6"/>'), value: this.money(0), label: this.t.supplierCost, hint: this.t.purchaseOrders },
                { icon: icon('<path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/>'), value: this.money(0), label: this.t.net, hint: this.t.margin },
                { icon: icon('<line x1="19" x2="5" y1="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>'), value: this.money(0), label: this.t.commission, hint: this.t.perRate },
                { icon: icon('<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>'), value: "0", label: this.t.orders, hint: this.t.active },
                { icon: icon('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/>'), value: "0", label: this.t.customers, hint: this.t.avgOrder },
                { icon: icon('<line x1="10" x2="14" y1="2" y2="2"/><line x1="12" x2="15" y1="14" y2="11"/><circle cx="12" cy="14" r="8"/>'), value: "—", label: this.t.onTime, hint: this.t.deviation },
                { icon: icon('<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>'), value: "0", label: this.t.late, hint: this.t.ready },
            ];
        },
        categories() {
            const colors = ["#C7783E", "#8F6A4B", "#5C6B63", "#9A5B3F", "#6D7C88", "#B99A6B"];
            const names = this.ar
                ? ["أثاث", "مطبخ", "دواليب", "أجهزة", "ستائر ومفروشات", "توريد وتركيب"]
                : ["Furniture", "Kitchen", "Wardrobes", "Appliances", "Curtains & textiles", "Supply & installation"];
            return names.map((name, index) => ({ name, color: colors[index] }));
        },
        stages() {
            return ["paid", "design_approval", "management_approval", "supplier_receipt", "supplier_started", "supplier_ready", "received", "closed"];
        },
        sla() {
            if (this.ar) {
                return [
                    { label: "الالتزام بمدة التسليم", target: "المستهدف: 95%" },
                    { label: "مراجعة التصميم واعتماده (أيام)", target: "المستهدف: 3 يوم" },
                    { label: "مراجعة الإدارة (أيام)", target: "المستهدف: 2 يوم" },
                    { label: "بدء التوريد بعد الاعتماد (أيام)", target: "المستهدف: 2 يوم" },
                    { label: "مدة التوريد (أيام)", target: "المستهدف: 18 يوم" },
                    { label: "التسليم والإغلاق (أيام)", target: "المستهدف: 25 يوم" },
                ];
            }
            return [
                { label: "On-time delivery", target: "Target: 95%" },
                { label: "Design review & approval (days)", target: "Target: 3 days" },
                { label: "Admin review (days)", target: "Target: 2 days" },
                { label: "Supply start after approval (days)", target: "Target: 2 days" },
                { label: "Supply duration (days)", target: "Target: 18 days" },
                { label: "Handover & closing (days)", target: "Target: 25 days" },
            ];
        },
        supplierCols() {
            return this.ar
                ? ["المورد", "الطلبات", "المبيعات", "التكلفة", "النسبة", "العمولة", "الالتزام", "الانحراف", "متأخرة"]
                : ["Supplier", "Orders", "Revenue", "Cost", "Rate", "Commission", "On-time", "Deviation", "Late"];
        },
        savingCols() {
            return this.ar
                ? ["نوع الوحدة", "غرف", "VIP", "CLASSIC", "توفير العميل", "ربح VIP", "ربح CLASSIC", "أثر الاختيار"]
                : ["Unit type", "Bedrooms", "VIP", "CLASSIC", "Customer saving", "VIP profit", "CLASSIC profit", "Profit impact"];
        },
        packages() {
            return this.ar ? ["باقة VIP", "باقة Classic"] : ["VIP Package", "Classic Package"];
        },
        units() {
            return [
                { type: "A", beds: 1, vip: 119135.48, classic: 98974.09 },
                { type: "B", beds: 2, vip: 173061.89, classic: 148583.74 },
                { type: "C", beds: 2, vip: 173061.89, classic: 148583.74 },
                { type: "G", beds: 2, vip: 173061.89, classic: 148583.74 },
                { type: "D", beds: 3, vip: 191196.59, classic: 165042.11 },
                { type: "F", beds: 3, vip: 191196.59, classic: 165042.11 },
            ];
        },
    },
    methods: {
        num(value) {
            return Number(value || 0).toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
        money(value) {
            return this.num(value) + " " + this.t.currency;
        },
    },
};
</script>
