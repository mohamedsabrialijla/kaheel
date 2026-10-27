(function () {
    function allowed(file, accept) {
        if (!accept) return true;
        const name = (file.name || "").toLowerCase();
        const type = (file.type || "").toLowerCase();
        return accept.split(",").map((part) => part.trim().toLowerCase()).some((part) => {
            if (!part) return false;
            if (part === "image/*") return type.startsWith("image/") && type !== "image/svg+xml";
            if (part.startsWith(".")) return name.endsWith(part);
            return type === part;
        });
    }

    function bind(root) {
        const input = root.querySelector("input[type='file']");
        const list = root.querySelector("[data-list]");
        const zone = root.querySelector("[data-browse]");
        if (!input || !list || !zone || root.dataset.bound === "1") return;
        root.dataset.bound = "1";
        const max = Number(root.dataset.max || (input.multiple ? 5 : 1));
        const maxMb = Number(root.dataset.maxMb || 20);
        const urls = [];
        let stored = Array.from(input.files || []);
        let syncing = false;

        function current() {
            return stored.slice();
        }

        function assign(files) {
            stored = files.slice(0, max);
            const transfer = new DataTransfer();
            stored.forEach((file) => transfer.items.add(file));
            syncing = true;
            input.files = transfer.files;
            input.dispatchEvent(new Event("change", { bubbles: true }));
            syncing = false;
            draw();
        }

        function draw() {
            urls.forEach((url) => URL.revokeObjectURL(url));
            urls.length = 0;
            list.innerHTML = "";
            current().forEach((file, index) => {
                const item = document.createElement("div");
                item.className = "ka-drop-item";
                const thumb = document.createElement("span");
                const pdf = /pdf/i.test(file.type) || /\.pdf$/i.test(file.name);
                thumb.className = "ka-drop-thumb" + (pdf ? " is-pdf" : "");
                if (!pdf && file.type.startsWith("image/")) {
                    const url = URL.createObjectURL(file);
                    urls.push(url);
                    thumb.style.backgroundImage = 'url("' + url + '")';
                } else {
                    thumb.textContent = pdf ? "PDF" : "FILE";
                }
                const meta = document.createElement("span");
                meta.className = "ka-drop-meta";
                const name = document.createElement("strong");
                name.textContent = file.name;
                const size = document.createElement("em");
                size.textContent = (file.size / (1024 * 1024)).toFixed(2) + " MB";
                meta.append(name, size);
                const remove = document.createElement("button");
                remove.type = "button";
                remove.className = "ka-drop-x";
                remove.setAttribute("aria-label", "Remove");
                remove.textContent = "×";
                remove.addEventListener("click", (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    assign(current().filter((_, i) => i !== index));
                });
                item.append(thumb, meta, remove);
                list.append(item);
            });
            root.classList.toggle("has-files", current().length > 0);
        }

        function take(incoming) {
            const next = input.multiple ? current().concat(incoming) : incoming.slice(0, 1);
            const unique = [];
            const seen = new Set();
            next.forEach((file) => {
                const key = file.name + ":" + file.size + ":" + file.lastModified;
                if (seen.has(key) || !allowed(file, input.accept) || file.size > maxMb * 1024 * 1024) return;
                seen.add(key);
                unique.push(file);
            });
            assign(unique.slice(0, max));
        }

        zone.addEventListener("click", () => input.click());
        ["dragenter", "dragover"].forEach((name) => zone.addEventListener(name, (event) => {
            event.preventDefault();
            root.classList.add("is-over");
        }));
        ["dragleave", "drop"].forEach((name) => zone.addEventListener(name, (event) => {
            event.preventDefault();
            root.classList.remove("is-over");
        }));
        zone.addEventListener("drop", (event) => take(Array.from(event.dataTransfer?.files || [])));
        input.addEventListener("change", () => {
            if (syncing) return;
            take(Array.from(input.files || []));
        });
        input.classList.add("ka-drop-input");
        draw();
    }

    function init(scope) {
        (scope || document).querySelectorAll("[data-ka-drop]").forEach(bind);
    }

    window.kaheelBindFilePickers = init;
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", () => init());
    } else {
        init();
    }
})();
