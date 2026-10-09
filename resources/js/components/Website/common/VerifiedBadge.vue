<template>
    <div
        v-if="showBadge"
        class="verified-badge"
        :class="{ 'verified-badge--inline': inline, 'verified-badge--compact': compact }"
    >
        <img
            src="/assets_website/icons/verified-badge.svg"
            class="verified-badge-icon"
            alt=""
            aria-hidden="true"
        />
        <span class="verified-badge-text">{{ badgeLabel }}</span>
    </div>
</template>

<script>
import { isIdentityVerified } from "@/utils/userVerification";

export default {
    name: "VerifiedBadge",
    props: {
        user: { type: Object, default: null },
        verified: { type: Boolean, default: null },
        inline: { type: Boolean, default: false },
        compact: { type: Boolean, default: false },
        label: { type: String, default: "" },
    },
    computed: {
        showBadge() {
            if (this.verified !== null) {
                return this.verified;
            }
            return isIdentityVerified(this.user);
        },
        badgeLabel() {
            if (this.label) {
                return this.label;
            }
            const t = this.$store?.getters?.getTranslations || {};
            return t.Verified || "Verified";
        },
    },
};
</script>

<style scoped>
.verified-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin: 8px auto 12px;
    width: fit-content;
}

.verified-badge--inline {
    display: inline-flex;
    margin: 0 0 0 8px;
    vertical-align: middle;
}

.verified-badge--compact {
    gap: 4px;
    margin: 0 0 0 6px;
}

.verified-badge-icon {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    display: block;
}

.verified-badge--compact .verified-badge-icon {
    width: 18px;
    height: 18px;
}

.verified-badge-text {
    font-size: 15px;
    font-weight: 500;
    color: #111111;
    line-height: 1;
    letter-spacing: 0;
}

.verified-badge--compact .verified-badge-text {
    font-size: 14px;
}

.verified-badge--inline .verified-badge-text {
    font-size: 14px;
}
</style>
