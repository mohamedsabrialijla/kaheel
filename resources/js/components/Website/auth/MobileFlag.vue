<template>
    <div>
        <input
            ref="phoneInput"
            type="tel"
            class="form-control"
            :placeholder="placeholder"
        />
    </div>
</template>

<script setup>
import { onMounted, ref, defineEmits, defineProps, watch, computed } from "vue";
import intlTelInput from "intl-tel-input";
import "intl-tel-input/build/css/intlTelInput.css";

const props = defineProps({
    modelValue: String,
    initialCountry: {
        type: String,
        default: "sa",
    },
    placeholder: {
        type: String,
        default: "Enter phone number",
    },
    items: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["update:modelValue","country-changed"]);
const phoneInput = ref(null);
let iti = null;

// Filter the country list of intl-tel-input to only those in props.items
const allowedCountryCodes = computed(() =>
    props.items.map((item) => item.code)
);

onMounted(() => {
    iti = intlTelInput(phoneInput.value, {
        initialCountry: props.initialCountry,
        nationalMode: false,
        separateDialCode: true,
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        onlyCountries:
            props.items.length > 0
                ? props.items.map((item) => item.slug.toLowerCase())
                : undefined,
    });

    const waitForUtils = setInterval(() => {
        if (iti.getNumber) {
            clearInterval(waitForUtils);

            phoneInput.value.addEventListener("input", () => {
                const countryCode = iti.getSelectedCountryData().dialCode;
                let numberWithoutCode = phoneInput.value.value;

                if (numberWithoutCode.startsWith(countryCode)) {
                    numberWithoutCode = numberWithoutCode.replace(
                        countryCode,
                        ""
                    );
                }

                const fullNumber = countryCode + numberWithoutCode;
                emit("update:modelValue", fullNumber);
            });


            phoneInput.value.addEventListener("countrychange", () => {
                const countryData = iti.getSelectedCountryData();
                const dialCode = countryData.dialCode;
                emit("country-changed", dialCode);
            });
        }
    }, 200);
});
</script>

<style>
.iti {
    width: 100% !important;
    display: block !important;
}

.iti input {
    width: 100% !important;
    box-sizing: border-box;
}
</style>
