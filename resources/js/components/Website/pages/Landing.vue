<template>
    <div class="landing-bg">
        <!-- Filter & Sort Icons -->
        <div
            class="container my-4 position-relative container-custom-search-filter"
        >
            <!-- Top Search Bar -->
            <div
                class="d-flex align-items-center bg-white rounded-4 shadow-sm px-6 py-2 mb-0"
                style="max-width: 100%; width: 100%"
            >
                <i class="fas fa-search me-3 text-muted"></i>
                <input
                    type="text"
                    class="form-control border-0 bg-transparent"
                    :placeholder="translations?.search || 'Search'"
                    v-model="search"
                    @input="handleDataSearh()"
                />
            </div>

            <div class="d-flex justify-content-end align-items-center mb-0">
                <span class="icon-btn me-3" @click="showFilter = true">
                    <svg
                        width="28"
                        height="28"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M4 7h20M7 13h14M10 19h8"
                            stroke="#222"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>
                <span class="icon-btn me-3" @click="showSort = true">
                    <svg
                        width="28"
                        height="28"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M10 8v12M10 20l-3-3m3 3l3-3M18 20V8m0 0l3 3m-3-3l-3 3"
                            stroke="#222"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>
                <!-- <span class="icon-btn" @click="getFav()">
                    <i class="fas fa-heart"></i>
                </span> -->
            </div>
        </div>

        <!-- Filter Modal -->
        <div
            v-if="showFilter"
            class="modal-backdrop-custom"
            @click.self="showFilter = false"
        >
            <div class="modal-custom filter-modal">
                <div class="d-flex align-items-center mb-3">
                    <span
                        class="text-primary me-2"
                        style="cursor: pointer"
                        @click="showFilter = false"
                        >&lt; {{ translations?.back }}</span
                    >
                </div>
                <div class="d-flex flex-column align-items-center">
                    <img
                        :src="filterLogo"
                        alt="B Logo"
                        style="
                            width: 80px;
                            height: 80px;
                            border-radius: 8px;
                            background: #222;
                            margin-bottom: 10px;
                        "
                    />
                    <h3 class="fw-bolder mb-3">{{ translations?.filter }}</h3>
                </div>
                <hr />
                <!--  <div class="d-flex justify-content-center mb-4 gap-2">
                    <div
                        v-for="(cat, idx) in filterCategories"
                        :key="cat.label"
                        :class="[
                            'filter-cat-btn',
                            { active: filterCategory === idx },
                        ]"
                        @click="filterCategory = idx"
                    >
                        <div class="contentImg">
                            <img
                                :src="cat.icon"
                                style="
                                    max-width: 50px;
                                    height: 32px;
                                    margin-bottom: 4px;
                                    object-fit: contain;
                                "
                            />
                        </div>
                        <div class="fw-bold textContent">
                            {{ cat.label }}
                        </div>
                    </div>
                </div> -->

                <!-- صف 1: التصنيف (نصف) + بجانبه رجالي/نسائي أو البراند -->
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <select
                            class="form-select"
                            v-model.number="filter.category"
                            @click="fetchItemsCategories"
                        >
                            <option disabled value="">
                                {{ translations?.select }}
                                {{ translations?.category }}
                            </option>
                            <option
                                v-for="b in categories"
                                :key="b.id"
                                :value="b.id"
                            >
                                {{ b.name }}
                            </option>
                        </select>
                    </div>
                    <div
                        class="col-md-6 mb-2"
                        v-if="filter.category && filterGenders.length"
                    >
                        <select
                            class="form-select"
                            v-model.number="filter.gender"
                        >
                            <option disabled :value="null">
                                {{ translations?.select }}
                                {{ translations?.gender || 'الجنس' }}
                            </option>
                            <option
                                v-for="g in filterGenders"
                                :key="g.id"
                                :value="g.id"
                            >
                                {{ g.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2" v-else>
                        <select
                            class="form-select"
                            v-model="filter.brand"
                            :disabled="!filter.category"
                        >
                            <option disabled value="">
                                {{ translations?.select }}
                                {{ translations?.brand }}
                            </option>
                            <option
                                v-for="b in brands"
                                :key="b.id"
                                :value="b.id"
                            >
                                {{ b.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- صف 2: النوع ثم البراند -->
                <div
                    class="row mb-3"
                    v-if="filter.category && filterGenders.length"
                >
                    <div class="col-md-6 mb-2" v-if="filter.gender && filterSubcategories.length">
                        <select
                            class="form-select"
                            v-model.number="filter.subcategory"
                        >
                            <option disabled :value="null">
                                {{ translations?.select }}
                                {{ translations?.type || 'النوع' }}
                            </option>
                            <option
                                v-for="s in filterSubcategories"
                                :key="s.id"
                                :value="s.id"
                            >
                                {{ s.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <select class="form-select" v-model="filter.brand">
                            <option disabled value="">
                                {{ translations?.select }}
                                {{ translations?.brand }}
                            </option>
                            <option
                                v-for="b in brands"
                                :key="b.id"
                                :value="b.id"
                            >
                                {{ b.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <select
                            class="form-select"
                            v-model="filter.country"
                            @click="fetchItemsCountryAd"
                        >
                            <option disabled value="">
                                {{ translations?.select }}
                                {{ translations?.country }}
                            </option>
                            <option
                                v-for="c in countries"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <select
                            class="form-select"
                            v-model="filter.city"
                            @click="fetchItemsCityAd"
                        >
                            <option disabled value="">
                                {{ translations?.select }}
                                {{ translations?.state }}
                            </option>
                            <option
                                v-for="c in cities"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <select
                            class="form-select"
                            v-model="filter.productType"
                        >
                            <option disabled value="">
                                {{ translations?.product_type }}
                            </option>
                            <option
                                v-for="t in filterProductTypes"
                                :key="t.value"
                                :value="t.value"
                            >
                                {{ t.label }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <select class="form-select" v-model="filter.invoice">
                            <option disabled value="">
                                {{ translations?.invoice }}
                            </option>
                            <option
                                v-for="t in filterPrice"
                                :key="t.value"
                                :value="t.value"
                            >
                                {{ t.label }}
                            </option>
                        </select>
                    </div>
                </div>
                <button
                    class="btn btn-primary w-100 mt-3"
                    @click="handleFilterDone"
                >
                    {{ translations?.filter }}
                </button>
            </div>
        </div>

        <!-- Sort Modal -->
        <div
            v-if="showSort"
            class="modal-backdrop-custom"
            @click.self="showSort = false"
        >
            <div class="modal-custom sort-modal">
                <div class="d-flex align-items-center mb-3">
                    <span
                        class="text-primary me-2"
                        style="cursor: pointer"
                        @click="showSort = false"
                        >&lt; {{ translations?.back || "Back" }}</span
                    >
                </div>
                <div class="d-flex flex-column align-items-center">
                    <img
                        :src="filterLogo"
                        alt="B Logo"
                        style="
                            width: 80px;
                            height: 80px;
                            border-radius: 8px;
                            background: #222;
                            margin-bottom: 10px;
                        "
                    />
                    <h3 class="fw-bolder mb-3">{{ translations?.sort }}</h3>
                </div>
                <hr />
                <div class="mb-4">
                    <div
                        v-for="(option, idx) in sortOptions"
                        :key="option.value"
                        class="form-check mb-2"
                    >
                        <input
                            class="form-check-input"
                            type="radio"
                            :id="'sort' + idx"
                            v-model="sortSelected"
                            :value="option.value"
                        />
                        <label class="form-check-label" :for="'sort' + idx">{{
                            option.label
                        }}</label>
                    </div>
                </div>
                <button
                    class="btn btn-primary w-100 mt-3"
                    @click="handleSortDone"
                >
                    {{ translations?.filter || "filter" }}
                </button>
            </div>
        </div>
        <!--   <div class="container mb-4 containerCategoryFilter">
            <div class="d-flex justify-content-between gap-2">
                <div
                    v-for="(cat, idx) in filterCategories"
                    :key="cat.label + '-main'"
                    :class="[
                        'filter-cat-btn',
                        { active: selectedCategory === idx },
                    ]"
                    @click="goToCategory(cat, idx)"
                >
                    <div class="contentImg">
                        <img
                            :src="cat.icon"
                            style="
                                max-width: 50px;
                                height: 32px;
                                margin-bottom: 4px;
                                object-fit: contain;
                            "
                        />
                    </div>
                    <div class="fw-bold textContent">
                        {{ cat.label }}
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Main Banner and Promo Card -->
        <div class="container mb-5">
            <div class="row g-3 align-items-stretch mb-15">
                <!-- Banner/Slider as component -->
                <div class="col-lg-12">
                    <BannerSlider />
                </div>
                <!-- Promo Card -->
                <!-- <div class="col-lg-3">
                    <div
                        class="promo-card rounded-4 p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center"
                        style="background: #e5aa2a; min-height: 270px"
                    >
                        <h1 class="fw-bold text-white mb-2">Big Sale</h1>
                        <div class="text-white mb-2">Up to 50% off</div>
                        <div class="text-white-50">Happening Now</div>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- Category Filter Bar (ثابت تحت السلايدر) -->

        <!-- Product List -->
        <div class="container mb-3">
            <!-- <div class="d-flex justify-content-end gap-2">
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
            </div> --> 
        </div>
        <div class="container mb-15">
            <div v-if="viewMode === 'list'" class="product-list">
                <div
                    v-for="(ad, index) in ads"
                    :key="ad.id"
                    class="product-card d-flex align-items-center justify-content-between bg-white rounded-4 shadow-sm mb-4 p-3"
                >
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <img
                            v-if="ad.image_basic"
                            @click="goToAdDetails(ad)"
                            :src="ad.image_basic.url"
                            alt="Product"
                            class="rounded-3 me-3"
                            style="
                                width: 70px;
                                height: 70px;
                                object-fit: cover;
                                cursor: pointer;" 
                        />

                        <img
                            v-else
                            @click="goToAdDetails(ad)"
                            src="/assets/media/svg/brand-logos/logo.png"
                            alt="Product"
                            class="rounded-3 me-3"
                            style="
                                width: 70px;
                                height: 70px;
                                object-fit: cover;
                                cursor: pointer;
                            " 
                        />

                        <div class="flex-grow-1">
                            <div
                                class="fw-bold titleProduct"
                                @click="goToAdDetails(ad)"
                                style="cursor: pointer"
                            >
                                {{ ad.title }}
                            </div>
                            <div class="small text-muted ownerProduct">
                                <div class="nameOwnerProductLocation">
                                    <!-- في حالة فيه سعر -->
                                <span
                                  class="fw-bold me-2"
                                  style="color: #e5aa2a"
                                  v-if="ad.price"
                                >
                                  {{ ad.price }}
                                  <img v-if="locale=='ar'" src="/assets_website/logos/sar.png" width="30" />
                                  <span v-else>SAR</span>
                                </span>

                                <!-- في حالة ما فيه سعر -->
                                <span
                                  class="fw-bold me-2"
                                  style="color: #222"
                                  v-else
                                >
                                  {{
                                            translations?.Not_Price ||
                                            "Not_Price "
                                        }}
                                </span>


                                    <span
                                        class="badge text-dark ms-2 ownerProductStatus"
                                        >{{ ad.product_type_translated }}
                                    </span>

                                       <span class="badge text-dark ms-2">{{
                                                ad.views
                                            }} {{ translations?.views || "views" }}
                                        </span>

 
                                </div>
                            </div> 

                            <div class="small text-muted ownerProduct">
                                <i class="fas fa-user-circle me-1"></i>

                                <UserNameLink
                                    v-if="ad?.user"
                                    :user="ad.user"
                                    link-class="fw-bold text-dark ownerProductName"
                                />
                                <span
                                    v-if="ad?.city"
                                    class="ms-2 ownerProductLocation"
                                    >{{ ad.country.name }} - {{ ad.city.name }}
                                </span>

                                <span class="ms-2 ownerProductLocation"
                                    >{{ ad.updated_at_human }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="d-flex flex-column align-items-center ms-10 me-auto"
                    >
                        <!-- <div class="d-flex align-items-center mt-1 ownerProductPrice">
                            <span class="fw-bold me-2" style="color: #222" v-if="ad.price"
                                >SAR {{ ad.price }}</span
                            >

                            <span class="fw-bold me-2" style="color: #222" v-else
                                >not found price</span
                            >
                            <span
                                class="badge text-dark ms-2 ownerProductStatus"
                                >{{ad.product_type_translated}}</span
                            >
                        </div> -->
                        <!-- <div class="text-muted small mt-1 ProductTime w-100">
                            {{ ad.updated_at_human }}
                        </div> -->
                    </div>
                    <div class="product-actions ms-3">
                        <div
                            v-if="ad.invoice && ad.invoice !== ''"
                            class="invoice-link d-flex align-items-center"
                        >
                            <a :href="getInvoiceUrl(ad.invoice)" target="_blank"
                                ><i
                                    class="far fa-file-alt me-1"
                                    style="color: #e5aa2a; cursor: pointer"
                                ></i
                            ></a>
                            <!-- <span>Invoice</span> -->
                        </div>

                        <div
                            v-else
                            class="invoice-link d-flex align-items-center"
                        >
                            <i class="far fa-file-alt me-1"></i>
                            <!-- <span>Invoice</span> -->
                        </div>

                        <div
                            class="fav-btn mb-2"
                            :class="{ active: ad.is_favorite }"
                            @click="toggleFavorite(ad)"
                        >
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                </div>

                <div v-if="isFetchingMore" class="text-center my-4">
                    <span
                        class="spinner-border spinner-border-sm align-middle text-primary me-2"
                    ></span>
                    <span class="align-middle">
                        {{
                            translations?.loading_more || "Loading more..."
                        }}</span
                    >
                </div>
            </div>
            <div v-else class="product-grid">
                <div
                    v-for="(ad, index) in ads"
                    :key="ad.id"
                    class="product-card-grid bg-white rounded-4 shadow-sm mb-4 p-3"
                >
                    <img
                        v-if="ad.image_basic"
                        @click="goToAdDetails(ad)"
                        :src="ad.image_basic.url"
                        alt="Product"
                        class="rounded-3 mb-2"
                        style="
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            cursor: pointer;
                        "
                    />
                    <div
                        class="fw-bold titleProduct mb-1"
                        @click="goToAdDetails(ad)"
                        style="cursor: pointer"
                    >
                        {{ ad.title }}
                    </div>
                    <div class="small text-muted ownerProduct mb-1">
                        <i class="fas fa-user-circle me-1"></i>
                        <UserNameLink
                            v-if="ad?.user"
                            :user="ad.user"
                            link-class="fw-bold text-dark ownerProductName"
                        />
                        <span
                            v-if="ad?.city"
                            class="ms-2 ownerProductLocation"
                            style="color: #e5aa2a"
                            >{{ ad?.country?.name }} -
                            {{ ad?.city?.name }}</span
                        >
                    </div>
                    <div
                        class="d-flex align-items-center mt-1 ownerProductPrice mb-1"
                    >
                        <span
                            class="fw-bold me-2"
                            style="color: #222"
                            v-if="ad.price"
                            >SAR {{ ad.price }}</span
                        >
                        <span class="fw-bold me-2" style="color: #222" v-else>
                            {{ translations?.Not_Price || "Not Price" }}</span
                        >

                        <span class="badge text-dark ms-2 ownerProductStatus">{{
                            ad.product_type_translated
                        }}</span>




                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center mt-2"
                    >
                        <span class="text-muted small ProductTime">
                            {{ ad.updated_at_human }}</span
                        >
                        <!-- <span class="text-muted small"
                            ><i class="far fa-eye"></i> 3500</span
                        > -->
                        <div
                            v-if="ad.invoice && ad.invoice !== ''"
                            class="invoice-link d-flex align-items-center"
                        >
                            <a :href="getInvoiceUrl(ad.invoice)" target="_blank"
                                ><i
                                    class="far fa-file-alt me-1"
                                    style="color: #e5aa2a; cursor: pointer"
                                ></i
                            ></a>
                            <span>
                                {{ translations?.invoice || "Invoice" }}</span
                            >
                        </div>

                        <div
                            v-else
                            class="invoice-link d-flex align-items-center"
                        >
                            <i class="far fa-file-alt me-1"></i>
                            <span>{{
                                translations?.invoice || "Invoice"
                            }}</span>
                        </div>

                        <div
                            class="fav-btn mb-2"
                            :class="{ active: ad.is_favorite }"
                            @click="toggleFavorite(ad)"
                        >
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Favorites Modal -->
        <div
            v-if="showFavorites"
            class="modal-backdrop-custom"
            @click.self="showFavorites = false"
        >
            <div class="modal-custom" style="max-width: 100%; width: auto">
                <div class="text-center mb-4">
                    <img
                        src="@/assets/logo-b.png"
                        style="width: 80px; border-radius: 16px"
                    />
                    <h2 class="fw-bold mt-2">{{ translations?.favorite || "Favorite" }}</h2> 
                </div>
                <div
                    v-if="favorites.length === 0"
                    class="text-center text-muted"
                >
                    {{ translations?.No_favorites_yet || "No favorites yet." }}
                </div>
                <div v-else>
                    <div
                        v-for="ad in favorites"
                        :key="ad.id"
                        class="product-card d-flex align-items-center justify-content-between bg-white rounded-4 shadow-sm mb-4 p-3 gap-10"
                    >
                        <div class="d-flex align-items-center">
                            <img
                                @click="goToAdDetails(ad)"
                                :src="ad.image_basic.url"
                                alt="Product"
                                class="rounded-3 me-3"
                                style="
                                    width: 70px;
                                    height: 70px;
                                    object-fit: cover;
                                    cursor: pointer;
                                "
                            />
                            <div class="flex-grow-1">
                                <div
                                    class="fw-bold titleProduct"
                                    @click="goToAdDetails(ad)"
                                    style="cursor: pointer"
                                >
                                    {{ ad.title }}
                                </div>
                                <div class="small text-muted ownerProduct">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <div class="nameOwnerProductLocation">
                                        <UserNameLink
                                            v-if="ad?.user"
                                            :user="ad.user"
                                            link-class="fw-bold text-dark ownerProductName"
                                        />
                                        <span
                                            class="ms-2 ownerProductLocation"
                                            style="color: #e5aa2a"
                                            >{{ ad?.country?.name }} -
                                            {{ ad?.city?.name }}</span
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
                                <span
                                    v-if="ad.price"
                                    class="fw-bold me-2"
                                    style="color: #222"
                                    >SAR {{ ad.price }}</span
                                >

                                <span
                                    v-else
                                    class="fw-bold me-2"
                                    style="color: #222"
                                    >{{
                                        translations?.Not_Price || "Not Price"
                                    }}</span
                                >

                                <span
                                    class="badge text-dark ms-2 ownerProductStatus"
                                    >{{ ad.product_type_translated }}</span
                                >
                            </div>
                            <div
                                class="text-muted small mt-1 ProductTime w-100"
                            >
                                {{ ad.updated_at_human }}
                            </div>
                        </div>

                        <div class="product-actions ms-3">
                            <div
                                v-if="ad.invoice && ad.invoice !== ''"
                                class="invoice-link d-flex align-items-center"
                            >
                                <a
                                    :href="getInvoiceUrl(ad.invoice)"
                                    target="_blank"
                                    ><i
                                        class="far fa-file-alt me-1"
                                        style="color: #e5aa2a; cursor: pointer"
                                    ></i
                                ></a>
                                <span>{{
                                    translations?.invoice || "Invoice"
                                }}</span>
                            </div>

                            <div
                                v-else
                                class="invoice-link d-flex align-items-center"
                            >
                                <i class="far fa-file-alt me-1"></i>
                                <span>{{
                                    translations?.invoice || "Invoice"
                                }}</span>
                            </div>

                            <div
                                class="fav-btn active mb-2"
                                @click="toggleFavorite(ad)"
                            >
                                <i class="far fa-heart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showConfirmToast" class="custom-toast">
            {{ confirmToastMsg }}
            <button
                class="btn btn-sm btn-danger ms-2"
                @click="confirmRemoveFavorite"
            >
                {{ translations?.Yes || "Yes" }}
            </button>
            <button
                class="btn btn-sm btn-secondary ms-2"
                @click="cancelRemoveFavorite"
            >
                {{ translations?.No || "No" }}
            </button>
        </div>
        <div v-if="showToast && !showConfirmToast" class="custom-toast">
            {{ toastMsg }}
        </div>
    </div>
</template>

<script>
import BannerSlider from "./BannerSlider.vue";
import logoB from "@/assets/logo-b.png";
import goldIcon from "@/assets/icons/gold.png";
import watchIcon from "@/assets/icons/watch.png";
import bagIcon from "@/assets/icons/bag.png";
import perfumeIcon from "@/assets/icons/perfume.png";
import glassesIcon from "@/assets/icons/glasses.png";
import clothsIcon from "@/assets/icons/cloths.png";
import penIcon from "@/assets/icons/pen.png";
import shoesIcon from "@/assets/icons/shoes.png";
import beltIcon from "@/assets/icons/belt.png";
// import axios from "axios";
import { api as axios } from "@/axiosUser";
import { navigateToUserProfile } from "@/utils/userProfile";
import { requireLogin } from "@/utils/requireLogin";
import { fetchAvailableGenders } from "@/utils/categoryGenders";

export default {
    name: "Home",
    components: {
        BannerSlider,
    },
    data() {
        return {
            showFilter: false,
            showSort: false,
            showFavorites: false,
            showToast: false,
            toastMsg: "",
            showConfirmToast: false,
            confirmToastMsg: "",
            confirmProduct: null,
            filterLogo: logoB,

            selectedCategory: null,
            countries: [],
            cities: [],
            categories: [],
            brands: [],
            filterGenders: [],
            filterSubcategories: [],
            filterProductTypes: [
                { label: "New", value: "new" },
                { label: "Used", value: "used" },
            ],
            filterPrice: [
                { label: "Yes", value: "yes" },
                { label: "No", value: "no" },
            ],

            filter: {
                category: null,
                country: "",
                city: "",
                brand: "",
                gender: null,
                subcategory: null,
                productType: "",
                invoice: "",
            },
            // sortOptions: [
            //   { label: "Most Viewed", value: "most_viewed" },
            //   { label: "The Latest", value: "latest" },
            //   { label: "More Rated", value: "more_rate" },
            //   { label: "Highest Value", value: "highest_price" },
            //   { label: "Less Value", value: "less_price" }
            // ],

            sortSelected: "latest",
            viewMode: "list",
            favorites: [],
            ads: [],
            currentPage: 1,
            lastPage: null,
            isFetchingMore: false,
            hasMoreData: true,
            search: null,
            isLoggedIn: !!localStorage.getItem("token"),
        };
    },
    mounted() {
        this.fetchItemsAds();
        window.addEventListener("scroll", this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener("scroll", this.handleScroll);
    },
    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },

        translations() {
            return this.$store.getters.getTranslations || {};
        },

        user() {
            return this.$store.getters.getUser;
        },

        sortOptions() {
            return [
                {
                    label: this.translations.most_viewed || "Most Viewed",
                    value: "most_viewed",
                },
                {
                    label: this.translations.latest || "The Latest",
                    value: "latest",
                },
                {
                    label: this.translations.more_rate || "More Rated",
                    value: "more_rate",
                },
                {
                    label: this.translations.highest_price || "Highest Value",
                    value: "highest_price",
                },
                {
                    label: this.translations.less_price || "Less Value",
                    value: "less_price",
                },
            ];
        },
    },
    methods: {
        goToCategory(cat, idx) {
            this.$router.push({
                name: "category-products",
                params: { id: idx },
            });
        },

        // isFavorite(product) {
        //     return this.favorites.some((fav) => fav.id === product.id);
        // },
        // showRemoveFavoriteConfirm(product) {
        //     this.confirmProduct = product;
        //     this.confirmToastMsg =
        //         "Are you sure you want to remove this product from favorites?";
        //     this.showConfirmToast = true;
        // },
        // confirmRemoveFavorite() {
        //     this.toggleFavorite(this.confirmProduct);
        //     this.showConfirmToast = false;
        //     this.toastMsg = "Removed from favorites!";
        //     this.showToast = true;
        //     setTimeout(() => {
        //         this.showToast = false;
        //     }, 2000);
        //     this.confirmProduct = null;
        // },
        // cancelRemoveFavorite() {
        //     this.showConfirmToast = false;
        //     this.confirmProduct = null;
        // },

        getInvoiceUrl(file) {
            if (file instanceof File) {
                return URL.createObjectURL(file);
            }

            return file;
        },

        goToAdDetails(ad) {
            // this.$store.commit("setAdCurrent", ad);
            localStorage.setItem("ad", JSON.stringify(ad));
            this.$router.push({ path: `/${this.locale}/ad-details/${ad.id}` });
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

        listingParams(page = 1) {
            return {
                page,
                pagination: 1,
                sort_by: this.sortSelected || "latest",
                search: this.search || undefined,
                category_id: this.filter.category || undefined,
                brand_id: this.filter.brand || undefined,
                gender_id: this.filter.gender || undefined,
                subcategory_id: this.filter.subcategory || undefined,
                country_id: this.filter.country || undefined,
                city_id: this.filter.city || undefined,
                invoice: this.filter.invoice || undefined,
                product_type: this.filter.productType || undefined,
            };
        },

        resetListing() {
            this.ads = [];
            this.currentPage = 1;
            this.hasMoreData = true;
            this.lastPage = null;
        },

        async fetchItemsAds() {


            if (!this.hasMoreData || this.isFetchingMore) return;

            this.isFetchingMore = true;

            try {
                const response = await axios.get("ads/getAllItems", {
                    params: this.listingParams(this.currentPage),
                });

                const items = response.data.items?.data || [];

                if (items.length > 0) {
                    this.ads = [...this.ads, ...items];
                    this.currentPage++;
                }

                const totalPages = response.data.items?.last_page;
                if (this.currentPage > totalPages) {
                    this.hasMoreData = false;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.isFetchingMore = false;
            }
        },

        handleScroll() {
            const scrollBottom =
                window.innerHeight + window.scrollY >=
                document.body.offsetHeight - 200;

            if (scrollBottom) {
                this.fetchItemsAds(true);
            }
        },

        goToUserPage(user) {
            navigateToUserProfile(this.$router, this.locale, this.user, user);
        },

        async fetchItemsCountryAd() {
            this.isLoading = true;
            await axios
                .get("basic/getCountries")
                .then((response) => {
                    this.countries = response.data.items;

                    this.fetchItemsCityAd();

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async fetchItemsCityAd() {
            this.isLoading = true;
            await axios
                .get("basic/getCountries", {
                    params: {
                        country_id: this.filter.country,
                    },
                })
                .then((response) => {
                    this.cities = response.data.items;
                    console.log(this.cities);
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async fetchItemsCategories() {
            this.isLoading = true;
            await axios
                .get("basic/getCategories")
                .then((response) => {
                    this.categories = response.data.items;
                    if (this.filter.category) {
                        this.onFilterCategoryChange();
                    }
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async onFilterCategoryChange() {
            this.filter.gender = null;
            this.filter.subcategory = null;
            this.filter.brand = "";
            this.filterGenders = [];
            this.filterSubcategories = [];
            this.brands = [];

            if (!this.filter.category) {
                return;
            }

            await this.fetchFilterGenders();
            await this.fetchItemsBrands();
        },

        async onFilterGenderChange() {
            this.filter.subcategory = null;
            this.filterSubcategories = [];
            if (this.filter.gender) {
                await this.fetchFilterSubcategories();
            }
        },

        async fetchFilterGenders() {
            if (!this.filter.category) {
                this.filterGenders = [];
                return;
            }
            try {
                this.filterGenders = await fetchAvailableGenders(
                    axios,
                    this.filter.category
                );
                if (
                    this.filter.gender &&
                    !this.filterGenders.some(
                        (g) => Number(g.id) === Number(this.filter.gender)
                    )
                ) {
                    this.filter.gender = null;
                    this.filter.subcategory = null;
                    this.filterSubcategories = [];
                }
            } catch (error) {
                console.error("getGenders failed", error);
                this.filterGenders = [];
            }
        },

        async fetchFilterSubcategories() {
            if (!this.filter.gender) {
                this.filterSubcategories = [];
                return;
            }
            try {
                const response = await axios.get("basic/getSubcategories", {
                    params: { gender_id: this.filter.gender },
                });
                this.filterSubcategories = response.data?.items || [];
            } catch (error) {
                console.error("getSubcategories failed", error);
                this.filterSubcategories = [];
            }
        },

        async fetchItemsBrands() {
            if (!this.filter.category) {
                this.brands = [];
                return;
            }
            this.isLoading = true;
            await axios
                .get("basic/getCategories", {
                    params: {
                        category_id: this.filter.category,
                    },
                })
                .then((response) => {
                    this.brands = response.data.items || [];
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async handleFilterDone() {
            this.isLoading = true;
            this.showFilter = false;
            this.resetListing();
            try {
                await this.fetchItemsAds();
            } catch (error) {
                this.swalFunction("error", error);
            } finally {
                this.isLoading = false;
            }
        },

        async handleSortDone() {
            this.isLoading = true;
            this.showSort = false;
            this.resetListing();
            try {
                await this.fetchItemsAds();
            } catch (error) {
                this.swalFunction("error", error);
            } finally {
                this.isLoading = false;
            }
        },

        async handleDataSearh() {
            this.isLoading = true;
            this.resetListing();
            try {
                await this.fetchItemsAds();
            } catch (error) {
                this.swalFunction("error", error);
            } finally {
                this.isLoading = false;
            }
        },

      

        async toggleFavorite(ad) {
            if (!requireLogin(this.$store)) {
                return;
            }
            // e.preventDefault();
            try {
                const formData = new FormData();

                formData.append("ad_id", ad.id);

                const response = await axios.post(
                    "/ads/toggleFavorite",
                    formData,
                    {
                        headers: {
                            "Content-Type":
                                formData instanceof FormData
                                    ? "multipart/form-data"
                                    : "application/json",
                        },
                    }
                );

                if (response.data.code === 200) {
                    ad.is_favorite = !ad.is_favorite;
                    if (this.showFavorites) {
                        // this.favorites.splice(idx, 1);
                        this.getFav();
                    }
                    this.fetchItemsAds();
                    if (ad.is_favorite) {
                        this.toastMsg = response.data.message;
                        this.showToast = true;

                        setTimeout(() => {
                            this.showToast = false;
                        }, 2000);
                    } else {
                        this.toastMsg = response.data.message;
                        this.showToast = true;

                        setTimeout(() => {
                            this.showToast = false;
                        }, 2000);
                    }
                } else {
                    this.toastMsg = "Error to favorites!";
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 2000);
                }
            } catch (error) {
                this.toastMsg =
                    error.response?.data?.message || "Error to favorites!";
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                }, 2000);
            } finally {
                this.isUpdating = false;
            }
        },

        async getFav() {
            this.isLoading = true;
            await axios
                .get("ads/favorites", {
                    params: {
                        search: this.search,
                    },
                })
                .then((response) => {
                    this.showFavorites = true;
                    this.favorites = response.data.items;
                    this.isLoading = false;
                })
                .catch((error) => {
                    // console.log(error)
                    this.showFilter = false;
                    // this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },
    },

    watch: {
        "filter.category"(val) {
            this.onFilterCategoryChange();
        },
        "filter.gender"(val) {
            this.onFilterGenderChange();
        },
    },
};
</script>

<style type="text/css">
button.btn.customeHover.btn > i {
    padding-right: 0 !important;
    padding-left: 0 !important;
}
button.btn.customeHover:hover i {
    color: #000;
}
.containerCategoryFilter .filter-cat-btn {
    min-width: 10%;
}
.container-custom-search-filter {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}
.fav-btn i {
    font-size: 18px !important;
}
.modal-custom label.form-check-label {
    color: #071437;
}
.invoice-link i {
    font-size: 18px !important;
}
.product-actions {
    min-width: 70px;
    align-items: center;
    display: flex;
    gap: 15px;
}
.ownerProductStatus {
    background-color: #ced0d763 !important;
    font-size: 12px;
}
.nameOwnerProductLocation {
    display: flex;
    align-items: center;
    font-size: 14px;
}
.ownerProduct {
    display: flex;
    align-items: center;
}
.fw-bold.titleProduct {
    font-size: 16px;
}
span.ownerProductName {
    display: flex;
    align-items: center;
    font-size: 15px;
    gap: 4px;
}
.user-profile-link {
    cursor: pointer;
}
.user-profile-link:hover {
    color: #e5aa2a !important;
    text-decoration: underline;
}
.video-container {
    width: 100%;
    height: 400px; /* تحديد الارتفاع */
    overflow: hidden; /* إخفاء المحتوى الزائد */
    position: relative;
}

video {
    width: 100%;
    height: 100%;
    object-fit: contain; /* تأكد من الحفاظ على الأبعاد الأصلية */
}

.tns-nav {
    margin-top: 15px;
    position: absolute;
    bottom: -40px;
    z-index: 1;
    left: auto;
    right: auto;
    text-align: left;
    width: 100%;
}
.tns-nav button {
    border: none;
    background: #ccc;
    border-radius: 33%;
    width: 16px;
    height: 7px;
    margin: 0 5px;
    padding: 0;
}
.tns-nav .tns-nav-active {
    background: #e4a92a;
}

button[data-action="start"],
button[data-action="stop"] {
    display: none !important;
}

.product-actions {
    min-width: 70px;
}

.fav-btn {
    background: #ecedef;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(80, 80, 80, 0.07);
    margin-bottom: 0px;
    transition: background 0.2s;
    cursor: pointer;
}
.fav-btn i {
    color: #ff3e6c;
    font-size: 1.4rem;
    transition: color 0.2s;
}
.fav-btn:hover {
    background: #ffe6ef;
}
.fav-btn:hover i {
    color: #e5004c;
}

.invoice-link {
    color: #222;
    font-size: 14px;
    gap: 4px;
}
.invoice-link i {
    color: #bcbcc7;
    font-size: 1.1rem;
}

.icon-btn {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 54px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(80, 80, 80, 0.07);
    transition: background 0.2s;
}
.icon-btn:hover {
    background: #f5f5f5;
}

.modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.38);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-custom {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    padding: 32px 24px 24px 24px;
    min-width: 500px;
    max-width: 95vw;
    width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}
.filter-modal {
    width: auto;
    max-width: 98vw;
}
.sort-modal {
    width: 400px;
    max-width: 95vw;
}
.contentImg {
    padding: 20px 16px;
    display: flex;
    background: linear-gradient(180deg, #ffffff 0%, #eeeeee 100%);
    width: 100%;
    justify-content: center;
    align-items: center;
    border-radius: 8px 8px 8px 8px;
}
.filter-cat-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 2px solid #eee;
    border-radius: 8px;
    padding: 2px;
    min-width: 100px;
    cursor: pointer;
    background: #fff;
    transition: border 0.2s, background 0.2s;
}
.textContent {
    padding: 6px 16px 5px;
    font-size: 12px;
}
.filter-cat-btn.active {
    border: 2px solid #e5aa2a;
    background: #fffbe6;
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
.fav-btn.active i {
    color: #e5004c !important;
}
.custom-toast {
    position: fixed;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: #222;
    color: #fff;
    padding: 12px 32px;
    border-radius: 8px;
    z-index: 3000;
    font-size: 1.1rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
    transition: opacity 0.3s;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .container-custom-search-filter {
        padding: 0 16px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .container-custom-search-filter {
        padding: 0 12px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .filter-cat-btn {
        min-width: 80px;
    }

    .textContent {
        font-size: 11px;
        padding: 4px 12px 3px;
    }
}

@media (max-width: 768px) {
    .container-custom-search-filter {
        padding: 0 8px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .product-card-grid {
        min-height: 280px;
    }

    .filter-cat-btn {
        min-width: 70px;
    }

    .textContent {
        font-size: 10px;
        padding: 3px 8px 2px;
    }

    .contentImg {
        padding: 16px 12px;
    }
}

@media (max-width: 576px) {
    .container-custom-search-filter {
        padding: 0 4px;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 8px;
    }

    .product-card-grid {
        min-height: 260px;
    }

    .filter-cat-btn {
        min-width: 60px;
    }

    .textContent {
        font-size: 9px;
        padding: 2px 6px 1px;
    }

    .contentImg {
        padding: 12px 8px;
    }

    .custom-toast {
        font-size: 1rem;
        padding: 10px 24px;
        top: 20px;
    }
}
</style>
