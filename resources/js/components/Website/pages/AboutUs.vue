<template>
    <div class="about-us-container">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h1 class="hero-title">
                                {{ locale === "ar" ? "من نحن" : "About Us" }}
                            </h1>
                            <p class="hero-description">
                                {{
                                    locale === "ar"
                                        ? "نحن منصة رائدة في مجال الإعلانات والتسويق الرقمي، نساعد الشركات والأفراد على الوصول إلى جمهورهم المستهدف بطرق مبتكرة وفعالة."
                                        : "We are a leading platform in digital advertising and marketing, helping companies and individuals reach their target audience through innovative and effective methods."
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">10K+</div>
                                <div class="stat-label">
                                    {{
                                        locale === "ar"
                                            ? "مستخدم نشط"
                                            : "Active Users"
                                    }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">50K+</div>
                                <div class="stat-label">
                                    {{
                                        locale === "ar"
                                            ? "إعلان منشور"
                                            : "Published Ads"
                                    }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">95%</div>
                                <div class="stat-label">
                                    {{
                                        locale === "ar"
                                            ? "معدل رضا العملاء"
                                            : "Customer Satisfaction"
                                    }}
                                </div>
                            </div>
                        </div>
                        <!-- <div class="hero-image">
                            <img
                                src="/assets/about-hero.png"
                                alt="About Us"
                                class="img-fluid rounded-4"
                            />
                        </div> -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="mission-vision-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="mission-card">
                            <div class="card-icon">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h3>
                                {{ locale === "ar" ? "مهمتنا" : "Our Mission" }}
                            </h3>
                            <p>
                                {{
                                    locale === "ar"
                                        ? "تمكين الشركات والأفراد من الوصول إلى جمهورهم المستهدف من خلال منصة إعلانية متطورة وسهلة الاستخدام، مع توفير أدوات تحليلية متقدمة لتحسين نتائج الحملات الإعلانية."
                                        : "Empowering companies and individuals to reach their target audience through an advanced and user-friendly advertising platform, while providing advanced analytical tools to improve advertising campaign results."
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="vision-card">
                            <div class="card-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3>
                                {{ locale === "ar" ? "رؤيتنا" : "Our Vision" }}
                            </h3>
                            <p>
                                {{
                                    locale === "ar"
                                        ? "أن نكون المنصة الإعلانية الأولى في المنطقة، ونقود التحول الرقمي في مجال التسويق والإعلانات، مع توفير حلول مبتكرة تلبي احتياجات العملاء المتطورة."
                                        : "To be the leading advertising platform in the region, leading the digital transformation in marketing and advertising, while providing innovative solutions that meet evolving customer needs."
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section">
            <div class="container">
                <div class="section-header text-center">
                    <h2>
                        {{
                            locale === "ar"
                                ? "قيمنا الأساسية"
                                : "Our Core Values"
                        }}
                    </h2>
                    <p>
                        {{
                            locale === "ar"
                                ? "القيم التي تقودنا نحو التميز والنجاح"
                                : "The values that drive us towards excellence and success"
                        }}
                    </p>
                </div>
                <div class="row g-4">
                    <div
                        class="col-lg-3 col-md-6"
                        v-for="value in values"
                        :key="value.id"
                    >
                        <div class="value-card">
                            <div class="value-icon">
                                <i :class="value.icon"></i>
                            </div>
                            <h4>{{ value.title }}</h4>
                            <p>{{ value.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <!-- <section class="team-section">
            <div class="container">
                <div class="section-header text-center">
                    <h2>{{ locale === "ar" ? "فريق العمل" : "Our Team" }}</h2>
                    <p>
                        {{
                            locale === "ar"
                                ? "تعرف على الفريق المتميز الذي يقف وراء نجاحنا"
                                : "Meet the exceptional team behind our success"
                        }}
                    </p>
                </div>
                <div class="row g-4">
                    <div
                        class="col-lg-3 col-md-6"
                        v-for="member in teamMembers"
                        :key="member.id"
                    >
                        <div class="team-card">
                            <div class="member-image">
                                <img :src="member.image" :alt="member.name" />
                            </div>
                            <div class="member-info">
                                <h4>{{ member.name }}</h4>
                                <p class="member-position">
                                    {{ member.position }}
                                </p>
                                <p class="member-description">
                                    {{ member.description }}
                                </p>
                                <div class="social-links">
                                    <a href="#" class="social-link"
                                        ><i class="fab fa-linkedin"></i
                                    ></a>
                                    <a href="#" class="social-link"
                                        ><i class="fab fa-twitter"></i
                                    ></a>
                                    <a href="#" class="social-link"
                                        ><i class="fas fa-envelope"></i
                                    ></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
 -->
        <!-- Timeline Section -->
        <section class="timeline-section">
            <div class="container">
                <div class="section-header text-center">
                    <h2>
                        {{ locale === "ar" ? "رحلة النجاح" : "Our Journey" }}
                    </h2>
                    <p>
                        {{
                            locale === "ar"
                                ? "مراحل تطورنا عبر السنوات"
                                : "Our development stages over the years"
                        }}
                    </p>
                </div>
                <div class="timeline">
                    <div
                        class="timeline-item"
                        v-for="milestone in milestones"
                        :key="milestone.id"
                    >
                        <div class="timeline-content">
                            <div class="timeline-year">
                                {{ milestone.year }}
                            </div>
                            <h4>{{ milestone.title }}</h4>
                            <p>{{ milestone.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content text-center">
                    <h2 class="titlefoote">
                        {{
                            locale === "ar"
                                ? "ابدأ رحلتك معنا اليوم"
                                : "Start Your Journey With Us Today"
                        }}
                    </h2>
                    <p>
                        {{
                            locale === "ar"
                                ? "انضم إلى آلاف العملاء الراضين واستفد من منصتنا المتطورة"
                                : "Join thousands of satisfied customers and benefit from our advanced platform"
                        }}
                    </p>
                    <div class="cta-buttons">
                        <router-link
                            :to="`/${locale}/signUp`"
                            class="btn btn-primary btn-lg"
                        >
                            {{ locale === "ar" ? "ابدأ الآن" : "Get Started" }}
                        </router-link>
                        <router-link
                            :to="`/${locale}/contact-us`"
                            class="btn btn-outline-primary btn-lg"
                        >
                            {{ locale === "ar" ? "تواصل معنا" : "Contact Us" }}
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: "AboutUs",
    data() {
        return {
            values: [
                {
                    id: 1,
                    icon: "fas fa-lightbulb",
                    title: this.locale === "ar" ? "الابتكار" : "Innovation",
                    description:
                        this.locale === "ar"
                            ? "نبحث دائماً عن طرق جديدة ومبتكرة لتحسين خدماتنا وتجربة المستخدم"
                            : "We constantly seek new and innovative ways to improve our services and user experience",
                },
                {
                    id: 2,
                    icon: "fas fa-shield-alt",
                    title: this.locale === "ar" ? "الموثوقية" : "Reliability",
                    description:
                        this.locale === "ar"
                            ? "نلتزم بتقديم خدمات موثوقة وآمنة لجميع عملائنا"
                            : "We are committed to providing reliable and secure services to all our customers",
                },
                {
                    id: 3,
                    icon: "fas fa-users",
                    title: this.locale === "ar" ? "العمل الجماعي" : "Teamwork",
                    description:
                        this.locale === "ar"
                            ? "نؤمن بقوة العمل الجماعي والتعاون لتحقيق الأهداف المشتركة"
                            : "We believe in the power of teamwork and collaboration to achieve common goals",
                },
                {
                    id: 4,
                    icon: "fas fa-heart",
                    title: this.locale === "ar" ? "الشغف" : "Passion",
                    description:
                        this.locale === "ar"
                            ? "نعمل بشغف لتقديم أفضل الخدمات وتحقيق رضا العملاء"
                            : "We work with passion to provide the best services and achieve customer satisfaction",
                },
            ],
            teamMembers: [
                {
                    id: 1,
                    name: this.locale === "ar" ? "أحمد محمد" : "Ahmed Mohamed",
                    position: this.locale === "ar" ? "الرئيس التنفيذي" : "CEO",
                    description:
                        this.locale === "ar"
                            ? "خبرة 15 عام في مجال التكنولوجيا والابتكار"
                            : "15 years of experience in technology",
                    image: "https://randomuser.me/api/portraits/men/32.jpg",
                },
                {
                    id: 2,
                    name: this.locale === "ar" ? "سارة أحمد" : "Sara Ahmed",
                    position:
                        this.locale === "ar"
                            ? "مدير التطوير"
                            : "Development Manager",
                    description:
                        this.locale === "ar"
                            ? "متخصصة في تطوير المنتجات الرقمية"
                            : "Specialized in digital product development",
                    image: "https://randomuser.me/api/portraits/women/44.jpg",
                },
                {
                    id: 3,
                    name: this.locale === "ar" ? "محمد علي" : "Mohamed Ali",
                    position:
                        this.locale === "ar"
                            ? "مدير التسويق"
                            : "Marketing Manager",
                    description:
                        this.locale === "ar"
                            ? "خبير في استراتيجيات التسويق الرقمي"
                            : "Expert in digital marketing strategies",
                    image: "https://randomuser.me/api/portraits/men/67.jpg",
                },
                {
                    id: 4,
                    name: this.locale === "ar" ? "فاطمة حسن" : "Fatima Hassan",
                    position:
                        this.locale === "ar"
                            ? "مدير خدمة العملاء"
                            : "Customer Service Manager",
                    description:
                        this.locale === "ar"
                            ? "متخصصة في رضا العملاء وتجربة المستخدم"
                            : "Specialized in customer satisfaction and UX",
                    image: "https://randomuser.me/api/portraits/women/65.jpg",
                },
            ],
            milestones: [
                {
                    id: 1,
                    year: "2022",
                    title:
                        this.locale === "ar"
                            ? "بداية المشروع"
                            : "Project Start",
                    description:
                        this.locale === "ar"
                            ? "تم إطلاق المنصة مع مجموعة أساسية من الميزات"
                            : "Platform launched with a core set of features",
                },
                {
                    id: 2,
                    year: "2023",
                    title:
                        this.locale === "ar" ? "النمو السريع" : "Rapid Growth",
                    description:
                        this.locale === "ar"
                            ? "وصل عدد المستخدمين إلى 10,000 مستخدم نشط"
                            : "Reached 10,000 active users",
                },
                {
                    id: 3,
                    year: "2024",
                    title:
                        this.locale === "ar"
                            ? "التوسع الإقليمي"
                            : "Regional Expansion",
                    description:
                        this.locale === "ar"
                            ? "تم التوسع إلى 5 دول جديدة في المنطقة"
                            : "Expanded to 5 new countries in the region",
                },
                {
                    id: 4,
                    year: "2025",
                    title:
                        this.locale === "ar"
                            ? "الابتكار المستمر"
                            : "Continuous Innovation",
                    description:
                        this.locale === "ar"
                            ? "إطلاق ميزات الذكاء الاصطناعي والتحليلات المتقدمة"
                            : "Launched AI features and advanced analytics",
                },
            ],
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },
    },
    watch: {
        locale() {
            this.updateContent();
        },
    },
    mounted() {
        this.updateContent();
    },
    methods: {
        updateContent() {
            // تحديث المحتوى حسب اللغة
            this.values = [
                {
                    id: 1,
                    icon: "fas fa-lightbulb",
                    title: this.locale === "ar" ? "الابتكار" : "Innovation",
                    description:
                        this.locale === "ar"
                            ? "نبحث دائماً عن طرق جديدة ومبتكرة لتحسين خدماتنا وتجربة المستخدم"
                            : "We constantly seek new and innovative ways to improve our services and user experience",
                },
                {
                    id: 2,
                    icon: "fas fa-shield-alt",
                    title: this.locale === "ar" ? "الموثوقية" : "Reliability",
                    description:
                        this.locale === "ar"
                            ? "نلتزم بتقديم خدمات موثوقة وآمنة لجميع عملائنا"
                            : "We are committed to providing reliable and secure services to all our customers",
                },
                {
                    id: 3,
                    icon: "fas fa-users",
                    title: this.locale === "ar" ? "العمل الجماعي" : "Teamwork",
                    description:
                        this.locale === "ar"
                            ? "نؤمن بقوة العمل الجماعي والتعاون لتحقيق الأهداف المشتركة"
                            : "We believe in the power of teamwork and collaboration to achieve common goals",
                },
                {
                    id: 4,
                    icon: "fas fa-heart",
                    title: this.locale === "ar" ? "الشغف" : "Passion",
                    description:
                        this.locale === "ar"
                            ? "نعمل بشغف لتقديم أفضل الخدمات وتحقيق رضا العملاء"
                            : "We work with passion to provide the best services and achieve customer satisfaction",
                },
            ];

            this.teamMembers = [
                {
                    id: 1,
                    name: this.locale === "ar" ? "أحمد محمد" : "Ahmed Mohamed",
                    position: this.locale === "ar" ? "الرئيس التنفيذي" : "CEO",
                    description:
                        this.locale === "ar"
                            ? "خبرة 15 عام في مجال التكنولوجيا والابتكار"
                            : "15 years of experience in technology",
                    image: "https://randomuser.me/api/portraits/men/32.jpg",
                },
                {
                    id: 2,
                    name: this.locale === "ar" ? "سارة أحمد" : "Sara Ahmed",
                    position:
                        this.locale === "ar"
                            ? "مدير التطوير"
                            : "Development Manager",
                    description:
                        this.locale === "ar"
                            ? "متخصصة في تطوير المنتجات الرقمية"
                            : "Specialized in digital product development",
                    image: "https://randomuser.me/api/portraits/women/44.jpg",
                },
                {
                    id: 3,
                    name: this.locale === "ar" ? "محمد علي" : "Mohamed Ali",
                    position:
                        this.locale === "ar"
                            ? "مدير التسويق"
                            : "Marketing Manager",
                    description:
                        this.locale === "ar"
                            ? "خبير في استراتيجيات التسويق الرقمي"
                            : "Expert in digital marketing strategies",
                    image: "https://randomuser.me/api/portraits/men/67.jpg",
                },
                {
                    id: 4,
                    name: this.locale === "ar" ? "فاطمة حسن" : "Fatima Hassan",
                    position:
                        this.locale === "ar"
                            ? "مدير خدمة العملاء"
                            : "Customer Service Manager",
                    description:
                        this.locale === "ar"
                            ? "متخصصة في رضا العملاء وتجربة المستخدم"
                            : "Specialized in customer satisfaction and UX",
                    image: "https://randomuser.me/api/portraits/women/65.jpg",
                },
            ];

            this.milestones = [
                {
                    id: 1,
                    year: "2022",
                    title:
                        this.locale === "ar"
                            ? "بداية المشروع"
                            : "Project Start",
                    description:
                        this.locale === "ar"
                            ? "تم إطلاق المنصة مع مجموعة أساسية من الميزات"
                            : "Platform launched with a core set of features",
                },
                {
                    id: 2,
                    year: "2023",
                    title:
                        this.locale === "ar" ? "النمو السريع" : "Rapid Growth",
                    description:
                        this.locale === "ar"
                            ? "وصل عدد المستخدمين إلى 10,000 مستخدم نشط"
                            : "Reached 10,000 active users",
                },
                {
                    id: 3,
                    year: "2024",
                    title:
                        this.locale === "ar"
                            ? "التوسع الإقليمي"
                            : "Regional Expansion",
                    description:
                        this.locale === "ar"
                            ? "تم التوسع إلى 5 دول جديدة في المنطقة"
                            : "Expanded to 5 new countries in the region",
                },
                {
                    id: 4,
                    year: "2025",
                    title:
                        this.locale === "ar"
                            ? "الابتكار المستمر"
                            : "Continuous Innovation",
                    description:
                        this.locale === "ar"
                            ? "إطلاق ميزات الذكاء الاصطناعي والتحليلات المتقدمة"
                            : "Launched AI features and advanced analytics",
                },
            ];
        },
    },
};
</script>

<style scoped>
.about-us-container {
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
    font-size: 2.5rem;
    font-weight: bold;
    color: #1a2733;
}

.stat-label {
    font-size: 0.9rem;
    color: #2c3e50;
    margin-top: 0.5rem;
}

.hero-image img {
    /* box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); */
}

/* Mission & Vision Section */
.mission-vision-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.mission-card,
.vision-card {
    background: #fff;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: 100%;
    transition: transform 0.3s ease;
}

.mission-card:hover,
.vision-card:hover {
    transform: translateY(-5px);
}

.card-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #e5aa2a, #f4c430);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.card-icon i {
    font-size: 2rem;
    color: #fff;
}

.mission-card h3,
.vision-card h3 {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: #1a2733;
}

.mission-card p,
.vision-card p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #2c3e50;
}

/* Values Section */
.values-section {
    padding: 80px 0;
    background: #fff;
}

.section-header {
    margin-bottom: 4rem;
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #1a2733;
    margin-bottom: 1rem;
}

.section-header p {
    font-size: 1.2rem;
    color: #2c3e50;
}

.value-card {
    text-align: center;
    padding: 2rem;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease;
    height: 100%;
}

.value-card:hover {
    transform: translateY(-5px);
}

.value-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #e5aa2a, #f4c430);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.value-icon i {
    font-size: 1.8rem;
    color: #fff;
}

.value-card h4 {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: #1a2733;
}

.value-card p {
    font-size: 1rem;
    line-height: 1.6;
    color: #2c3e50;
}

/* Team Section */
.team-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.team-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.team-card:hover {
    transform: translateY(-5px);
}

.member-image {
    width: 100%;
    height: 250px;
    overflow: hidden;
}

.member-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-info {
    padding: 2rem;
    text-align: center;
}

.member-info h4 {
    font-size: 1.4rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #1a2733;
}

.member-position {
    color: #e5aa2a;
    font-weight: 600;
    margin-bottom: 1rem;
}

.member-description {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1a2733;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #e5aa2a;
    color: #fff;
    transform: translateY(-2px);
}

/* Timeline Section */
.timeline-section {
    padding: 80px 0;
    background: #fff;
}

.timeline {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.timeline::before {
    content: "";
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5aa2a;
    transform: translateX(-50%);
}

.timeline-item {
    position: relative;
    margin-bottom: 3rem;
}

.timeline-item:nth-child(odd) .timeline-content {
    margin-left: 0;
    margin-right: 50%;
    text-align: right;
    padding-right: 2rem;
}

.timeline-item:nth-child(even) .timeline-content {
    margin-left: 50%;
    margin-right: 0;
    text-align: left;
    padding-left: 2rem;
}

.timeline-content {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    /* box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); */
    position: relative;
}
.timeline .timeline-content {
    overflow: unset;
}
.timeline-content::before {
    content: "";
    position: absolute;
    top: 50%;
    width: 20px;
    height: 20px;
    background: #e5aa2a;
    border-radius: 50%;
    transform: translateY(-50%);
}

.timeline-item:nth-child(odd) .timeline-content::before {
    right: -60px;
}

.timeline-item:nth-child(even) .timeline-content::before {
    left: -60px;
}

.timeline-year {
    font-size: 1.5rem;
    font-weight: bold;
    color: #e5aa2a;
    margin-bottom: 0.5rem;
}

.timeline-content h4 {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: #1a2733;
}

.timeline-content p {
    font-size: 1rem;
    line-height: 1.6;
    color: #2c3e50;
}

/* CTA Section */
.cta-section {
    padding: 80px 0;
    background: #1a2733;
    color: #fff;
}

.cta-content h2 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.cta-buttons {
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

/* Enhanced Responsive Design */
@media (max-width: 1200px) {
    .container {
        padding: 0 20px;
    }

    .hero-title {
        font-size: 3rem;
    }

    .hero-stats {
        gap: 1.5rem;
    }
}

@media (max-width: 992px) {
    .container {
        padding: 0 16px;
    }

    .hero-title {
        font-size: 2.8rem;
    }

    .hero-description {
        font-size: 1.1rem;
    }

    .hero-stats {
        gap: 1.2rem;
    }

    .stat-number {
        font-size: 2.5rem;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }

    .timeline::before {
        left: 20px;
    }

    .timeline-item:nth-child(odd) .timeline-content,
    .timeline-item:nth-child(even) .timeline-content {
        margin-left: 50px;
        margin-right: 0;
        text-align: left;
        padding-left: 2rem;
        padding-right: 1rem;
    }

    .timeline-item:nth-child(odd) .timeline-content::before,
    .timeline-item:nth-child(even) .timeline-content::before {
        left: -40px;
        right: auto;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }

    .container {
        padding: 0 12px;
    }

    .hero-description {
        font-size: 1rem;
    }

    .stat-number {
        font-size: 2.2rem;
    }
}

@media (max-width: 576px) {
    .container {
        padding: 0 8px;
    }

    .hero-title {
        font-size: 2rem;
    }

    .hero-description {
        font-size: 0.95rem;
    }

    .stat-number {
        font-size: 2rem;
    }

    .stat-label {
        font-size: 0.9rem;
    }

    .timeline-item:nth-child(odd) .timeline-content,
    .timeline-item:nth-child(even) .timeline-content {
        margin-left: 40px;
        padding-left: 1.5rem;
    }
}

/* RTL Support */
[dir="rtl"] .hero-stats {
    flex-direction: row-reverse;
}

[dir="rtl"] .timeline-item:nth-child(odd) .timeline-content {
    text-align: left;
}

[dir="rtl"] .timeline-item:nth-child(even) .timeline-content {
    text-align: right;
}
.titlefoote {
    color: #e5aa2a;
}
</style>
