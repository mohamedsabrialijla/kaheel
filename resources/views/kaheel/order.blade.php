@php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء طلب | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=13" rel="stylesheet">
</head>
<body>
    @include('kaheel.partials.public-header')
    <main class="order-page">
        <div class="order-steps">
            <span class="order-step" id="step-project" data-ar="اختر المشروع" data-en="Select project">اختر المشروع</span>
            <span class="order-step" id="step-unit" data-ar="اختر الوحدة" data-en="Select unit">اختر الوحدة</span>
            <span class="order-step" id="step-review" data-ar="مراجعة الطلب" data-en="Order review">مراجعة الطلب</span>
        </div>

        <section class="order-card">
            <h2 data-ar="الباقة المختارة" data-en="Selected package">الباقة المختارة</h2>
            <p class="order-lead" data-ar="اختر الباقة الأساسية أولاً، ثم اختر الباقة الفرعية وراجع عناصرها." data-en="Pick the main package first, then choose a sub-package and review its items.">اختر الباقة الأساسية أولاً، ثم اختر الباقة الفرعية وراجع عناصرها.</p>
            <div class="order-grid cols-2" id="package-grid"></div>
        </section>

        <section class="order-card" id="tier-section" hidden>
            <h2 data-ar="اختر الباقة الفرعية" data-en="Choose the sub-package">اختر الباقة الفرعية</h2>
            <p class="order-lead" data-ar="لكل باقة فرعية سعرها ومحتوياتها الخاصة." data-en="Each sub-package has its own price and contents.">لكل باقة فرعية سعرها ومحتوياتها الخاصة.</p>
            <div class="order-grid cols-3" id="tier-grid"></div>
        </section>

        <section class="order-card" id="items-section" hidden>
            <h2 data-ar="محتويات الباقة" data-en="Package contents">محتويات الباقة</h2>
            <ul class="order-list" id="item-list"></ul>
        </section>

        <section class="order-card">
            <h2 data-ar="اختر المشروع" data-en="Select project">اختر المشروع</h2>
            <div class="order-grid cols-3" id="compound-grid"></div>
            <p class="order-lead" id="no-compounds" hidden data-ar="لا توجد مشاريع مرتبطة بحسابك" data-en="No projects linked to your account">لا توجد مشاريع مرتبطة بحسابك</p>
        </section>

        <section class="order-card" id="unit-section" hidden>
            <h2 data-ar="اختر الوحدة" data-en="Select unit">اختر الوحدة</h2>
            <div class="order-grid cols-3" id="unit-grid"></div>
            <p class="order-lead" id="no-units" hidden data-ar="لا توجد وحدات في هذا المشروع" data-en="No units in this project">لا توجد وحدات في هذا المشروع</p>
        </section>

        <section class="order-card" id="review-section" hidden>
            <h2 data-ar="مراجعة الطلب" data-en="Order review">مراجعة الطلب</h2>
            <dl class="order-rows" id="review-rows"></dl>
            <h3 data-ar="مخطط الوحدة" data-en="Unit plan">مخطط الوحدة</h3>
            <input class="order-file" id="plans" type="file" accept="image/*,application/pdf" multiple>
            <h3 data-ar="العقد" data-en="Contract">العقد</h3>
            <pre class="order-contract" id="contract"></pre>
            <label class="order-agree">
                <input id="agreed" type="checkbox">
                <span data-ar="أقر بأنني اطلعت على العقد وأوافق على بنوده" data-en="I have read and agree to the contract">أقر بأنني اطلعت على العقد وأوافق على بنوده</span>
            </label>
            <h3 data-ar="التوقيع" data-en="Signature">التوقيع</h3>
            <canvas class="order-pad" id="pad" width="900" height="220"></canvas>
            <div class="order-actions">
                <button class="btn btn-sm btn-outline" type="button" id="clear-sign" data-ar="مسح التوقيع" data-en="Clear">مسح التوقيع</button>
            </div>
            <p class="auth-alert" id="order-alert" hidden></p>
            <button class="btn btn-lg btn-primary auth-submit" type="button" id="submit-order" data-ar="المتابعة للدفع" data-en="Proceed to payment">المتابعة للدفع</button>
            <p class="order-lead" data-ar="{{ $deliveryAr }}" data-en="{{ $deliveryEn }}">{{ $deliveryAr }}</p>
        </section>
    </main>
    <script>
        const config = {
            locale: @json(app()->getLocale()),
            pkg: @json($pkg),
            deposit: @json($depositPercent),
            catalog: @json($catalog),
            compounds: @json($compounds),
            deliveryAr: @json($deliveryAr),
            deliveryEn: @json($deliveryEn)
        };
        const root = document.documentElement;
        let lang = "ar";
        let packageCode = config.pkg || "";
        let tierId = null;
        let compoundId = null;
        let unitId = null;
        let signed = false;

        const pad = document.getElementById("pad");
        const pen = pad.getContext("2d");
        pen.lineWidth = 2;
        pen.lineCap = "round";
        pen.strokeStyle = "#005573";

        function text(node) {
            return lang === "ar" ? node.dataset.ar : node.dataset.en;
        }

        function money(value) {
            return Number(value).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function pick(row, key) {
            return lang === "ar" ? (row[key + "_ar"] || "") : (row[key + "_en"] || "");
        }

        function currentPackage() {
            return config.catalog.find((row) => row.code.toUpperCase() === packageCode.toUpperCase()) || null;
        }

        function currentTier() {
            return (currentPackage()?.tiers || []).find((row) => row.id === tierId) || null;
        }

        function currentCompound() {
            return config.compounds.find((row) => String(row.id) === String(compoundId)) || null;
        }

        function currentUnit() {
            return (currentCompound()?.units || []).find((row) => String(row.id) === String(unitId)) || null;
        }

        function button(className, selected, title, lines, price) {
            const node = document.createElement("button");
            node.type = "button";
            node.className = "order-choice" + (selected ? " is-on" : "");
            const name = document.createElement("strong");
            name.textContent = title;
            node.append(name);
            lines.filter(Boolean).forEach((line) => {
                const span = document.createElement("span");
                span.textContent = line;
                node.append(span);
            });
            if (price) {
                const amount = document.createElement("span");
                amount.className = "order-price";
                amount.textContent = price;
                node.append(amount);
            }
            return node;
        }

        function render() {
            const pkg = currentPackage();
            const tier = currentTier();
            const compound = currentCompound();
            const unit = currentUnit();
            const packageGrid = document.getElementById("package-grid");
            packageGrid.replaceChildren();
            config.catalog.forEach((row) => {
                const prices = row.tiers.map((item) => Number(item.price_incl_vat));
                const range = prices.length ? money(Math.min(...prices)) + " — " + money(Math.max(...prices)) + (lang === "ar" ? " ريال" : " SAR") : "";
                const node = button("", pkg && pkg.code === row.code, pick(row, "name"), [pick(row, "tagline")], range);
                node.addEventListener("click", () => {
                    packageCode = row.code;
                    tierId = null;
                    render();
                });
                packageGrid.append(node);
            });

            const tierSection = document.getElementById("tier-section");
            const tierGrid = document.getElementById("tier-grid");
            tierSection.hidden = !pkg;
            tierGrid.replaceChildren();
            (pkg?.tiers || []).forEach((row) => {
                const node = button("", tier && tier.id === row.id, pick(row, "name"), [
                    pick(row, "description"),
                    (lang === "ar" ? "شامل الضريبة" : "incl. VAT") + " · " + money(row.price_excl_vat) + (lang === "ar" ? " قبل الضريبة" : " excl. VAT"),
                    row.items.length + (lang === "ar" ? " عنصراً" : " items")
                ], money(row.price_incl_vat) + (lang === "ar" ? " ريال" : " SAR"));
                node.addEventListener("click", () => {
                    tierId = row.id;
                    render();
                });
                tierGrid.append(node);
            });

            const itemsSection = document.getElementById("items-section");
            const itemList = document.getElementById("item-list");
            itemsSection.hidden = !tier;
            itemList.replaceChildren();
            (tier?.items || []).forEach((item) => {
                const li = document.createElement("li");
                const copy = document.createElement("div");
                const title = document.createElement("strong");
                title.textContent = pick(item, "name");
                copy.append(title);
                const spec = pick(item, "product_name");
                const detail = pick(item, "detail");
                if (spec || detail) {
                    const extra = document.createElement("span");
                    extra.className = "order-muted";
                    extra.textContent = [spec, detail].filter(Boolean).join(" — ");
                    copy.append(extra);
                }
                const qty = document.createElement("span");
                qty.className = "order-price";
                const amount = String(item.quantity).replace(/\.0+$/, "").replace(/(\.\d*?)0+$/, "$1");
                qty.textContent = amount + " " + (lang === "ar" ? item.unit_label_ar : item.unit_label_en);
                li.append(copy, qty);
                itemList.append(li);
            });

            const compoundGrid = document.getElementById("compound-grid");
            compoundGrid.replaceChildren();
            document.getElementById("no-compounds").hidden = config.compounds.length > 0;
            config.compounds.forEach((row) => {
                const meta = [row.compound_number, row.short_name].filter(Boolean).join(" · ");
                const node = button("", compound && String(compound.id) === String(row.id), row.display_name || "—", [meta]);
                node.addEventListener("click", () => {
                    compoundId = row.id;
                    unitId = null;
                    render();
                });
                compoundGrid.append(node);
            });

            const unitSection = document.getElementById("unit-section");
            const unitGrid = document.getElementById("unit-grid");
            unitSection.hidden = !compound;
            unitGrid.replaceChildren();
            const units = compound?.units || [];
            document.getElementById("no-units").hidden = !compound || units.length > 0;
            units.forEach((row) => {
                const bits = [];
                if (row.bedrooms_number) bits.push(row.bedrooms_number + (lang === "ar" ? " غرف" : " bedrooms"));
                if (row.squar_meters) bits.push(row.squar_meters + (lang === "ar" ? " م²" : " m²"));
                const node = button("", unit && String(unit.id) === String(row.id), row.unit_no || "—", [bits.join(" · ")]);
                node.addEventListener("click", () => {
                    unitId = row.id;
                    render();
                });
                unitGrid.append(node);
            });

            const ready = pkg && tier && compound && unit;
            document.getElementById("review-section").hidden = !ready;
            document.getElementById("step-project").className = "order-step" + (compound ? " is-done" : " is-on");
            document.getElementById("step-unit").className = "order-step" + (unit ? " is-done" : (compound ? " is-on" : ""));
            document.getElementById("step-review").className = "order-step" + (ready ? " is-on" : "");

            const rows = document.getElementById("review-rows");
            rows.replaceChildren();
            if (!ready) return;
            const vat = Number(tier.price_incl_vat) - Number(tier.price_excl_vat);
            const deposit = Number((Number(tier.price_incl_vat) * config.deposit / 100).toFixed(2));
            const remaining = Number(tier.price_incl_vat) - deposit;
            const sar = lang === "ar" ? "ريال" : "SAR";
            const pairs = [
                [lang === "ar" ? "المشروع" : "Project", compound.display_name],
                [lang === "ar" ? "الوحدة" : "Unit", unit.unit_no],
                [lang === "ar" ? "الباقة" : "Package", pick(pkg, "name") + " — " + pick(tier, "name")],
                [lang === "ar" ? "السعر قبل الضريبة" : "Price before VAT", money(tier.price_excl_vat) + " " + sar],
                [lang === "ar" ? "ضريبة القيمة المضافة" : "VAT", money(vat) + " " + sar],
                [lang === "ar" ? "الإجمالي" : "Total", money(tier.price_incl_vat) + " " + sar, true],
                [(lang === "ar" ? "العربون" : "Deposit") + " (" + config.deposit + "%)", money(deposit) + " " + sar, true],
                [lang === "ar" ? "المتبقي" : "Remaining", money(remaining) + " " + sar]
            ];
            pairs.forEach(([label, value, strong]) => {
                const wrap = document.createElement("div");
                const dt = document.createElement("dt");
                const dd = document.createElement("dd");
                dt.textContent = label;
                dd.textContent = value;
                if (strong) dd.className = "is-strong";
                wrap.append(dt, dd);
                rows.append(wrap);
            });
            const daysNote = lang === "ar" ? config.deliveryAr : config.deliveryEn;
            document.getElementById("contract").textContent = [
                lang === "ar" ? "عقد تقديم خدمات تأثيث — FORMA BY AZURE / كحيل" : "Furnishing services agreement — FORMA BY AZURE / KAHEEL",
                (lang === "ar" ? "١. نطاق العمل: توريد وتركيب وتسليم باقة التأثيث " : "1. Scope: supply, delivery and installation of the ") + pick(pkg, "name") + " — " + pick(tier, "name") + (lang === "ar" ? " للوحدة " : " furnishing package for unit ") + unit.unit_no + (lang === "ar" ? "." : "."),
                (lang === "ar" ? "٢. القيمة: " : "2. Value: ") + money(tier.price_incl_vat) + (lang === "ar" ? " ريال شاملة ضريبة القيمة المضافة، ويُسدَّد عربون قدره " : " SAR including VAT. A deposit of ") + money(deposit) + (lang === "ar" ? " ريال عند التوقيع." : " SAR is payable on signature."),
                lang === "ar" ? "٣. التصميم: تقوم الشركة بإعداد التصميم بعد مراجعة مخطط الوحدة المرفوع من العميل." : "3. Design: the company prepares the design after reviewing the unit plan submitted by the client.",
                lang === "ar" ? "٤. التعديلات: أي تغيير أو بديل يختاره العميل يُسعَّر بشكل منفصل ويُضاف إلى الفاتورة النهائية." : "4. Modifications: any change or upgrade selected by the client is priced separately and added to the final invoice.",
                lang === "ar" ? "٥. السداد النهائي: يُسدَّد المتبقي مع قيمة التعديلات قبل بدء التنفيذ والتوريد." : "5. Final payment: the remaining balance and all add-ons are paid before production begins.",
                daysNote,
                lang === "ar" ? "٧. الاستلام: يتم الاستلام في الوحدة ويُوثَّق بمحضر استلام موقّع يُرفق بالطلب." : "7. Handover: handover takes place at the unit and is recorded with a signed handover report.",
                lang === "ar" ? "٨. يُحفظ هذا العقد ضمن مستندات الطلب ولا يمكن تعديله بعد التوقيع." : "8. This agreement is stored with the order and cannot be modified after signature."
            ].join("\n");
            const submit = document.getElementById("submit-order");
            submit.textContent = (lang === "ar" ? "المتابعة للدفع" : "Proceed to payment") + " — " + money(deposit) + " " + sar;
        }

        function applyLang(next) {
            lang = next;
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                if (node.id === "submit-order") return;
                node.textContent = text(node);
            });
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            document.title = (lang === "ar" ? "إنشاء طلب" : "Create order") + " | FORMA BY AZURE – KAHEEL";
            localStorage.setItem("forma-lang", lang);
            render();
        }

        function point(event) {
            const rect = pad.getBoundingClientRect();
            const source = event.touches ? event.touches[0] : event;
            return {
                x: (source.clientX - rect.left) * (pad.width / rect.width),
                y: (source.clientY - rect.top) * (pad.height / rect.height)
            };
        }

        let drawing = false;
        function start(event) {
            drawing = true;
            signed = true;
            const at = point(event);
            pen.beginPath();
            pen.moveTo(at.x, at.y);
        }
        function move(event) {
            if (!drawing) return;
            event.preventDefault();
            const at = point(event);
            pen.lineTo(at.x, at.y);
            pen.stroke();
        }
        pad.addEventListener("pointerdown", start);
        pad.addEventListener("pointermove", move);
        pad.addEventListener("pointerup", () => { drawing = false; });
        pad.addEventListener("pointerleave", () => { drawing = false; });
        document.getElementById("clear-sign").addEventListener("click", () => {
            pen.clearRect(0, 0, pad.width, pad.height);
            signed = false;
        });

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        document.getElementById("submit-order").addEventListener("click", async () => {
            const alertBox = document.getElementById("order-alert");
            alertBox.hidden = true;
            if (!document.getElementById("agreed").checked || !signed) {
                alertBox.hidden = false;
                alertBox.className = "auth-alert is-error";
                alertBox.textContent = lang === "ar" ? "يلزم الموافقة على العقد والتوقيع." : "Accept the contract and add your signature.";
                return;
            }
            const body = new FormData();
            body.append("package_code", packageCode);
            body.append("tier_id", String(tierId));
            body.append("compound_id", String(compoundId));
            body.append("unit_id", String(unitId));
            body.append("agreed", "1");
            body.append("signature", pad.toDataURL("image/png"));
            body.append("lang", lang);
            Array.from(document.getElementById("plans").files || []).slice(0, 5).forEach((file) => body.append("plans[]", file));
            const response = await fetch("/" + config.locale + "/order", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body
            });
            const data = await response.json().catch(() => null);
            if (!data || data.status !== true) {
                alertBox.hidden = false;
                alertBox.className = "auth-alert is-error";
                alertBox.textContent = (data && data.message) || (lang === "ar" ? "تعذر إنشاء الطلب." : "The order could not be created.");
                return;
            }
            location.assign("/" + config.locale + "/orders/" + data.items.id);
        });

        applyLang(localStorage.getItem("forma-lang") === "en" ? "en" : "ar");
    </script>
</body>
</html>
