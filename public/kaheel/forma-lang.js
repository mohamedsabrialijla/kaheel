(function () {
    function applyFormaLang(en) {
        const root = document.documentElement;
        root.lang = en ? "en" : "ar";
        root.dir = en ? "ltr" : "rtl";
        const dict = window.FORMA_I18N || {};
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);
        nodes.forEach((node) => {
            const parent = node.parentElement;
            if (!parent) return;
            const tag = parent.tagName;
            if (tag === "SCRIPT" || tag === "STYLE") return;
            if (parent.id === "lang-label") return;
            const raw = node.nodeValue;
            const trimmed = raw.trim();
            if (!trimmed) return;
            if (!node._formaAr) node._formaAr = trimmed;
            const src = node._formaAr;
            if (en) {
                const fromData = parent.getAttribute("data-en");
                const tr = fromData || dict[src];
                if (tr) node.nodeValue = raw.replace(trimmed, tr);
            } else {
                node.nodeValue = raw.replace(trimmed, src);
            }
        });
        ["placeholder", "aria-label", "alt", "title"].forEach((attr) => {
            document.querySelectorAll("[" + attr + "]").forEach((el) => {
                const key = "_formaAr_" + attr;
                if (!el[key]) el[key] = el.getAttribute(attr) || "";
                const src = el[key];
                if (!src) return;
                if (en) {
                    const tr = dict[src];
                    if (tr) el.setAttribute(attr, tr);
                } else {
                    el.setAttribute(attr, src);
                }
            });
        });
        const label = document.getElementById("lang-label");
        if (label) label.textContent = en ? "ع" : "EN";
    }

    window.applyFormaLang = applyFormaLang;
})();
