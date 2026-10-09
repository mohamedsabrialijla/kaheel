<template>
    <div v-if="visible" class="login-required-backdrop" @click.self="cancel">
        <div class="login-required-modal" role="dialog" aria-modal="true">
            <div class="login-required-icon">
                <i class="fas fa-user-lock"></i>
            </div>
            <h3 class="login-required-title">
                {{ translations?.login_required_title || "يجب تسجيل الدخول" }}
            </h3>
            <p class="login-required-text">
                {{
                    translations?.login_required_message ||
                    "يجب عليك تسجيل الدخول للمتابعة."
                }}
            </p>
            <div class="login-required-actions">
                <button type="button" class="login-required-cancel" @click="cancel">
                    {{ translations?.Cancel || "Cancel" }}
                </button>
                <button type="button" class="login-required-ok" @click="confirm">
                    {{ translations?.Ok || translations?.login || "OK" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoginRequiredModal",
    computed: {
        visible() {
            return !!this.$store.state.loginRequiredVisible;
        },
        translations() {
            return this.$store.getters.getTranslations || {};
        },
        locale() {
            return this.$route?.params?.locale || localStorage.getItem("locale") || "en";
        },
    },
    methods: {
        cancel() {
            this.$store.commit("hideLoginRequired");
        },
        confirm() {
            this.$store.commit("hideLoginRequired");
            this.$router.push({ path: `/${this.locale}/login` });
        },
    },
};
</script>

<style scoped>
.login-required-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, 0.45);
    z-index: 10050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.login-required-modal {
    width: 100%;
    max-width: 380px;
    background: #fff;
    border-radius: 16px;
    padding: 28px 24px 22px;
    text-align: center;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.18);
}
.login-required-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 14px;
    border-radius: 50%;
    background: #fff8e8;
    color: #e5aa2a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
}
.login-required-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a2733;
    margin: 0 0 8px;
}
.login-required-text {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0 0 22px;
    line-height: 1.5;
}
.login-required-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
.login-required-cancel,
.login-required-ok {
    min-width: 110px;
    border-radius: 999px;
    padding: 10px 18px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}
.login-required-cancel {
    background: #f3f4f6;
    color: #374151;
}
.login-required-ok {
    background: #e5aa2a;
    color: #fff;
}
</style>
