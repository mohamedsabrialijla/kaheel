<template>
    <div class="terms-container">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h1 class="hero-title">
                                {{
                                    locale === "ar"
                                        ? "الشروط والأحكام"
                                        : "Terms and Conditions"
                                }}
                            </h1>
                            <p class="hero-description">
                                {{
                                    locale === "ar"
                                        ? "يرجى قراءة هذه الشروط والأحكام بعناية قبل استخدام منصتنا. استخدامك للمنصة يعني موافقتك على هذه الشروط."
                                        : "Please read these Terms and Conditions carefully before using our platform. Your use of the platform constitutes your agreement to these terms."
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">
                                    {{
                                        locale === "ar"
                                            ? "آخر تحديث"
                                            : "Last Updated"
                                    }}
                                </div>
                                <div class="stat-label">
                                    {{
                                        locale === "ar"
                                            ? "يناير 2025"
                                            : "January 2025"
                                    }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">
                                    {{
                                        locale === "ar" ? "الإصدار" : "Version"
                                    }}
                                </div>
                                <div class="stat-label">2.1</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">
                                    {{ locale === "ar" ? "الحالة" : "Status" }}
                                </div>
                                <div class="stat-label">
                                    {{ locale === "ar" ? "نشط" : "Active" }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Table of Contents Section -->
        <section class="toc-section">
            <div class="container">
                <div class="toc-card">
                    <h3 class="toc-title">
                        {{
                            locale === "ar"
                                ? "جدول المحتويات"
                                : "Table of Contents"
                        }}
                    </h3>
                    <div class="toc-list">
                        <div
                            class="toc-item"
                            v-for="(section, index) in tableOfContents"
                            :key="index"
                            @click="scrollToSection(section?.id)"
                        >
                            <span class="toc-number">{{ index + 1 }}</span>
                            <span class="toc-text">{{ section?.title }}</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Terms Content Section -->
        <section class="terms-content-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <!-- Dynamic Content from API -->
                        <div
                            v-if="currentDescription"
                            class="dynamic-content"
                            v-html="currentDescription"
                        ></div>

                       
                    </div>
                </div>
            </div>
        </section>

        <!-- Important Notes Section -->
        <section class="notes-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="notes-card">
                            <div class="notes-header">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h3>
                                    {{
                                        locale === "ar"
                                            ? "ملاحظات مهمة"
                                            : "Important Notes"
                                    }}
                                </h3>
                            </div>
                            <div class="notes-content">
                                <div
                                    v-for="(note, index) in importantNotes"
                                    :key="index"
                                    class="note-item"
                                >
                                    <div class="note-icon">
                                        <i :class="note.icon"></i>
                                    </div>
                                    <div class="note-text">
                                        <h4>{{ note.title }}</h4>
                                        <p>{{ note.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-card text-center">
                    <h3 class="titlefoote">
                        {{
                            locale === "ar"
                                ? "هل لديك أسئلة حول الشروط والأحكام؟"
                                : "Have Questions About Terms and Conditions?"
                        }}
                    </h3>
                    <p>
                        {{
                            locale === "ar"
                                ? "إذا كان لديك أي استفسارات حول هذه الشروط، لا تتردد في التواصل معنا"
                                : "If you have any questions about these terms, don't hesitate to contact us"
                        }}
                    </p>
                    <div class="contact-buttons">
                        <router-link
                            :to="`/${locale}/contact-us`"
                            class="btn btn-primary btn-lg"
                        >
                            {{ locale === "ar" ? "تواصل معنا" : "Contact Us" }}
                        </router-link>
                       <!--  <button
                            class="btn btn-outline-primary btn-lg"
                            @click="downloadTerms"
                        >
                            <i class="fas fa-download"></i>
                            {{ locale === "ar" ? "تحميل PDF" : "Download PDF" }}
                        </button> -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from "vuex";
import { api } from "../../../axiosUser";

export default {
    name: "Terms",
    data() {
        return {
            isLoading: false,
            items: [],
            tableOfContents: [
                {
                    id: "privacy",
                    title: this.locale === "ar" ? "الخصوصية وحماية البيانات" : "Privacy and Data Protection",
                },
                {
                    id: "verification",
                    title: this.locale === "ar" ? "توثيق الحسابات" : "User Verification",
                },
                {
                    id: "commission",
                    title: this.locale === "ar" ? "العمولة والرسوم" : "Commission and Fees",
                },
                {
                    id: "role",
                    title: this.locale === "ar" ? "دور براندي كوسيط" : "Brandy’s Role as a Platform",
                },
                {
                    id: "seller-responsibilities",
                    title: this.locale === "ar" ? "مسؤوليات البائع" : "Seller Responsibilities",
                },
                {
                    id: "buyer-responsibilities",
                    title: this.locale === "ar" ? "مسؤوليات المشتري" : "Buyer Responsibilities",
                },
                {
                    id: "usage",
                    title: this.locale === "ar" ? "استخدام الموقع" : "Use of the Platform",
                },
                {
                    id: "intellectual-property",
                    title: this.locale === "ar" ? "حقوق الملكية الفكرية" : "Intellectual Property",
                },
                {
                    id: "modifications",
                    title: this.locale === "ar" ? "تعديلات على الشروط والسياسات" : "Modifications to Terms and Policies",
                },
            ],
            
            importantNotes: [
                {
                    icon: "fas fa-shield-alt",
                    title: this.locale === "ar" ? "الأمان" : "Security",
                    description:
                        this.locale === "ar"
                            ? "نحن نلتزم بحماية أمان وخصوصية بياناتك. نستخدم تقنيات تشفير متقدمة لحماية معلوماتك."
                            : "We are committed to protecting the security and privacy of your data. We use advanced encryption technologies to protect your information.",
                },
                {
                    icon: "fas fa-gavel",
                    title:
                        this.locale === "ar"
                            ? "الامتثال القانوني"
                            : "Legal Compliance",
                    description:
                        this.locale === "ar"
                            ? "جميع أنشطتنا تخضع للقوانين واللوائح المحلية والدولية المعمول بها."
                            : "All our activities are subject to applicable local and international laws and regulations.",
                },
                {
                    icon: "fas fa-handshake",
                    title: this.locale === "ar" ? "الشفافية" : "Transparency",
                    description:
                        this.locale === "ar"
                            ? "نحن نؤمن بالشفافية في جميع تعاملاتنا. سنواصل إعلامك بأي تغييرات مهمة في خدماتنا."
                            : "We believe in transparency in all our dealings. We will continue to inform you of any important changes to our services.",
                },
            ],
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {
            this.fetchItems();
        });
    },

    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        setting() {
            return this.$store.getters.getSetting;
        },

        translations() {
            return this.$store.getters.getTranslations || {};
        },

       currentDescription() {
        // تأكد أن items و translations موجودة
        if (!this.items || !this.items.translations) return ""; 
        
        const current = this.items.translations.find(
            t => t.locale === this.locale
        );

        return current ? current.description : this.items.description || "";
    },


       
        tableOfContents() {
            return [
                {
                    id: "privacy",
                    title: this.locale === "ar" ? "الخصوصية وحماية البيانات" : "Privacy and Data Protection",
                },
                {
                    id: "verification",
                    title: this.locale === "ar" ? "توثيق الحسابات" : "User Verification",
                },
                {
                    id: "commission",
                    title: this.locale === "ar" ? "العمولة والرسوم" : "Commission and Fees",
                },
                {
                    id: "role",
                    title: this.locale === "ar" ? "دور براندي كوسيط" : "Brandy’s Role as a Platform",
                },
                {
                    id: "seller-responsibilities",
                    title: this.locale === "ar" ? "مسؤوليات البائع" : "Seller Responsibilities",
                },
                {
                    id: "buyer-responsibilities",
                    title: this.locale === "ar" ? "مسؤوليات المشتري" : "Buyer Responsibilities",
                },
                {
                    id: "usage",
                    title: this.locale === "ar" ? "استخدام الموقع" : "Use of the Platform",
                },
                {
                    id: "intellectual-property",
                    title: this.locale === "ar" ? "حقوق الملكية الفكرية" : "Intellectual Property",
                },
                {
                    id: "modifications",
                    title: this.locale === "ar" ? "تعديلات على الشروط والسياسات" : "Modifications to Terms and Policies",
                },
            ]
        },
            
        importantNotes() {
        return [
            {
                icon: "fas fa-shield-alt",
                title: this.locale === "ar" ? "الأمان" : "Security",
                description:
                    this.locale === "ar"
                        ? "نحن نلتزم بحماية أمان وخصوصية بياناتك. نستخدم تقنيات تشفير متقدمة لحماية معلوماتك."
                        : "We are committed to protecting the security and privacy of your data. We use advanced encryption technologies to protect your information.",
            },
            {
                icon: "fas fa-gavel",
                title:
                    this.locale === "ar"
                        ? "الامتثال القانوني"
                        : "Legal Compliance",
                description:
                    this.locale === "ar"
                        ? "جميع أنشطتنا تخضع للقوانين واللوائح المحلية والدولية المعمول بها."
                        : "All our activities are subject to applicable local and international laws and regulations.",
            },
            {
                icon: "fas fa-handshake",
                title: this.locale === "ar" ? "الشفافية" : "Transparency",
                description:
                    this.locale === "ar"
                        ? "نحن نؤمن بالشفافية في جميع تعاملاتنا. سنواصل إعلامك بأي تغييرات مهمة في خدماتنا."
                        : "We believe in transparency in all our dealings. We will continue to inform you of any important changes to our services.",
            },
        ]
        },
    },

    watch: {
        locale(newLocale) {
            this.loadTranslations(newLocale); // يعيد تحميل الترجمات من DB
        }
    },

    methods: {
        

        scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: "smooth" });
            }
        },

        downloadTerms() {
            // محاكاة تحميل ملف PDF
            const link = document.createElement("a");
            link.href = "#";
            link.download =
                this.locale === "ar"
                    ? "الشروط والأحكام.pdf"
                    : "Terms_and_Conditions.pdf";
            link.click();
        },

        swalFunction(type, text) {
            Swal.fire({
                text: text,
                icon: type,
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        },

        async fetchItems() {
            this.isLoading = true;
            await axios
                .get("Pages/getById", {
                    params: {
                        ID: 4,
                    },
                })
                .then((response) => {
                    this.items = response.data.items;
                    this.isLoading = false;
                    console.log(this.items);
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },
    },
};
</script>

<style scoped>
.terms-container {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #e5aa2a 0%, #f4c430 100%);
    padding: 80px 0;
    color: #1a2733;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    color: #1a2733;
}

.hero-description {
    font-size: 1.2rem;
    line-height: 1.8;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.hero-stats {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
    justify-content: end;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.2rem;
    font-weight: bold;
    color: #1a2733;
}

.stat-label {
    font-size: 0.9rem;
    color: #2c3e50;
    margin-top: 0.5rem;
}

.hero-image img {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Table of Contents Section */
.toc-section {
    padding: 40px 0;
    background: #f8f9fa;
}

.toc-card {
    background: #fff;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.toc-title {
    font-size: 1.8rem;
    font-weight: bold;
    color: #1a2733;
    margin-bottom: 1.5rem;
    text-align: center;
}

.toc-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.toc-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toc-item:hover {
    background: #e5aa2a;
    color: #fff;
    transform: translateY(-2px);
}

.toc-number {
    width: 30px;
    height: 30px;
    background: #e5aa2a;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.toc-item:hover .toc-number {
    background: #fff;
    color: #e5aa2a;
}

.toc-text {
    flex: 1;
    font-weight: 500;
}

.toc-item i {
    color: #e5aa2a;
    transition: color 0.3s ease;
}

.toc-item:hover i {
    color: #fff;
}

/* Terms Content Section */
.terms-content-section {
    padding: 80px 0;
    background: #fff;
}

.dynamic-content {
    background: #fff;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    line-height: 1.8;
    color: #2c3e50;
}

.terms-sections {
    background: #fff;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.terms-section {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.terms-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 1.8rem;
    font-weight: bold;
    color: #1a2733;
}

.section-number {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #e5aa2a, #f4c430);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.subsection {
    margin-bottom: 2rem;
}

.subsection-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1a2733;
    margin-bottom: 1rem;
}

.subsection-content p {
    font-size: 1rem;
    line-height: 1.8;
    color: #2c3e50;
    margin-bottom: 1rem;
}

/* Important Notes Section */
.notes-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.notes-card {
    background: #fff;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.notes-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.notes-header i {
    font-size: 2rem;
    color: #e5aa2a;
}

.notes-header h3 {
    font-size: 1.8rem;
    font-weight: bold;
    color: #1a2733;
    margin: 0;
}

.notes-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.note-item {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.note-item:hover {
    transform: translateY(-5px);
}

.note-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #e5aa2a, #f4c430);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.note-icon i {
    font-size: 1.2rem;
    color: #fff;
}

.note-text h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a2733;
    margin-bottom: 0.5rem;
}

.note-text p {
    font-size: 0.95rem;
    line-height: 1.6;
    color: #2c3e50;
    margin: 0;
}

/* Contact Section */
.contact-section {
    padding: 80px 0;
    background: #1a2733;
    color: #fff;
}

.contact-card h3 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.contact-card p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.contact-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-primary {
    background: #e5aa2a;
    border-color: #e5aa2a;
    color: #1a2733;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #d49a1a;
    border-color: #d49a1a;
    transform: translateY(-2px);
}

.btn-outline-primary {
    border-color: #e5aa2a;
    color: #e5aa2a;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #e5aa2a;
    border-color: #e5aa2a;
    color: #1a2733;
    transform: translateY(-2px);
}
.titlefoote {
    color: #e5aa2a;
}
/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }

    .toc-list {
        grid-template-columns: 1fr;
    }

    .notes-content {
        grid-template-columns: 1fr;
    }

    .contact-buttons {
        flex-direction: column;
        align-items: center;
    }
}

/* RTL Support */
[dir="rtl"] .hero-stats {
    flex-direction: row-reverse;
}

[dir="rtl"] .toc-item {
    flex-direction: row-reverse;
}

[dir="rtl"] .note-item {
    flex-direction: row-reverse;
}
</style>
