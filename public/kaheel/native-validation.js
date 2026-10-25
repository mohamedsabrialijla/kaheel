(function () {
    var copy = {
        ar: {
            required: "هذا الحقل مطلوب.",
            email: "أدخل بريداً إلكترونياً صحيحاً.",
            url: "أدخل رابطاً صحيحاً.",
            number: "أدخل رقماً صحيحاً.",
            min: "القيمة يجب أن تكون {n} أو أكثر.",
            max: "القيمة يجب أن تكون {n} أو أقل.",
            minlength: "يجب ألا يقل عن {n} أحرف.",
            maxlength: "يجب ألا يزيد عن {n} أحرف.",
            step: "أدخل قيمة صحيحة.",
            pattern: "الصيغة غير صحيحة.",
            bad: "أدخل قيمة صحيحة."
        },
        en: {
            required: "This value is required.",
            email: "Please enter a valid email.",
            url: "Please enter a valid URL.",
            number: "Please enter a valid number.",
            min: "Value must be {n} or more.",
            max: "Value must be {n} or less.",
            minlength: "Please use at least {n} characters.",
            maxlength: "Please use no more than {n} characters.",
            step: "Please enter a valid value.",
            pattern: "Please match the requested format.",
            bad: "Please enter a valid value."
        }
    };

    function isAr() {
        return String(document.documentElement.lang || "ar").toLowerCase().indexOf("en") !== 0;
    }

    function text(key, n) {
        var value = (isAr() ? copy.ar : copy.en)[key] || "";
        return n == null ? value : value.replace("{n}", n);
    }

    function messageFor(el) {
        var v = el.validity;
        if (!v || v.valid) return "";
        if (v.valueMissing) return text("required");
        if (v.typeMismatch) {
            if (el.type === "email") return text("email");
            if (el.type === "url") return text("url");
            return text("number");
        }
        if (v.badInput) return text("number");
        if (v.rangeUnderflow) return text("min", el.min);
        if (v.rangeOverflow) return text("max", el.max);
        if (v.tooShort) return text("minlength", el.minLength);
        if (v.tooLong) return text("maxlength", el.maxLength);
        if (v.stepMismatch) return text("step");
        if (v.patternMismatch) return text("pattern");
        return text("required");
    }

    function localize(el) {
        if (!el || typeof el.setCustomValidity !== "function") return;
        el.setCustomValidity(messageFor(el));
    }

    function clear(el) {
        if (el && typeof el.setCustomValidity === "function") el.setCustomValidity("");
    }

    document.addEventListener("invalid", function (event) {
        localize(event.target);
    }, true);

    document.addEventListener("input", function (event) {
        clear(event.target);
    }, true);

    document.addEventListener("change", function (event) {
        clear(event.target);
    }, true);
})();
