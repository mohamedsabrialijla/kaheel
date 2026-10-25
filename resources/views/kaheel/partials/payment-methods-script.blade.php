<script>
    (function () {
        const chooseReceipt = document.getElementById("choose-receipt");
        const chooseVisa = document.getElementById("choose-visa");
        if (!chooseReceipt || !chooseVisa) return;

        const panelReceipt = document.getElementById("panel-receipt");
        const panelVisa = document.getElementById("panel-visa");
        const receipt = document.getElementById("receipt");
        const receiptOk = document.getElementById("receipt-ok");
        const visaFields = ["visa-name", "visa-number", "visa-expiry", "visa-cvv"].map((id) => document.getElementById(id));
        const visaOk = document.getElementById("visa-ok");

        function setMode(mode) {
            const receiptOn = mode === "receipt";
            chooseReceipt.classList.toggle("is-on", receiptOn);
            chooseVisa.classList.toggle("is-on", !receiptOn);
            panelReceipt.classList.toggle("is-off", !receiptOn);
            panelVisa.classList.toggle("is-off", receiptOn);
            receipt.disabled = !receiptOn;
            receiptOk.disabled = !receiptOn || !receipt.files.length || !!receiptProblem(receipt.files[0]);
            visaFields.forEach((field) => { field.disabled = receiptOn; });
            visaOk.disabled = receiptOn;
        }

        chooseReceipt.addEventListener("click", () => setMode("receipt"));
        chooseVisa.addEventListener("click", () => setMode("visa"));
        const fileError = document.getElementById("pay-file-error");
        const lang = () => document.documentElement.lang === "en" ? "en" : "ar";
        function receiptProblem(file) {
            if (!file) return "";
            if (file.size > 20 * 1024 * 1024) {
                return lang() === "ar" ? "الحد الأقصى 20 ميغابايت." : "Maximum size is 20 MB.";
            }
            const name = file.name || "";
            const type = file.type || "";
            const ok = /\.(jpe?g|jfif|png|webp|gif|bmp|tiff?|heic|heif|avif|pdf)$/i.test(name)
                || (type.startsWith("image/") && type !== "image/svg+xml")
                || type === "application/pdf"
                || type === "application/x-pdf";
            if (ok) return "";
            return lang() === "ar"
                ? "الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF."
                : "Accepted formats: JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, and PDF.";
        }
        receipt.addEventListener("change", () => {
            const problem = receipt.files[0] ? receiptProblem(receipt.files[0]) : "";
            if (fileError) {
                fileError.hidden = !problem;
                fileError.textContent = problem;
            }
            receiptOk.disabled = !receipt.files.length || !!problem;
        });
        panelReceipt.addEventListener("submit", (event) => {
            const problem = receipt.files[0] ? receiptProblem(receipt.files[0]) : "";
            if (!problem) return;
            event.preventDefault();
            if (fileError) {
                fileError.hidden = false;
                fileError.textContent = problem;
            }
        });
        setMode("receipt");
    })();
</script>
