<template>
    <footer class="footer-bar"> 
        <div class="footer-links">
            <router-link :to="`/${locale}/terms`"
                >{{ translations?.terms_and_conditions || "terms and conditions" }}</router-link
            >
            <router-link :to="`/${locale}/about-us`">{{ translations?.AboutUS || "about us" }}</router-link>
            <router-link :to="`/${locale}/contact-us`">{{ translations?.ContactUs || "Contact Us" }}</router-link>
        </div>
    </footer>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from "vuex";
import { api } from "../../../axiosUser";

export default {
    name: "FooterWebsite",
    data() {
        return {
            success: 0,
            // t: window.translations,
        };
    },
    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {});
    },

    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        setting() {
            return this.$store.getters.getSetting;
        },

        translations() {
            return this.$store.getters.getTranslations;
        },
    },

    methods: {},
};
</script>

<style scoped>
.footer-bar {
    width: 100%;
    background: #e5aa2a;
    padding: 0.7rem 0;
    display: flex;
    justify-content: center;
}
.footer-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2.5rem;
}
.footer-links a {
    color: #fff;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    padding: 2px 10px;
    border-radius: 6px;
}
.footer-links a:hover {
    background: rgba(255, 255, 255, 0.13);
    color: #fff;
}
</style>
