<template>
    <span
        v-if="displayName"
        class="user-name-link user-profile-link"
        :class="linkClass"
        role="link"
        tabindex="0"
        @click.stop="openProfile"
        @keyup.enter.stop="openProfile"
    >{{ displayName }}</span>
</template>

<script>
import { navigateToUserProfile, parseUserId } from "@/utils/userProfile";

export default {
    name: "UserNameLink",
    props: {
        user: { type: Object, default: null },
        name: { type: String, default: "" },
        userId: { type: [Number, String], default: null },
        linkClass: { type: [String, Object, Array], default: "" },
    },
    computed: {
        displayName() {
            return (this.name || this.user?.name || "").trim();
        },
        targetId() {
            return parseUserId(this.userId ?? this.user?.id);
        },
    },
    methods: {
        openProfile() {
            if (!this.targetId) {
                return;
            }
            const locale =
                this.$route?.params?.locale ||
                localStorage.getItem("locale") ||
                "en";
            const currentUser = this.$store?.getters?.getUser || null;
            navigateToUserProfile(this.$router, locale, currentUser, {
                id: this.targetId,
                name: this.displayName,
            });
        },
    },
};
</script>

<style scoped>
.user-name-link {
    cursor: pointer;
}
.user-name-link:hover {
    color: #e5aa2a !important;
    text-decoration: underline;
}
</style>
