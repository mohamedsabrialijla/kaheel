<?php $noticeCustomer = auth('admin')->check() ? null : kaheel_customer(); ?>
<?php if($noticeCustomer): ?>
    <div class="notice-bell" id="notice-bell" data-url="<?php echo e(url(app()->getLocale().'/orders/notices')); ?>" data-orders="<?php echo e(url(app()->getLocale().'/orders')); ?>">
        <button class="btn btn-sm notice-bell-btn" id="notice-bell-btn" type="button" aria-label="التنبيهات">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            <span class="notice-bell-badge" id="notice-bell-badge" hidden>0</span>
        </button>
        <div class="notice-bell-menu" id="notice-bell-menu" hidden></div>
    </div>
    <script>
        (() => {
            const root = document.getElementById("notice-bell");
            const button = document.getElementById("notice-bell-btn");
            const menu = document.getElementById("notice-bell-menu");
            const badge = document.getElementById("notice-bell-badge");
            if (!root || root.dataset.ready) return;
            root.dataset.ready = "1";
            const stamp = (iso) => {
                const date = new Date(iso);
                if (Number.isNaN(date.getTime())) return "";
                const en = document.documentElement.lang === "en";
                return new Intl.DateTimeFormat(en ? "en" : "ar", { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }).format(date);
            };
            const mediaLabel = (kind) => {
                const en = document.documentElement.lang === "en";
                const labels = en
                    ? { image: "Photo", video: "Video", audio: "Voice message", pdf: "PDF" }
                    : { image: "صورة", video: "فيديو", audio: "رسالة صوتية", pdf: "PDF" };
                return labels[kind] || "";
            };
            let lastData = null;
            const render = (data) => {
                lastData = data;
                const total = Number(data.total || 0);
                badge.textContent = String(total);
                badge.hidden = total < 1;
                menu.replaceChildren();
                const items = data.items || [];
                if (!items.length) {
                    const empty = document.createElement("p");
                    empty.className = "notice-bell-empty";
                    empty.textContent = document.documentElement.lang === "en" ? "No new messages" : "لا رسائل جديدة";
                    menu.append(empty);
                    return;
                }
                items.forEach((item) => {
                    const link = document.createElement("a");
                    link.className = "notice-bell-item";
                    link.href = root.dataset.orders + "/" + item.order_id + "?chat=1";
                    const title = document.createElement("strong");
                    title.textContent = item.number + (item.name ? " · " + item.name : "");
                    const body = document.createElement("span");
                    body.textContent = (item.body || "").trim() || mediaLabel(item.kind);
                    const time = document.createElement("time");
                    time.textContent = stamp(item.created_at);
                    link.append(title, body, time);
                    link.addEventListener("click", (event) => {
                        const target = new URL(link.href);
                        if (location.pathname !== target.pathname) return;
                        event.preventDefault();
                        menu.hidden = true;
                        document.getElementById("order-chat-open")?.click();
                    });
                    menu.append(link);
                });
            };
            const load = async () => {
                try {
                    const response = await fetch(root.dataset.url, { headers: { "Accept": "application/json" }, credentials: "same-origin" });
                    if (!response.ok) return;
                    render(await response.json());
                } catch (error) {}
            };
            button.addEventListener("click", () => {
                menu.hidden = !menu.hidden;
                if (!menu.hidden) load();
            });
            document.addEventListener("click", (event) => {
                if (!root.contains(event.target)) menu.hidden = true;
            });
            document.getElementById("lang-toggle")?.addEventListener("click", () => {
                setTimeout(() => { if (lastData) render(lastData); }, 0);
            });
            load();
            setInterval(load, 8000);
        })();
    </script>
<?php endif; ?>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/notice-bell.blade.php ENDPATH**/ ?>