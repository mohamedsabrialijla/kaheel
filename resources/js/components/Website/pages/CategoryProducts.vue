<template>
    <div>
        <!-- Banner -->
        <div class="category-banner-container">
            <img
                :src="category.banner"
                class="category-banner-img"
                alt="banner"
            />
            <div class="category-banner-overlay">
                <button class="btn btn-link text-white" @click="$router.back()">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <h1 class="category-title">{{ category.label }}</h1>
            </div>
        </div>
        <!-- Header -->
        <!-- <div class="category-header py-4 mb-3" style="background: #f6e3b1">
            <div class="container d-flex align-items-center gap-3">
                <img
                    :src="category.icon"
                    :alt="category.label"
                    style="
                        width: 56px;
                        height: 56px;
                        border-radius: 12px;
                        background: #fff;
                    "
                />
                <h2 class="fw-bold mb-0">{{ category.label }}</h2>
            </div>
        </div> -->
        <!-- Filter/Sort/Toggle Bar -->
        <div class="container mb-3">
            <div
                class="d-flex justify-content-between align-items-center flex-wrap gap-2"
            >
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-sort"></i> Sort
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button
                        class="btn customeHover"
                        :class="{
                            'btn-primary': viewMode === 'list',
                            'btn-outline-primary': viewMode !== 'list',
                        }"
                        @click="viewMode = 'list'"
                    >
                        <i class="fas fa-list"></i>
                    </button>
                    <button
                        class="btn customeHover"
                        :class="{
                            'btn-primary': viewMode === 'grid',
                            'btn-outline-primary': viewMode !== 'grid',
                        }"
                        @click="viewMode = 'grid'"
                    >
                        <i class="fas fa-th"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Products -->
        <div class="container mb-15">
            <div v-if="viewMode === 'list'" class="product-list">
                <div
                    v-for="i in 6"
                    :key="i"
                    class="product-card d-flex align-items-center justify-content-between bg-white rounded-4 shadow-sm mb-4 p-3"
                >
                    <div class="d-flex align-items-center">
                        <img
                            :src="productImg"
                            alt="Product"
                            class="rounded-3 me-3"
                            style="width: 70px; height: 70px; object-fit: cover"
                        />
                        <div class="flex-grow-1">
                            <div class="fw-bold titleProduct">
                                Golden Necklace
                            </div>
                            <div class="small text-muted ownerProduct">
                                <i class="fas fa-user-circle me-1"></i>
                                <div class="nameOwnerProductLocation">
                                    <span
                                        class="fw-bold text-dark ownerProductName"
                                        >Abdulrahman</span
                                    >
                                    <span
                                        class="ms-2 ownerProductLocation"
                                        style="color: #e5aa2a"
                                        >Jeddah, Saudi</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="d-flex flex-column align-items-center ms-10 me-auto"
                    >
                        <div
                            class="d-flex align-items-center mt-1 ownerProductPrice"
                        >
                            <span class="fw-bold me-2" style="color: #222"
                                >SAR 7,500</span
                            >
                            <span
                                class="badge text-dark ms-2 ownerProductStatus"
                                >new</span
                            >
                        </div>
                        <div class="text-muted small mt-1 ProductTime w-100">
                            2 minutes ago
                        </div>
                    </div>
                    <div class="product-actions ms-3">
                        <div class="invoice-link d-flex align-items-center">
                            <i class="far fa-file-alt me-1"></i>
                            <span>Invoice</span>
                        </div>
                        <div class="fav-btn mb-2">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="product-grid">
                <div
                    v-for="i in 8"
                    :key="i"
                    class="product-card-grid bg-white rounded-4 shadow-sm mb-4 p-3"
                >
                    <img
                        :src="productImg"
                        alt="Product"
                        class="rounded-3 mb-2"
                        style="width: 100%; height: 180px; object-fit: cover"
                    />
                    <div class="fw-bold titleProduct mb-1">Golden Necklace</div>
                    <div class="small text-muted ownerProduct mb-1">
                        <i class="fas fa-user-circle me-1"></i>
                        <span class="fw-bold text-dark ownerProductName"
                            >Abdulrahman</span
                        >
                        <span
                            class="ms-2 ownerProductLocation"
                            style="color: #e5aa2a"
                            >Jeddah, Saudi</span
                        >
                    </div>
                    <div
                        class="d-flex align-items-center mt-1 ownerProductPrice mb-1"
                    >
                        <span class="fw-bold me-2" style="color: #222"
                            >SAR 7,500</span
                        >
                        <span class="badge text-dark ms-2 ownerProductStatus"
                            >new</span
                        >
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center mt-2"
                    >
                        <span class="text-muted small ProductTime"
                            >5 min ago</span
                        >
                        <span class="invoice-link d-flex align-items-center"
                            ><i class="far fa-file-alt me-1"></i>invoice</span
                        >
                        <span class="fav-btn"
                            ><i class="far fa-heart"></i
                        ></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// يمكنك نسخ filterCategories من Landing.vue أو استيرادها من ملف مشترك
import goldIcon from "@/assets/icons/gold.png";
import watchIcon from "@/assets/icons/watch.png";
import bagIcon from "@/assets/icons/bag.png";
import perfumeIcon from "@/assets/icons/perfume.png";
import glassesIcon from "@/assets/icons/glasses.png";
import clothsIcon from "@/assets/icons/cloths.png";
import penIcon from "@/assets/icons/pen.png";
import shoesIcon from "@/assets/icons/shoes.png";
import beltIcon from "@/assets/icons/belt.png";
import bannerImg1 from "@/assets/banner/gold-cat.jpeg";
import bannerImg2 from "@/assets/banner/watch-cat.jpeg";
import bannerImg3 from "@/assets/banner/bag-cat.jpeg";
import bannerImg4 from "@/assets/banner/perfrum-cat.jpeg";
import bannerImg5 from "@/assets/banner/glacess-cat.jpeg";
import bannerImg6 from "@/assets/banner/cloths-cat.jpeg";
import bannerImg7 from "@/assets/banner/pen-cat.jpeg";
import bannerImg8 from "@/assets/banner/chose-cat.jpeg";
import bannerImg9 from "@/assets/banner/belt-cat.jpeg";

const filterCategories = [
    { label: "GOLD AND ACCESSORIES", icon: goldIcon, banner: bannerImg1 },
    { label: "WATCH", icon: watchIcon, banner: bannerImg2 },
    { label: "BAGS", icon: bagIcon, banner: bannerImg3 },
    { label: "PERFUMES", icon: perfumeIcon, banner: bannerImg4 },
    { label: "GLASSES", icon: glassesIcon, banner: bannerImg5 },
    { label: "CLOTHS", icon: clothsIcon, banner: bannerImg6 },
    { label: "PENS", icon: penIcon, banner: bannerImg7 },
    { label: "SHOES", icon: shoesIcon, banner: bannerImg8 },
    { label: "BELTS", icon: beltIcon, banner: bannerImg9 },
];

export default {
    name: "CategoryProducts",
    data() {
        return {
            viewMode: "grid",
            productImg:
                "https://images.pexels.com/photos/461382/pexels-photo-461382.jpeg?auto=compress&w=400",
        };
    },
    computed: {
        categoryId() {
            return Number(this.$route.params.id);
        },
        category() {
            return filterCategories[this.categoryId] || filterCategories[0];
        },
    },
    methods: {
        goToCategory(cat, idx) {
            this.$router.push({
                name: "category-products",
                params: { id: idx },
            });
        },
    },
};
</script>

<style scoped>
.category-banner-container {
    position: relative;
    width: 100%;
    height: 260px;
    overflow: hidden;
    margin-bottom: 15px;
}
.category-banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.7);
}
.category-banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 32px 48px;
    background: #00000052;
}
.category-title {
    color: #fff;
    font-size: 2.5rem;
    font-weight: bold;
    margin: -40px auto 0;
}
.category-header {
    background: #f6e3b1;
}
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
}
.product-card-grid {
    position: relative;
    min-height: 320px;
    transition: box-shadow 0.2s;
}
.product-card-grid .fav-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    margin-bottom: 0;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .category-banner-overlay {
        padding: 28px 40px;
    }

    .category-title {
        font-size: 2.2rem;
    }
}

@media (max-width: 992px) {
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .category-banner-overlay {
        padding: 24px 32px;
    }

    .category-title {
        font-size: 2rem;
    }

    .category-banner-container {
        height: 220px;
    }
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .category-banner-overlay {
        padding: 20px 24px;
    }

    .category-title {
        font-size: 1.8rem;
    }

    .category-banner-container {
        height: 180px;
    }

    .product-card-grid {
        min-height: 280px;
    }

    .d-flex.gap-2 {
        flex-direction: column;
        gap: 8px !important;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 8px;
    }

    .category-banner-overlay {
        padding: 16px 20px;
    }

    .category-title {
        font-size: 1.5rem;
    }

    .category-banner-container {
        height: 150px;
    }

    .product-card-grid {
        min-height: 260px;
    }

    .container {
        padding: 0 8px;
    }
}
</style>
