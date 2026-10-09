<template>
    <div class="pay-commission-container">
        <div class="ad-details-bg">
            <div class="container ad-details-layout">
                <!-- الجزء الرئيسي -->
                <div class="ad-details-main-content">
                    <div class="ad-details-card">
                        <div class="pay-commission-header">
                            <button
                                class="ad-details-back"
                                @click="$router.back()"
                            >
                                <i
                                    :class="
                                        locale == 'ar'
                                            ? 'fas fa-angle-right'
                                            : 'fas fa-angle-left'
                                    "
                                ></i>
                                {{ translations?.Back || "Back" }}
                            </button>
                            <div class="pay-commission-title">
                                {{ translations?.Ad_Details || "Ad Details" }}
                            </div>
                        </div>
                        <hr class="pay-commission-divider" />
                        <!-- صور الإعلان -->
                        <div class="ad-details-images-container">
                            <!-- الصورة الرئيسية -->
                            <div class="ad-details-main-img-wrap">
                                <img style="object-fit:fill;" 
                                    v-if="mainImg"
                                    :src="mainImg"
                                    class="ad-details-main-img"
                                />
                                <!-- <div class="ad-details-main-logo-box">
                                    <img 
                                        src="/assets/media/svg/brand-logos/logo.png"
                                        class="ad-details-main-logo"
                                    />
                                    <div
                                        class="ad-details-main-logo-text"
                                        v-if="setting"
                                    >
                                        {{ setting.name }}
                                    </div>
                                </div> -->
                            </div>

                            <!-- الصور المصغرة -->
                            <div class="ad-details-thumbs-grid">
                                <div
                                    v-for="(img, i) in ad.images"
                                    :key="i"
                                    class="ad-details-thumb-wrap"
                                >
                                    <img style="object-fit: fill;" 
                                        :src="img.url"
                                        class="ad-details-thumb"
                                        :class="{ active: mainImg === img.url }"
                                        @click="selectImg(img.url)"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- تفاصيل الإعلان -->
                        <div class="ad-details-info-card">
                            <div class="ad-details-title-main">
                                {{ ad.title }} 

                            </div>
                            <span class="ad-details-time">{{ad.updated_at_human || "1min ago" }}</span>
                            

                            <div class="ad-details-table" style="margin-top:18px;">
                                <div class="ad-details-row" v-if="ad.brand">
                                    <span>{{
                                        translations?.brand || "Brand"
                                    }}</span
                                    ><span>{{ ad?.brand?.name }}</span>
                                </div>
                                <div class="ad-details-row" v-if="ad.gender">
                                    <span>{{ translations?.gender || "الجنس" }}</span>
                                    <span>{{ ad?.gender?.name }}</span>
                                </div>
                                <div class="ad-details-row" v-if="ad.subcategory">
                                    <span>{{ translations?.type || "النوع" }}</span>
                                    <span>{{ ad?.subcategory?.name }}</span>
                                </div>
                                <div class="ad-details-row" v-if="ad.price">
                                    <span>
                                        {{
                                            translations?.price || "Price"
                                        }}</span
                                    >
                                    <!-- <span class="ad-details-price">
                                        <i class="fas fa-coins"></i>
                                        {{ ad.price }}
                                    </span> -->

                                    <span
                                      class="fw-bold me-2"
                                      style="color: #e5aa2a"
                                      v-if="ad.price"
                                    >
                                      {{ ad.price }}
                                  <img v-if="locale=='ar'" style="vertical-align: middle;" src="/assets_website/logos/sar.png" width="30" />
                                  <span v-else>SAR</span>
                                </span>


                                </div>
                                <div class="ad-details-row">
                                    <span>{{
                                        translations?.date || "Date"
                                    }}</span>
                                    <span>
                                        
                                        {{ ad?.city?.name }},
                                        {{ ad?.country?.name }}
                                    </span>
                                </div>
                                <div class="ad-details-row">
                                    <span>
                                        {{ translations?.Type || "Type" }}</span
                                    ><span>{{
                                        ad?.product_type_translated
                                    }}</span>
                                </div>

                                <div class="ad-details-row"> 


                                    <span v-if="ad.invoice">
                                        {{ translations?.invoice || "Invoice" }} ( {{ translations?.found || "Found" }} )</span>


                                    <span v-else>
                                        {{ translations?.invoice || "Invoice" }}  ( {{ translations?.not_found || "Not Found" }} )  </span>

                                    

                                    <a  v-if="ad.invoice" :href="getInvoiceUrl(ad.invoice)" target="_blank"
                                        ><i
                                            class="far fa-file-alt me-1"
                                            style="color: #e5aa2a; cursor: pointer"
                                        ></i>
                                    </a>


                                    <a  v-else :href="getInvoiceUrl(ad.invoice)" target="_blank"
                                        ><i
                                            class="far fa-file-alt me-1"
                                            
                                        ></i>
                                    </a>



                                </div>
                                <div class="ad-details-row">
                                    <span>
                                        {{
                                            translations?.Ad_Number ||
                                            "Ad Number"
                                        }}</span
                                    ><span>{{ ad?.ad_number }}</span>
                                </div>
                                <div
                                    class="ad-details-row ad-details-row-advertiser"
                                >
                                    <span>
                                        {{
                                            translations?.Advertiser ||
                                            "Advertiser"
                                        }}</span
                                    >
                                    <span
                                        class="ad-details-advertiser user-profile-link"
                                        style="display: block; cursor: pointer"
                                        @click="goToSellerPage(ad?.user)"
                                    >
                                        <img
                                            :src="
                                                ad?.user?.logo ||
                                                'https://randomuser.me/api/portraits/men/32.jpg'
                                            "
                                            class="ad-details-advertiser-img"
                                        />
                                        <span
                                            class="ad-details-advertiser-name"
                                            style="vertical-align: super; padding: 4px"
                                        >
                                            <UserNameLink
                                                v-if="ad?.user"
                                                :user="ad.user"
                                            />
                                        </span>
                                        <VerifiedBadge
                                            v-if="ad?.user"
                                            :user="ad.user"
                                            compact
                                        />
                                        <span
                                            class="ad-details-advertiser-rating"
                                        >
                                            <span
                                                v-for="i in 5"
                                                :key="i"
                                                class="ad-details-star"
                                            >
                                                <i
                                                    class="fas fa-star"
                                                    :class="{
                                                        active:
                                                            i <=
                                                            ad?.user
                                                                ?.average_rating,
                                                    }"
                                                ></i>
                                            </span>
                                            <span
                                                class="ad-details-rating-value"
                                                >({{
                                                    ad?.user?.average_rating
                                                }})</span
                                            >
                                        </span>
                                    </span>
                                </div>
                                <div class="ad-details-row">
                                    <span>
                                        {{
                                            translations?.No_of_views ||
                                            "No of views"
                                        }}</span
                                    ><span
                                        >{{ ad.views }},
                                        {{
                                            translations?.views || "views"
                                        }}</span
                                    >
                                </div>
                            </div>
                            <div class="ad-details-other-title">
                                {{
                                    translations?.Other_Details ||
                                    "Other Details"
                                }}
                            </div>
                            <textarea
                                class="ad-details-other"
                                :value="ad.description"
                                readonly
                                :placeholder="
                                    translations?.Other_Details ||
                                    'Other Details'
                                "
                            ></textarea>

                            <div class="ad-details-actions-row" >
                                <button
                                    class="ad-details-action"
                                    @click="shareApp()"
                                >
                                    <i class="fas fa-share-alt"></i>
                                </button>
                                <!-- <button 
                                    class="ad-details-action"
                                    @click="copyLink()"
                                >
                                    <i class="fas fa-copy"></i>
                                </button> -->
                                <button
                                    v-if="isLoggedIn && ad.my_ad"
                                    class="ad-details-action"
                                    @click.stop="openDeleteModal(ad)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                                <button
                                    v-if="isLoggedIn && ad.my_ad"
                                    class="ad-details-action"
                                    @click.stop="openEditModalAd(ad)"
                                >
                                    <i class="fas fa-edit"></i>
                                </button> 



                                <a v-if="ad.mobile && !ad.my_ad"
                                  href="javascript:void(0)"
                                  class="ad-details-action"
                                  style="color: #25d366"
                                  @click.prevent="contactWhatsApp"
                                >
                                  <i class="fab fa-whatsapp"></i>
                                </a>

                                <!-- زر اتصال -->
                                <a v-if="ad.mobile && !ad.my_ad"
                                  href="javascript:void(0)"
                                  class="ad-details-action"
                                  style="color: #007bff"
                                  @click.prevent="contactCall"
                                >
                                  <i class="fas fa-phone-alt"></i>
                                </a>


                                <div v-if="!ad.my_ad"
                                    class="ad-details-action"
                                    :class="{ active: ad.is_favorite }"
                                    @click="toggleFavorite(ad)"
                                >
                                    <i class="far fa-heart"></i>
                                </div>

                                <button
                                    v-if="!ad.my_ad"
                                    type="button"
                                    class="ad-details-action"
                                    :title="translations?.report_ad || translations?.report || 'Report'"
                                    @click="openReportAdModal"
                                >
                                    <i class="fas fa-flag"></i>
                                </button>
                            </div>






                        </div>
                        <!-- التعليقات -->
                        <div class="ad-details-comments-card">
                            <div class="ad-details-comments-title">
                                {{ translations?.Comments || "Comments" }}
                            </div>
                            <div class="ad-details-comments-list">
                                <div
                                    v-for="(c, ndex) in comments"
                                    :key="c.id"
                                    class="ad-details-comment-row"
                                    :class="{
                                        owner: c.isOwner,
                                        other: !c.isOwner,
                                    }"
                                >
                                    <template v-if="c.isOwner">
                                        <div
                                            class="ad-details-comment-meta-block left-meta"
                                        >
                                            <img
                                                :src="c.user?.logo"
                                                class="ad-details-comment-avatar"
                                            />
                                            <div>
                                                <UserNameLink
                                                    v-if="c.user"
                                                    :user="c.user"
                                                    link-class="ad-details-comment-user owner-user"
                                                />
                                                <span
                                                    class="ad-details-comment-time"
                                                    >{{
                                                        c.created_at_human
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="ad-details-comment-bubble owner-bubble"
                                        >
                                            {{ c.body }}
                                            <button v-if="isLoggedIn && ad.my_ad"
                                                class="ad-details-comment-delete"
                                                @click="deleteComment(c.id)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div
                                            class="ad-details-comment-meta-block right-meta"
                                        >
                                            <img
                                                :src="c.user?.avatar"
                                                class="ad-details-comment-avatar"
                                            />
                                            <div>
                                                <UserNameLink
                                                    v-if="c.user"
                                                    :user="c.user"
                                                    link-class="ad-details-comment-user other-user"
                                                />
                                                <span
                                                    class="ad-details-comment-time"
                                                    >{{
                                                        c.created_at_human
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="ad-details-comment-bubble other-bubble"
                                        >
                                            {{ c.body }}
                                            <button
                                                class="ad-details-comment-menu"
                                                @click="
                                                    openCommentMenu(
                                                        c.id,
                                                        $event
                                                    )
                                                "
                                            >
                                                <i
                                                    class="fas fa-ellipsis-v"
                                                ></i>
                                            </button>

                                            <div
                                                v-if="
                                                    showCommentMenuId === c.id
                                                "
                                                class="modal-followers-popup-menu"
                                                :style="followerMenuStyle"
                                            >
                                                <div
                                                    class="followers-popup-item"
                                                    @click="
                                                        addReportComment(
                                                            'not serious about buying',
                                                            c.id
                                                        )
                                                    "
                                                >
                                                    {{
                                                        translations?.Not_serious_about_buying ||
                                                        "Not serious about buying"
                                                    }}
                                                </div>

                                                <!-- يظهر Block إذا المستخدم غير محظور -->
                                                <div
                                                    class="followers-popup-item"
                                                    @click="
                                                        addReportComment(
                                                            'inappropriate response',
                                                            c.id
                                                        )
                                                    "
                                                >
                                                    {{
                                                        translations?.Inappropriate_response ||
                                                        "Inappropriate response"
                                                    }}
                                                </div>

                                                <div
                                                    class="followers-popup-item"
                                                    @click="
                                                        addReportComment(
                                                            'undervaluation of the commodity',
                                                            c.id
                                                        )
                                                    "
                                                >
                                                    {{
                                                        translations?.Undervaluation_of_the_commodity ||
                                                        "Undervaluation of the commodity"
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div
                                        v-if="ndex < comments.length - 1"
                                        class="ad-details-comment-divider"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="ad-details-comments-input-row">
                            <input
                                class="ad-details-comments-input"
                                :placeholder="
                                    translations?.Leave_your_comments ||
                                    'Leave your comments here...!'
                                "
                                v-model="newComment"
                                @keyup.enter="addComment"
                            />
                            <button
                                class="ad-details-comments-send"
                                @click="addComment"
                            >
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- الجزء الجانبي الأيمن -->
                <div class="ad-details-sidebar">
                    <div class="ad-details-sidebar-card">
                        <div class="ad-details-similar-title">
                            {{ translations?.Similar_Ads || "Similar Ads " }}
                        </div>
                        <div class="ad-details-similar-list">
                            <div
                                class="ad-details-similar-item"
                                v-for="s in similarAds"
                                :key="s.id"
                            >
                                <img
                                    :src="s?.image_basic?.url"
                                    class="ad-details-similar-img"
                                    @click="goToAdDetails(s)"
                                    style="cursor: pointer"
                                />
                                <div class="ad-details-similar-info">
                                    <div
                                        class="ad-details-similar-title-main"
                                        @click="goToAdDetails(s)"
                                        style="cursor: pointer"
                                    >
                                        {{ s.title }}
                                    </div>


                                  <div class="small text-muted ownerProduct">
                                <div class="nameOwnerProductLocation">
                                <span
                                  class="fw-bold me-2"
                                  style="color: #e5aa2a"
                                  v-if="s.price">

                                  {{ s.price }}
                                  <img  v-if="locale=='ar'" src="/assets_website/logos/sar2.png" width="30" style="vertical-align: bottom;" />
                                  <span v-else>SAR</span>
                                </span>

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
                                        >{{ s.product_type_translated }}
                                    </span>

                                       <span class="badge text-dark ms-2">{{
                                                s.views
                                            }} {{ translations?.views || "views" }}
                                        </span>

 
                                </div>
                            </div>



                                    <div class="small text-muted ownerProduct">
                                        <i class="fas fa-user-circle me-1"></i>

                                        <UserNameLink
                                            v-if="s?.user"
                                            :user="s.user"
                                            link-class="fw-bold text-dark ownerProductName"
                                        />
                                        <span
                                            v-if="s?.city"
                                            class="ms-2 ownerProductLocation"
                                            >{{ s.country?.name }} - {{ s.city?.name }}
                                        </span>

                                        <!-- <span class="ms-2 ownerProductLocation"
                                            >{{ ad.updated_at_human }}
                                        </span> -->
                                    </div>

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showDeleteModal" class="modal-delete-backdrop">
            <div class="modal-delete-content">
                <div class="modal-delete-icon-circle">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="28" fill="#fff" />
                        <circle
                            cx="28"
                            cy="28"
                            r="24"
                            stroke="#FF2D2D"
                            stroke-width="3"
                            fill="#fff"
                        />
                        <text
                            x="50%"
                            y="54%"
                            text-anchor="middle"
                            fill="#FF2D2D"
                            font-size="32"
                            font-family="Arial"
                            dy=".3em"
                        >
                            !
                        </text>
                    </svg>
                </div>
                <div class="modal-delete-title">{{ titleDeleteAd }}</div>
                <div class="modal-delete-text modal-delete-gray">
                    {{ bodyDeleteAd }}
                </div>
                <div class="modal-delete-actions">
                    <button class="modal-delete-no" @click="NoDeleteModalAd()">
                        {{ translations?.No || "No" }}
                    </button>
                    <button class="modal-delete-yes" @click="YesDeleteModalAd">
                        {{ translations?.Yes || "Yes" }}
                    </button>
                </div>
            </div>
        </div>


        <div
            v-if="showEditModal"
            class="modal-account-backdrop"
            style="overflow: scroll"
        >
            <div
                class="modal-account-content"
                @click.stop
                style="margin-top: 400px"
            >
                <button
                    class="modal-account-close"
                    @click="showEditModal = false"
                >
                    &times;
                </button>
                <div class="modal-account-header">
                    <div class="add-ad-title-row">
                        <img
                            src="/assets/media/svg/brand-logos/logo.png"
                            class="add-ad-logo"
                        />
                        <div class="add-ad-title">
                            {{ translations?.update_ad || "Update an Ad" }}
                        </div>
                    </div>
                    <hr class="add-ad-divider" />
                    <form class="ad-form">
                        <div class="ad-form-upload">
                            <label class="ad-form-upload-box">
                                <input
                                    type="file"
                                    multiple
                                    accept="image/*"
                                    @change="onImageChange"
                                    style="display: none"
                                />
                                <div class="ad-form-upload-content">
                                    <i
                                        class="fas fa-camera ad-form-upload-icon"
                                    ></i>
                                    <span class="ad-form-upload-plus">+</span>
                                </div>
                            </label>
                        </div>
                        <div class="ad-form-upload-thumbs" v-if="images.length">
                            <div
                                v-for="(img, i) in images"
                                :key="i"
                                class="ad-form-thumb-wrap"
                            >
                                <img :src="img.url" class="ad-form-thumb" />
                                <span
                                    class="ad-form-thumb-remove"
                                    @click.stop="removeImage(img.id, i)"
                                    >&times;</span
                                >
                            </div>
                        </div>
                        <div class="ad-form-row">
                            <select
                                class="ad-form-input"
                                v-model="formAd.country_id"
                                required
                            >
                                <option value="" disabled>
                                    {{ translations?.select || "Select" }}
                                    {{ translations?.Country || "Country" }}
                                </option>

                                <option
                                    :value="country.id"
                                    v-for="(country, index) in countries"
                                >
                                    {{ country.name }}
                                </option>
                            </select>

                            <select
                                @click="fetchItemsCountryAd"
                                class="ad-form-input"
                                v-model="formAd.city_id"
                                required
                            >
                                <option value="" disabled>
                                    {{ translations?.select || "Select" }}
                                    {{ translations?.state || "City " }}
                                </option>
                                <option
                                    :value="city.id"
                                    v-for="city in cities"
                                    :key="city.id"
                                >
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>
                        <div class="ad-form-row">
                            <select
                                class="ad-form-input"
                                v-model="formAd.category_id"
                                @click="fetchItemsCategories()"
                                @change="onCategoryChangeEdit"
                            >
                                <option disabled value="">
                                    {{ translations?.category || "category" }}
                                </option>
                                <option
                                    :value="category.id"
                                    v-for="category in categories"
                                    :key="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <select
                                v-if="hasGenderFlow"
                                class="ad-form-input"
                                v-model="formAd.gender_id"
                                @change="onGenderChangeEdit"
                            >
                                <option disabled value="">
                                    {{ translations?.gender || "الجنس" }}
                                </option>
                                <option
                                    v-for="g in genders"
                                    :key="g.id"
                                    :value="g.id"
                                >
                                    {{ g.name }}
                                </option>
                            </select>
                            <select
                                v-else
                                class="ad-form-input"
                                v-model="formAd.brand_id"
                            >
                                <option disabled value="">
                                    {{ translations?.brand || 'Brand' }}
                                </option>
                                <option
                                    :value="brand.id"
                                    v-for="brand in brands"
                                    :key="brand.id"
                                >
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>
                        <div class="ad-form-row" v-if="hasGenderFlow">
                            <select
                                v-if="formAd.gender_id && subcategories.length"
                                class="ad-form-input"
                                v-model="formAd.subcategory_id"
                            >
                                <option disabled value="">
                                    {{ translations?.type || "النوع" }}
                                </option>
                                <option
                                    v-for="s in subcategories"
                                    :key="s.id"
                                    :value="s.id"
                                >
                                    {{ s.name }}
                                </option>
                            </select>
                            <select class="ad-form-input" v-model="formAd.brand_id">
                                <option disabled value="">
                                    {{ translations?.brand || 'Brand' }}
                                </option>
                                <option
                                    :value="brand.id"
                                    v-for="brand in brands"
                                    :key="brand.id"
                                >
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>
                        <div class="ad-form-row">
                            <input
                                class="ad-form-input"
                                placeholder="+966  (Optional)"
                                v-model="formAd.mobile"
                            />
                            <input
                                class="ad-form-input"
                                placeholder="SAR (Optional)"
                                v-model="formAd.price"
                            />
                        </div>
                        <div class="ad-form-row ad-form-invoice-row">
                            <div class="ad-form-invoice-box">
                                <label class="ad-form-invoice-label">
                                    <span class="ad-form-invoice-title">
                                        {{
                                            translations?.invoice || "Invoice"
                                        }}
                                        <span class="ad-form-invoice-optional"
                                            >({{
                                                translations?.optional ||
                                                "optional"
                                            }})</span
                                        ></span
                                    >
                                    <input
                                        type="file"
                                        accept="*"
                                        @change="onInvoiceChange"
                                        style="display: none"
                                        ref="invoiceInput"
                                    />

                                    <!-- إذا لا يوجد ملف، أظهر زر الرفع -->
                                    <span
                                        v-if="!formAd.invoice"
                                        class="ad-form-invoice-attach"
                                        @click="$refs.invoiceInput.click()"
                                    >
                                        <i class="fas fa-paperclip"></i>
                                    </span>

                                    <!-- إذا الملف موجود -->
                                    <span v-else class="ad-form-invoice-file">
                                        <!-- اسم الملف + رابط فتح -->
                                        <a
                                            :href="
                                                getInvoiceUrl(formAd.invoice)
                                            "
                                            target="_blank"
                                            rel="noopener"
                                            class="ad-form-invoice-filename"
                                            style="margin-right: 8px"
                                        >
                                            <i class="fas fa-file-alt"></i>
                                        </a>

                                        <!-- زر الحذف -->
                                    </span>
                                </label>

                                <span
                                    v-if="formAd.invoice"
                                    class="ad-form-invoice-remove"
                                    @click.stop="removeInvoice"
                                >
                                    &times;
                                </span>
                            </div>
                            <div
                                class="ad-form-invoice-check"
                                v-if="invoiceFile"
                            >
                                <svg
                                    width="40"
                                    height="40"
                                    viewBox="0 0 40 40"
                                    fill="none"
                                >
                                    <circle
                                        cx="20"
                                        cy="20"
                                        r="20"
                                        fill="#fff"
                                    />
                                    <circle
                                        @click="getInvoiceUrl(formAd.invoice)"
                                        cx="20"
                                        cy="20"
                                        r="16"
                                        stroke="#e5aa2a"
                                        stroke-width="3"
                                        fill="#fff"
                                    />
                                    <path
                                        d="M13 21L18 26L27 15"
                                        stroke="#e5aa2a"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>
                        </div>
                        <label class="ad-form-label">
                            {{
                                translations?.product_type || "Product Type"
                            }}</label
                        >
                        <div class="ad-form-row ad-form-product-type-row">
                            <div class="ad-form-product-type">
                                <div
                                    class="ad-form-type-option"
                                    :class="{
                                        active: formAd.product_type === 'new',
                                    }"
                                    @click="formAd.product_type = 'new'"
                                >
                                    <span class="ad-form-type-label">
                                        {{ translations?.new || "New" }}</span
                                    >
                                    <span class="ad-form-type-radio">
                                        <span
                                            class="ad-form-type-radio-inner"
                                            v-if="formAd.product_type === 'new'"
                                        ></span>
                                    </span>
                                </div>
                                <div
                                    class="ad-form-type-option"
                                    :class="{
                                        active: formAd.product_type === 'used',
                                    }"
                                    @click="formAd.product_type = 'used'"
                                >
                                    <span class="ad-form-type-label">
                                        {{ translations?.Used || "Used" }}</span
                                    >
                                    <span class="ad-form-type-radio">
                                        <span
                                            class="ad-form-type-radio-inner"
                                            v-if="
                                                formAd.product_type === 'used'
                                            "
                                        ></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <input
                            class="ad-form-input"
                            placeholder="Advertising title"
                            v-model="formAd.title"
                            maxlength="60"
                            @input="onTitleInput"
                        />
                        <div class="ad-form-footer-row" style="margin-top: 4px; margin-bottom: 8px;">
                            <span class="ad-form-max">{{ (formAd.title || '').length }}/60</span>
                        </div>
                        <textarea
                            class="ad-form-textarea"
                            placeholder="Other details to add an ad\nWrite other details here..."
                            v-model="formAd.description"
                        ></textarea>
                        <div class="ad-form-footer-row">
                            <span class="ad-form-max">
                                {{
                                    translations?.Maximum_of_500_words ||
                                    "Maximum of 500 words"
                                }}</span
                            >
                        </div>
                        <button class="ad-form-submit" @click="updateAd">
                            {{ translations?.update_ad || "Update Advertise" }}
                        </button>
                    </form>
                </div>
            </div>
        </div>



        <!-- Report Ad Modal -->
        <div
            v-if="showReportAdModal"
            class="modal-report-backdrop"
            @click.self="closeReportAdModal"
        >
            <div class="modal-report-content">
                <div class="modal-report-title">
                    {{
                        translations?.Why_are_you_Reporting ||
                        "Why are you Reporting?"
                    }}
                </div>
                <textarea
                    class="modal-report-textarea"
                    v-model="reportAdMessage"
                    rows="5"
                    :placeholder="
                        translations?.report_ad_placeholder ||
                        'Write your report reason here...'
                    "
                ></textarea>
                <button
                    class="modal-report-submit"
                    :disabled="isSubmittingReport"
                    @click="submitReportAd"
                >
                    {{ translations?.Submit_Now || "Submit Now" }}
                </button>
            </div>
        </div>

    </div>
</template>
<script>
import imgAdd from "@/assets/img-add.png";
import defaultImg from "@/assets/ad-details-img.png";
import { navigateToUserProfile } from "@/utils/userProfile";
import { requireLogin } from "@/utils/requireLogin";
import { fetchAvailableGenders } from "@/utils/categoryGenders";
import { api as axios } from "@/axiosUser";

export default {
    name: "AdDetails",
    data() {
        return {
            ad: {},
            images: [],
            mainImg: "",
            comments: [],
            newComment: "",
            similarAds: [],
            showCommentMenuId: "",
            titleDeleteAd: "Reason",
            bodyDeleteAd: "Do you want to delete the product due to sale?",
            showDeleteModal: false,
            showReportAdModal: false,
            reportAdMessage: "",
            isSubmittingReport: false,
            isLoggedIn: !!localStorage.getItem("token"),
            showEditModal: false,
             countries: [],
            cities: [],
            categories: [],
            brands: [],
            genders: [],
            subcategories: [],
            hasGenderFlow: false,
            logo: "",
            images: [],
             invoiceFile:"",
            formAd: {
                invoiceFile: "",
                country_id: "",
                city_id: "",
                category_id: "",
                brand_id: "",
                gender_id: "",
                subcategory_id: "",
            },
        };
    },
    computed: {
        displayImages() {
            
            let imgs = this.images.slice(0, 5);
            while (imgs.length < 5) imgs.push(defaultImg);
            return imgs;
        },
        orderedComments() {
            return this.comments.slice().sort((a, b) => a.id - b.id);
        },
        user() {
            return this.$store.getters.getUser;
        },
 
        setting() {
            return this.$store.getters.getSetting;
        },

        locale() {
            return this.$route.params.locale || "en";
        },
        translations() {
            return this.$store.getters.getTranslations || {};
        },
    },

    beforeDestroy() {
        this.ads = null;

        localStorage.removeItem("my_ads");
    },

    beforeUnmount() {
        document.removeEventListener("mousedown", this.handleClickOutside);
    },
    mounted() {
        document.addEventListener("mousedown", this.handleClickOutside);

        // this.fetchAd(this.$route.params.id);

        // const adData = localStorage.getItem('ad');
        // console.log(adData)
        //   if (adData) {
        //     this.ad = JSON.parse(adData);
        //     console.log(this.ad)
        //     this.mainImg = this.ad.image_basic.url

        //   } else {
        //     console.log("Error Happens");
        //   }

        this.$store.dispatch("fetchSetting");

        this.fetchItemsAdsOne();
        this.fetchItemsComments();
        this.fetchItemsSimilarAds();
    },
    methods: {
        goToSellerPage(user) {
            navigateToUserProfile(this.$router, this.locale, this.user, user);
        },

        onTitleInput(e) {
            let value = String(e?.target?.value ?? this.formAd.title ?? "");
            value = value.replace(/\s{2,}/g, " ");
            if (value.length > 60) {
                value = value.slice(0, 60);
            }
            this.formAd.title = value;
        },

        contactWhatsApp() {
            if (!requireLogin(this.$store)) {
                return;
            }
            if (this.ad?.mobile) {
                window.open(`https://wa.me/${this.ad.mobile}`, "_blank");
            }
        },

        contactCall() {
            if (!requireLogin(this.$store)) {
                return;
            }
            if (this.ad?.mobile) {
                window.location.href = `tel:${this.ad.mobile}`;
            }
        },

        handleClickOutside(event) {
            if (this.showCommentMenuId !== null) {
                const popup = document.querySelector(
                    ".modal-followers-popup-menu"
                );
                if (popup && !popup.contains(event.target)) {
                    this.showCommentMenuId = null;
                }
            }

            if (this.showEditModal) {
                const modal = document.querySelector(".modal-account-content");
                if (modal && !modal.contains(event.target)) {
                    if (!this.isEditing) {
                        this.showAccountModal = false;
                    }
                }
            }

            if (this.showDeleteModal) {
                const modal = document.querySelector(".modal-delete-content");
                if (modal && !modal.contains(event.target)) {
                    this.showDeleteModal = false;
                }
            }
        },

        shareApp() {
            if (navigator.share) {
                navigator
                    .share({
                        title: "Check out this app!",
                        text: "I found this awesome app, check it out!",
                        url: window.location.href, // ✅ الرابط الكامل للصفحة الحالية
                    })
                    .then(() => console.log("App shared successfully"))
                    .catch((error) => console.error("Error sharing", error));
            } else {
                alert("Sharing is not supported on this device/browser.");
            }
        },
        openEditModalAd(ad) {
            this.showEditModal = true;

            this.formAd = { ...ad };
            this.images = this.formAd.images || [];

            this.fetchItemsCountryAd();
            this.fetchItemsCategories();
        },

        copyLink() {
            const link = window.location.href;

            navigator.clipboard
                .writeText(link)
                .then(() => {
                    this.swalFunction("info", "Copied .");
                })
                .catch((err) => {
                    this.swalFunction("error", "Please Try Again.");
                });
        },

        openDeleteModal(ad) {
            this.ad = ad;
            this.showDeleteModal = true;
        },

        async NoDeleteModalAd() {
            this.isLoading = true;
            await axios
                .get("ads/deleteAd", {
                    params: {
                        ad_id: this.$route.params.id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        // this.showUpdateModal = true;
                        this.swalFunction("info", response.data.message);
                        this.$router.push("/");
                        // this.$router.back();
                    } else {
                        this.titleBlack = response.data.message;
                        this.bodyBlack = "Error";
                        this.showUpdateModal = true;
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    let message = "حدث خطأ ما";

                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.message
                    ) {
                        message = error.response.data.message;
                        this.titleBlack = message;
                    }

                    // this.bodyBlack = '';
                    this.showUpdateModal = true;
                    this.isLoading = false;
                });
        },

        async YesDeleteModalAd() {
            this.isLoading = true;

            await axios
                .get("ads/deleteAd", {
                    params: {
                        ad_id: this.$route.params.id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        this.goToPayCommission();
                    } else {
                        this.titleBlack = response.data.message;
                        this.bodyBlack = "Error";
                        this.showUpdateModal = true;
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    let message = "Error Happens";

                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.message
                    ) {
                        message = error.response.data.message;
                        this.titleBlack = message;
                    }

                    // this.bodyBlack = '';
                    this.showUpdateModal = true;
                    this.isLoading = false;
                });
        },

        goToPayCommission() {
            // this.$store.commit("setAdCurrent", this.formAd);
            localStorage.setItem("adPay", JSON.stringify(this.ad));
            this.$router.push({ path: `/${this.locale}/pay-commission` });
            this.showProfileMenu = false;
        },

        // fetchAd(id) {
        //   const ad = JSON.parse(localStorage.getItem('ad'));
        //   this.ad = ad;

        //   // اختياري: تحقق من ID إذا أردت فلترة أكثر دقة
        //   if (ad?.id != id) {
        //     console.warn("Mismatch between route ID and stored ad.");
        //   }
        // },



         async fetchItemsCountryAd() {
            this.isLoading = true;
            await axios
                .get("basic/getCountries")
                .then((response) => {
                    this.countries = response.data.items;

                    this.fetchItemsCityAd();

                    this.isLoading = false;
                    this.languages = this.$store.state.languages;
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
                        country_id: this.formAd.country_id,
                    },
                })
                .then((response) => {
                    if (this.formAd.country_id) {
                        this.cities = response.data.items;
                    } else {
                        this.countries = response.data.items;
                    }

                    this.isLoading = false;
                    this.languages = this.$store.state.languages;
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
                .then(async (response) => {
                    this.categories = response.data.items;
                    if (this.formAd.category_id) {
                        await this.fetchGendersForEdit();
                    } else {
                        await this.fetchItemsBrands();
                    }
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async onCategoryChangeEdit() {
            this.formAd.brand_id = "";
            this.formAd.gender_id = "";
            this.formAd.subcategory_id = "";
            this.genders = [];
            this.subcategories = [];
            this.hasGenderFlow = false;
            if (!this.formAd.category_id) {
                this.brands = [];
                return;
            }
            await this.fetchGendersForEdit();
        },

        async onGenderChangeEdit() {
            this.formAd.subcategory_id = "";
            this.subcategories = [];
            if (this.formAd.gender_id) {
                await this.fetchSubcategoriesForEdit();
            }
        },

        async fetchGendersForEdit() {
            if (!this.formAd.category_id) {
                this.genders = [];
                this.hasGenderFlow = false;
                return;
            }
            try {
                this.genders = await fetchAvailableGenders(
                    axios,
                    this.formAd.category_id
                );
                this.hasGenderFlow = this.genders.length > 0;
                if (
                    this.formAd.gender_id &&
                    !this.genders.some(
                        (g) => Number(g.id) === Number(this.formAd.gender_id)
                    )
                ) {
                    this.formAd.gender_id = "";
                    this.formAd.subcategory_id = "";
                    this.subcategories = [];
                } else if (this.formAd.gender_id) {
                    await this.fetchSubcategoriesForEdit();
                }
                await this.fetchItemsBrands();
            } catch (e) {
                this.genders = [];
                this.hasGenderFlow = false;
                await this.fetchItemsBrands();
            }
        },

        async fetchSubcategoriesForEdit() {
            if (!this.formAd.gender_id) {
                this.subcategories = [];
                return;
            }
            try {
                const res = await axios.get("basic/getSubcategories", {
                    params: { gender_id: this.formAd.gender_id },
                });
                this.subcategories = res.data?.items || [];
            } catch (e) {
                this.subcategories = [];
            }
        },

        async fetchItemsBrands() {
            this.isLoading = true;
            await axios
                .get("basic/getCategories", {
                    params: {
                        category_id: this.formAd.category_id,
                    },
                })
                .then((response) => {
                    if (this.formAd.category_id) {
                        this.brands = response.data.items;
                    } else {
                        this.categories = response.data.items;
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },


        async updateAd(e) {
            e.preventDefault();
            this.formAd.title = String(this.formAd.title || "")
                .replace(/\s{2,}/g, " ")
                .trim()
                .slice(0, 60);
            if (!this.formAd.title) {
                this.swalFunction(
                    "error",
                    this.translations?.title_required || "Title is required"
                );
                return;
            }
            try {
                const formData = new FormData();

                formData.append("ad_id", this.formAd.id);
                formData.append("title", this.formAd.title);
                formData.append("description", this.formAd.description);
                formData.append("category_id", this.formAd.category_id);
                formData.append("brand_id", this.formAd.brand_id ?? "");
                formData.append("country_id", this.formAd.country_id);
                formData.append("city_id", this.formAd.city_id);
                formData.append("mobile", this.formAd.mobile ?? "");
                formData.append("price", this.formAd.price ?? "");
                formData.append("gender_id", this.formAd.gender_id || "");
                formData.append("subcategory_id", this.formAd.subcategory_id || "");
                formData.append("product_type", this.formAd.product_type || "");

                if (this.invoiceFile) {
                    formData.append("invoice", this.invoiceFile);
                }

                if (this.images && this.images.length > 0) {
                    this.images.forEach((img, index) => {
                        if (img.file) {
                            formData.append(`images[${index}]`, img.file);
                        }
                    });
                }

                const response = await axios.post("/ads/update", formData, {
                    headers: {
                        "Content-Type":
                            formData instanceof FormData
                                ? "multipart/form-data"
                                : "application/json",
                    },
                });

                if (response.data.code === 200) {
                    this.swalFunction(
                        "success",
                        "Profile updated successfully"
                    );
                    this.formAd = response.data.items;

                    this.fetchItemsAdsOne();
                    this.fetchItemsComments();
                    this.fetchItemsSimilarAds();

                    this.showEditModal = false;
                    this.showProfileMenu = true;

                    this.isEditing = false;
                } else {
                    this.swalFunction("error", response.data.message);
                }
            } catch (error) {
                this.swalFunction(
                    "error",
                    error.response?.data?.message || "Error updating profile"
                );
            } finally {
                this.isUpdating = false;
            }
        },

        async addComment(e) {
            e.preventDefault();
            if (!requireLogin(this.$store)) {
                return;
            }
            try {
                const formData = new FormData();

                formData.append("ad_id", this.$route.params.id); // تأكد أن ID موجود داخل formAd
                formData.append("body", this.newComment);

                const response = await axios.post("/ads/addComment", formData, {
                    headers: {
                        "Content-Type":
                            formData instanceof FormData
                                ? "multipart/form-data"
                                : "application/json",
                    },
                });

                if (response.data.code === 200) {
                    this.fetchItemsComments();
                    this.newComment = "";
                } else {
                    // this.swalFunction("error", response.data.message);
                    if (response.data.message == "Unauthenticated.") {
                        this.$router.push(
                            "/" + this.$route.params.locale + "/login"
                        );
                    }
                }
            } catch (error) {
                if (error.response?.data?.message == "Unauthenticated.") {
                    this.$router.push(
                        "/" + this.$route.params.locale + "/login"
                    );
                } else {
                    this.swalFunction(
                        "error",
                        error.response?.data?.message || "Error add comments "
                    );
                }
            } finally {
                // this.loading = false;
            }
        }, 

        getInvoiceUrl(file) {
            if (file instanceof File) {
                return URL.createObjectURL(file);
            }

            return file;
        },

        async deleteComment(id) {
            this.isLoading = true;
            await axios
                .get("ads/deleteComment", {
                    params: {
                        comment_id: id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        this.fetchItemsComments();
                    } else {
                        if (response.data.message == "Unauthenticated.") {
                            this.$router.push(
                                "/" + this.$route.params.locale + "/login"
                            );
                        }
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    if (error.response?.data?.message == "Unauthenticated.") {
                        this.$router.push(
                            "/" + this.$route.params.locale + "/login"
                        );
                    } else {
                        this.swalFunction(
                            "error",
                            error.response?.data?.message ||
                                "Error add comments "
                        );
                    }
                    this.isLoading = false;
                });
        },

        selectImg(img) {
            this.mainImg = img;
        },

        swalFunction(type, text) {
            Swal.fire({
                text: text,
                icon: type,
                showConfirmButton: false, // إخفاء زر التأكيد
                timer: 1000, // يغلق تلقائياً بعد 2 ثانية
                timerProgressBar: true, // شريط مؤقت
                toast: true, // إشعار صغير مثل التوست
                position: "top-end", // المكان: أعلى يمين
                customClass: {
                    popup: "bg-light shadow-sm",
                },
            });
        },

        async fetchItemsAdsOne() {

            this.isLoading = true;
            await axios
                .get("ads/getAllItems", {
                    params: {
                        ad_id: this.$route.params.id,
                    },
                })

                .then((response) => {
                    this.ad = response.data.items;
                    this.mainImg = this.ad.image_basic.url;
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async fetchItemsComments() {
            this.isLoading = true;
            await axios
                .get("ads/getComments", {
                    params: {
                        ad_id: this.$route.params.id,
                    },
                })
                .then((response) => {
                    this.comments = response.data.items;
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        async fetchItemsSimilarAds() {
            this.isLoading = true;
            await axios
                .get("ads/getAdSimilar", {
                    params: {
                        ad_id: this.$route.params.id,
                    },
                })
                .then((response) => {
                    this.similarAds = response.data.items;
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        goToAdDetails(ad) {
            // this.$store.commit("setAdCurrent", ad);
            localStorage.setItem("ad", JSON.stringify(ad));
            this.$router.push({ path: `/${this.locale}/ad-details/${ad.id}` });
        },

        loadAdData(id) {
            const ad = JSON.parse(localStorage.getItem("ad"));
            this.ad = ad; // أو fetch باستخدام id
        },

        openCommentMenu(id, event) {
            this.showCommentMenuId = id;
            // Position the popup menu next to the button
            const rect = event.currentTarget.getBoundingClientRect();
            this.followerMenuStyle = {
                position: "fixed",
                top: rect.top + rect.height + 4 + "px",
                left: rect.left + "px",
                zIndex: 9999,
            };
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

        openReportAdModal() {
            if (!requireLogin(this.$store)) {
                return;
            }
            this.reportAdMessage = "";
            this.showReportAdModal = true;
        },

        closeReportAdModal() {
            this.showReportAdModal = false;
            this.reportAdMessage = "";
            this.isSubmittingReport = false;
        },

        async submitReportAd() {
            if (!requireLogin(this.$store)) {
                return;
            }

            const message = String(this.reportAdMessage || "").trim();
            if (message.length < 3) {
                this.swalFunction(
                    "info",
                    this.translations?.report_ad_required ||
                        "Please write a report reason."
                );
                return;
            }

            this.isSubmittingReport = true;
            try {
                const formData = new FormData();
                formData.append("ad_id", this.ad.id);
                formData.append("message", message);

                const response = await axios.post("/ads/reportAd", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                if (response.data.code === 200 || response.data.status === true) {
                    this.closeReportAdModal();
                    this.swalFunction(
                        "success",
                        response.data.message ||
                            this.translations?.report_ad_success ||
                            "Report submitted successfully."
                    );
                } else {
                    this.swalFunction(
                        "error",
                        response.data.message || "Error"
                    );
                }
            } catch (error) {
                this.swalFunction(
                    "error",
                    error.response?.data?.message || "Error"
                );
            } finally {
                this.isSubmittingReport = false;
            }
        },

        async addReportComment(report, comment_id) {
            // alert(report)
            // e.preventDefault();
            try {
                const formData = new FormData();

                formData.append("comment_id", comment_id);
                formData.append("report", report);

                const response = await axios.post(
                    "/ads/addReportComment",
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
                    // this.user = response.data.items;
                    // this.$store.commit('setUser', response.data.items);
                    this.showCommentMenuId = false;
                } else {
                    this.swalFunction("error", response.data.message);
                }
            } catch (error) {
                console.log(error);
                this.swalFunction(
                    "error",
                    error.response?.data?.message || "Error updating profile"
                );
            } finally {
                this.isUpdating = false;
            }
        },
    },

    watch: {
        "$route.params.id"(newId) {
            this.fetchAd(newId);
        },
    },
};
</script>
<style scoped>
.pay-commission-container {
    padding: 0;
}
.ad-details-bg {
    min-height: 100vh;
    background: #fafbfc;
    padding-bottom: 48px;
}
.ad-details-layout {
    display: flex;
    gap: 24px;
    margin: 24px auto;
    padding: 32px 16px;
}
.ad-details-main-content {
    flex: 2;
    min-width: 0;
}
.ad-details-sidebar {
    flex: 1;
    min-width: 300px;
}
.ad-details-sidebar-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
    padding: 24px;
}
.ad-details-header {
    display: none;
}
.ad-details-header-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
    max-width: 950px;
    margin: 32px auto 0 auto;
    padding: 32px 32px 18px 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    position: relative;
}
.ad-details-back {
    background: none;
    border: none;
    color: #222;
    font-size: 1.08rem;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    margin-bottom: 16px;
    font-weight: 500;
    transition: color 0.18s;
    /* margin-top: -80px;
    margin-bottom: 50px; */
}
.ad-details-back:hover {
    color: #e5aa2a;
}
.ad-details-logo-main {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    object-fit: contain;
    background: #1a2733;
    padding: 8px;
}
.ad-details-title-main-header {
    flex: 1;
    text-align: left;
    font-size: 2rem;
    font-weight: bold;
    color: #1a2733;
    letter-spacing: 0.5px;
    margin-left: 18px;
}
.ad-details-card {
    background: #fff;
    border-radius: 1rem;
    /* box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07); */
    /* max-width: 950px; */
    /* margin: 32px auto 0 auto; */
    /* padding: 32px 32px 24px 32px; */
    /* padding-top: 32px !important;
    padding-bottom: 32px !important; */
    padding: 24px !important;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
}
.pay-commission-header {
    display: flex;
    align-items: center;
    gap: 18px;
    justify-content: flex-start;
}
.pay-commission-logo {
    width: 54px;
    height: 54px;
    border-radius: 8px;
    object-fit: contain;
}
.pay-commission-title {
    flex: 1;
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #212f3a;
}
.pay-commission-divider {
    margin: 18px 0 18px 0;
    border: none;
    border-top: 2px solid #e5e5e5;
}
.ad-details-images-container {
    display: flex;
    gap: 10px;
    margin-bottom: 24px;
}

.ad-details-main-img-wrap {
    flex: 6;
    border-radius: 1rem;
    overflow: hidden;
    justify-content: center;
    height: 300px;
    position: relative;
    background: #f8f9fa;
}
.ad-details-main-logo-box {
    position: absolute;
    left: 24px;
    bottom: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
}
.ad-details-main-logo {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    background: #232f3a;
    object-fit: contain;
    margin-bottom: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.ad-details-main-logo-text {
    color: #bfa15a;
    font-size: 1.1rem;
    font-weight: bold;
    text-align: center;
    line-height: 1.1;
    letter-spacing: 0.5px;
    margin-top: 2px;
}
.ad-details-main-img {
    width: 100%;
    height: 100%;
    max-width: 100%;
    -o-object-fit: contain;
    object-fit: cover;
    display: block;
}
.ad-details-thumbs-grid {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 300px;
    overflow-y: auto;
}
.ad-details-thumb-wrap {
    box-sizing: border-box;
    transition: box-shadow 0.18s, border 0.18s;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
    height: 60px;
}
.ad-details-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: border 0.18s, opacity 0.18s;
    display: block;
    opacity: 0.8;
}
.ad-details-thumb.active {
    border: 2.5px solid #e5aa2a;
    opacity: 1;
    box-shadow: 0 0 0 2px #e5aa2a44;
    z-index: 2;
}
.ad-details-info-card {
    border: 1.5px solid #e5aa2a;
    border-radius: 8px;
    background: #fff;
    padding: 24px 18px 18px 18px;
    margin-bottom: 32px;
    margin-top: 18px;
    box-shadow: 0 2px 8px rgba(229, 170, 42, 0.04);
}
.ad-details-title-main {
    color: #e5aa2a;
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 1px;
    margin-top: 0;
    letter-spacing: 0.2px;
}
.ad-details-table {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 1.08rem;
    color: #222;
    border: none;
    padding: 0 0 0 0;
    border-collapse: separate;
    width: 100%;
}
.ad-details-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0 10px 0;
    border-bottom: 1px dashed #e5aa2a77;
    font-size: 1.08rem;
    color: #222;
    font-weight: 500;
    margin: 0 0;
}
.ad-details-row:last-child {
    border-bottom: none;
}
.ad-details-price {
    color: #222;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ad-details-price i {
    color: #e5aa2a;
    font-size: 1.1em;
}
.ad-details-time {
    color: #888;
    font-size: 0.98em;
    margin-right: 8px;
}
.ad-details-row-advertiser {
    align-items: flex-start;
}
.ad-details-advertiser {
    display: flex;
    align-items: center;
    gap: 8px;
}
.ad-details-advertiser-img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5aa2a;
}
.ad-details-advertiser-name {
    color: #e5aa2a;
    font-weight: bold;
    font-size: 1.08em;
}
.user-profile-link {
    cursor: pointer;
}
.user-profile-link:hover {
    color: #c9921f !important;
    text-decoration: underline;
}
.ad-details-advertiser-rating {
    display: flex;
    align-items: center;
    gap: 2px;
    margin-left: 6px;
    margin-top: 2px;
}
.ad-details-star {
    color: #ffd700;
    font-size: 1.1em;
}
.ad-details-star .fa-star {
    opacity: 0.5;
}
.ad-details-star .fa-star.active {
    color: #e5aa2a;
    opacity: 1;
}
.ad-details-rating-value {
    color: #888;
    font-size: 1em;
    margin-left: 2px;
}
.ad-details-other-title {
    color: #222;
    font-weight: bold;
    margin-bottom: 6px;
    margin-top: 18px;
    font-size: 1.15rem;
}
.ad-details-other {
    width: 100%;
    background: #fafbfc;
    border: 1.5px solid #e5aa2a33;
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 1.08rem;
    color: #888;
    outline: none;
    min-height: 60px;
    margin-bottom: 8px;
    resize: none;
    text-align: right;
}
.ad-details-actions-row {
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    margin-top: 12px;
}
.ad-details-action {
    background: #fafbfc;
    border: 1.5px solid #e5aa2a33;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #222;
    font-size: 1.18rem;
    cursor: pointer;
    transition: background 0.18s, color 0.18s, border 0.18s;
}
.ad-details-action:hover {
    background: #fffbe9;
    color: #e5aa2a;
    border: 1.5px solid #e5aa2a;
}
.ad-details-comments-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    border: none;
/*    border: 1.5px solid #f3f3f3;*/
    margin: 32px 0 18px 0;
/*    padding: 32px 32px 18px 32px;*/
    padding: 1px 1px 1px 3px;
}
.ad-details-comments-title {
    font-weight: bold;
    color: #222;
    font-size: 1.45rem;
    margin-bottom: 18px;
}
.ad-details-comments-list {
    margin-bottom: 12px;
}
.ad-details-comment-row {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    margin-bottom: 0;
    padding: 18px 0 0 0;
}
.ad-details-comment-row.owner {
    align-items: flex-end;
}
.ad-details-comment-row.other {
    align-items: flex-start;
}
.ad-details-comment-meta-block {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}
.ad-details-comment-meta-block.left-meta {
    justify-content: flex-start;
}
.ad-details-comment-meta-block.right-meta {
    justify-content: flex-end;
}
.ad-details-comment-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.ad-details-comment-meta-text {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
}
.ad-details-comment-user {
    font-weight: bold;
    font-size: 1.13rem;
    margin-bottom: 0;
}
.ad-details-comment-user.owner-user {
    color: #222;
}
.ad-details-comment-user.other-user {
    color: #e5aa2a;
}
.ad-details-comment-time {
    color: #bbb;
    font-size: 0.98rem;
    margin-left: 6px;
}
.ad-details-comment-bubble {
    width: 75%;
    background: #ededed;
    border-radius: 7px;
    padding: 2px 15px;
    color: #222;
    font-size: 1rem;
    position: relative;
    display: flex;
    align-items: center;
    min-height: 42px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    word-break: break-word;
    margin-bottom: 0;
    border: 1.5px solid #ededed;
}
.ad-details-comment-bubble.owner-bubble {
    background: #fdf8ee;
    color: #222;
    border: 1.5px solid #e5aa2a44;
    margin-left: 0;
}
.ad-details-comment-bubble.other-bubble {
    background: #ededed;
    color: #222;
    border: 1.5px solid #ededed;
    margin-right: 0;
}
.ad-details-comment-delete {
    background: #fff0f0;
    border: none;
    color: #ff5a5a;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-left: 18px;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
}
.ad-details-comment-delete i {
    color: #ff5a5a;
}
.ad-details-comment-delete:hover {
    background: #ffeded;
    color: #d32f2f;
}
.ad-details-comment-menu {
    background: none;
    border: none;
    color: #bbb;
    font-size: 1.35rem;
    margin-left: 18px;
    cursor: pointer;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.18s, color 0.18s;
}
.ad-details-comment-menu:hover {
    background: #f7f7f7;
    color: #888;
}
.ad-details-comment-divider {
    width: 100%;
    height: 1px;
    background: #f3f3f3;
    margin: 18px 0 0 0;
    border-radius: 2px;
}
.ad-details-comments-input-row {
    display: flex;
    align-items: center;
    gap: 0;
    margin-top: 18px;
    background: #fff;
    border: 2px solid #e5aa2a;
    border-radius: 8px;
    padding: 0 0 0 0;
    box-shadow: 0 2px 8px rgba(229, 170, 42, 0.04);
    height: 60px;
    position: relative;
}
.ad-details-comments-input {
    flex: 1;
    background: #fff;
    border: none;
    border-radius: 8px;
    padding: 0px 60px 0px 22px;
    font-size: 1.08rem;
    color: #222;
    outline: none;
    height: 100%;
    box-sizing: border-box;
}
.ad-details-comments-input::placeholder {
    color: #888;
    opacity: 1;
    font-size: 1.08rem;
}
.ad-details-comments-send {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    width: 54px;
    height: 54px;
    min-width: 54px;
    min-height: 54px;
    margin: 0 -7px 0 0;
    font-size: 2rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(229, 170, 42, 0.08);
    transition: background 0.18s, box-shadow 0.18s;
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
}
.ad-details-comments-send i.fas.fa-paper-plane {
    color: #fff;
}
.ad-details-comments-send:hover {
    background: #d49a1a;
    box-shadow: 0 4px 16px rgba(229, 170, 42, 0.13);
}
.ad-details-similar-title {
    font-weight: bold;
    color: #222;
    font-size: 1.15rem;
    margin: 32px 0 12px 0;
}
.ad-details-similar-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.ad-details-similar-item {
    display: flex;
    align-items: center;
    background: #f7f7f9;
    border-radius: 8px;
    padding: 10px 18px;
    gap: 18px;
}
.ad-details-similar-img {
    width: 54px;
    height: 54px;
    border-radius: 8px;
    object-fit: cover;
    background: #fff;
}
.ad-details-similar-info {
    flex: 1;
}
.ad-details-similar-title-main {
    font-weight: 500;
    font-size: 1.08em;
    color: #222;
}
.ad-details-similar-meta {
    color: #888;
    font-size: 0.98em;
}
.ad-details-similar-price {
    color: #e5aa2a;
    font-weight: bold;
    font-size: 1.08em;
}
@media (max-width: 900px) {
    .ad-details-layout {
        flex-direction: column;
        gap: 16px;
        padding: 16px 8px;
    }
    .ad-details-main-content {
        flex: none;
    }
    .ad-details-sidebar {
        flex: none;
        min-width: auto;
    }
    .ad-details-sidebar-card {
        position: static;
        top: auto;
    }
    .ad-details-card {
        max-width: 100vw;
        padding: 12px 2vw 12px 2vw;
    }
    .ad-details-images-container {
        flex-direction: column;
        gap: 12px;
    }
    .ad-details-main-img-wrap {
        height: 200px;
        flex: none;
    }
    .ad-details-thumbs-grid {
        flex: none;
        flex-direction: row;
        max-height: none;
        overflow-y: visible;
        overflow-x: auto;
    }
    .ad-details-thumb-wrap {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
    }
}

.modal-followers-menu-btn:hover {
    background: #e5aa2a22;
}
.modal-followers-popup-menu {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 18px 32px;
    min-width: 170px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.followers-popup-item {
    font-size: 1.15rem;
    color: #222;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s;
    border-radius: 8px;
    padding: 2px 0 2px 0;
    text-align: left;
}
.followers-popup-item:hover {
    background: #f6f6f6;
}
.popup-divider {
    border: none;
    border-top: 1.5px solid #adadad;
    margin: 0px 0 0px 0;
}

.modal-delete-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2500;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-delete-content {
    background: #fff;
    border-radius: 1.3rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 32px 32px 24px 32px;
    min-width: 340px;
    text-align: center;
    position: relative;
}
.modal-delete-icon-circle {
    z-index: 2;
    width: 80px;
    height: 80px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 5px auto 5px auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    position: absolute;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    top: 0;
}
.modal-delete-title {
    color: #1a2733;
    font-size: 1.35rem;
    font-weight: bold;
    margin-top: 24px;
    margin-bottom: 8px;
}
.modal-delete-gray {
    color: #888;
    font-size: 1.08rem;
    margin-bottom: 22px;
    margin-top: 0;
    font-weight: 500;
}
.modal-delete-actions {
    display: flex;
    gap: 18px;
    justify-content: center;
}
.modal-delete-no {
    background: #1a2733;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 38px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-delete-no:hover {
    background: #222f3a;
}
.modal-delete-yes {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 38px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-delete-yes:hover {
    background: #d49a1a;
}
.modal-update-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2600;
    display: flex;
    align-items: center;
    justify-content: center;
}



.modal-account-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2100;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-account-content {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 32px 32px 24px 32px;
    min-width: 690px;
    max-width: 98vw;
    position: relative;
    text-align: center;
}
.modal-account-close {
    position: absolute;
    top: 18px;
    right: 24px;
    background: none;
    border: none;
    font-size: 2rem;
    color: #888;
    cursor: pointer;
    z-index: 2;
}
.modal-account-header {
    margin-bottom: 18px;
}
.modal-account-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e5aa2a;
}

.avatar-upload-container {
    position: relative;
    display: inline-block;
}

.avatar-upload-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.avatar-upload-container:hover .avatar-upload-overlay {
    opacity: 1;
}

.avatar-upload-btn {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s;
}

.avatar-upload-btn:hover {
    background: #d49a1a;
}

.avatar-upload-btn i {
    font-size: 1.2rem;
}
.modal-account-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
    margin-top: 18px;
}
.modal-account-field {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 6px;
    padding: 0 18px;
    height: 48px;
    font-size: 1.1rem;
    color: #222;
    position: relative;
}
.modal-account-field i {
    margin-right: 12px;
    color: #bdbdbd;
    font-size: 1.15em;
}
.modal-account-field input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 1.08em;
    color: #222;
    padding: 0;
}
.modal-account-field input[readonly] {
    cursor: default;
}

.modal-account-eye {
    position: absolute;
    right: 12px;
    color: #bdbdbd;
    font-size: 1.1em;
    cursor: pointer;
}
.modal-account-edit {
    margin-top: 18px;
    width: 100%;
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px 0;
    font-size: 1.15rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-account-edit:hover {
    background: #d49a1a;
}

.modal-account-actions {
    margin-top: 18px;
}

.modal-account-save {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 1.15rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
    flex: 1;
}

.modal-account-save:hover {
    background: #d49a1a;
}

.modal-account-save:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.modal-account-cancel {
    background: #6c757d;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 1.15rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
    flex: 1;
}

.modal-account-cancel:hover {
    background: #5a6268;
}

.d-flex {
    display: flex;
}

.gap-2 {
    gap: 0.5rem;
}


.profile-card {
    background: #f5f5f5;
    border-radius: 8px;
    padding: 32px 0 16px 0;
    text-align: center;
    position: relative;
    margin-bottom: 32px;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e5aa2a;
    margin-bottom: 12px;
}
.profile-name {
    font-size: 1.4rem;
    font-weight: bold;
    margin-bottom: 8px;
}
.profile-menu {
    position: absolute;
    top: 24px;
    right: 32px;
    z-index: 2;
}
.profile-menu-btn {
    background: #1a2733;
    color: #e5aa2a;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 1.3rem;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
}
.profile-menu-btn:hover {
    background: #222f3a;
    color: #ffd24d;
}
.profile-menu-btn i {
    color: #e5aa2a !important;
    font-size: 1.3rem;
}
.profile-popup-menu {
    position: absolute;
    top: 32px;
    right: 0;
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 18px 28px 18px 28px;
    min-width: 210px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.popup-menu-item {
    font-size: 1.25rem;
    color: #222;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s;
    border-radius: 8px;
    padding: 2px 10px 2px 10px;
    text-align: left;
}
.popup-menu-item:hover {
    background: #f6f6f6;
}
.popup-menu-item.invoice {
    color: #212f3a;
}
.profile-rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
}
.stars {
    color: #ffd700;
    font-size: 1.1em;
}
.stars .fa-star {
    opacity: 0.5;
}
.stars .fa-star.active {
    color: #e5aa2a;
}
.rating-value {
    color: #888;
    font-size: 1em;
}
.profile-follow {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 32px;
    margin-bottom: 8px;
}
.profile-follow-count {
    font-weight: bold;
    font-size: 1.1em;
}
.profile-follow-label {
    color: #888;
    font-size: 0.95em;
}
.profile-share {
    color: #1a2733;
    font-size: 1.3em;
    cursor: pointer;
}
.profile-share i {
    color: #1a2733 !important;
}
.profile-joined {
    color: #888;
    font-size: 0.95em;
    margin-top: 8px;
}
.my-ads-section {
    margin-top: 32px;
}
.my-ads-title {
    font-weight: bold;
    margin-bottom: 18px;
}
.ad-card {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 6px;
    border: 1.5px solid #e5e5e5;
    margin-bottom: 16px;
    padding: 14px 18px;
    gap: 18px;
}
.ad-img {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    object-fit: cover;
    background: #f7e7ff;
}
.ad-info {
    flex: 1;
}
.ad-title {
    font-weight: 500;
    font-size: 1.1em;
}
.ad-time {
    color: #888;
    font-size: 0.95em;
}
.ad-actions {
    display: flex;
    gap: 12px;
    font-size: 0.98em;
}
.ad-action {
    color: #1a2733;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.18s;
}
.ad-action:hover {
    color: #e5aa2a;
}
.modal-rating-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-rating-content {
    background: #f7f7f9;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 36px 38px 32px 38px;
    min-width: 320px;
    text-align: center;
}
.modal-rating-title {
    font-size: 2.1rem;
    font-weight: bold;
    color: #212f3a;
    margin-bottom: 10px;
}
.modal-rating-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
    margin-bottom: 10px;
}
.modal-rating-label {
    color: #888;
    font-size: 1.15rem;
    margin-right: 6px;
}
.modal-rating-stars {
    font-size: 2.1rem;
    display: flex;
    gap: 4px;
}
.modal-rating-stars .fa-star {
    color: #ffd700;
    opacity: 1;
    cursor: pointer;
    transition: color 0.18s, opacity 0.18s;
}
.modal-rating-stars .fa-star.inactive {
    color: #e0e0e0;
    opacity: 1;
}
.modal-rating-stars .fa-star.active {
    color: #e5aa2a;
}
.modal-rating-ok {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 38px;
    font-size: 1.15rem;
    font-weight: 500;
    margin-top: 8px;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-rating-ok:hover {
    background: #d49a1a;
}



/* Enhanced Responsive Design */
@media (max-width: 1200px) {
    .my-page-container {
        padding: 16px;
    }

    .profile-card {
        max-width: 95%;
    }

    .ads-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .my-page-container {
        padding: 12px;
    }

    .profile-card {
        max-width: 98%;
        padding: 20px;
    }

    .ads-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .profile-name {
        font-size: 1.8rem;
    }
}

@media (max-width: 768px) {
    .my-page-container {
        padding: 8px;
    }

    .profile-card {
        padding: 16px;
    }

    .ads-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .profile-name {
        font-size: 1.6rem;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
    }

    .ads-card {
        min-height: 280px;
    }
}

@media (max-width: 600px) {
    .modal-account-content {
        min-width: 0;
        padding: 18px 4vw 18px 4vw;
    }

    .my-page-container {
        padding: 4px;
    }

    .profile-card {
        padding: 12px;
    }

    .ads-grid {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 8px;
    }

    .profile-name {
        font-size: 1.4rem;
    }

    .profile-avatar {
        width: 70px;
        height: 70px;
    }

    .ads-card {
        min-height: 260px;
    }
}
.modal-invoice-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2200;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-invoice-content {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 32px 32px 24px 32px;
    min-width: 700px;
    max-width: 98vw;
    position: relative;
    text-align: center;
}
.modal-invoice-close {
    position: absolute;
    top: 18px;
    right: 24px;
    background: none;
    border: none;
    font-size: 2rem;
    color: #888;
    cursor: pointer;
    z-index: 2;
}
.modal-invoice-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 24px;
    margin-bottom: 10px;
}
.modal-invoice-back {
    background: none;
    border: none;
    color: #888;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}
.modal-invoice-logo {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: contain;
}
.modal-invoice-title {
    flex: 1;
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #212f3a;
}
.modal-invoice-divider {
    margin: 18px 0 24px 0;
    border: none;
    border-top: 2px solid #e5e5e5;
}
.modal-invoice-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.modal-invoice-item {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 10px;
    padding: 0 18px;
    height: 48px;
    font-size: 1.1rem;
    color: #222;
    position: relative;
    justify-content: space-between;
}
.modal-invoice-icon {
    color: #bdbdbd;
    font-size: 1.25em;
    margin-right: 12px;
}
.modal-invoice-text {
    flex: 1;
    text-align: left;
    color: #888;
    font-size: 1.08em;
}
.modal-invoice-clip {
    color: #bdbdbd;
    font-size: 1.15em;
    margin-left: 12px;
}
@media (max-width: 900px) {
    .modal-invoice-content {
        min-width: 0;
        padding: 18px 4vw 18px 4vw;
    }
}
.modal-followers-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 300;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-followers-content {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 32px 32px 24px 32px;
    min-width: 600px;
    max-width: 98vw;
    position: relative;
    text-align: center;
}
.modal-followers-close {
    position: absolute;
    top: 18px;
    right: 24px;
    background: none;
    border: none;
    font-size: 2rem;
    color: #888;
    cursor: pointer;
    z-index: 2;
}
.modal-followers-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}
.modal-followers-logo {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    object-fit: contain;
}
.modal-followers-title {
    font-size: 1.6rem;
    font-weight: bold;
    color: #212f3a;
}
.modal-followers-divider {
    margin: 18px 0 18px 0;
    border: none;
    border-top: 2px solid #e5e5e5;
}
.modal-followers-search {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 8px;
    padding: 0 18px;
    height: 40px;
    margin-bottom: 18px;
    font-size: 1.1rem;
    color: #888;
    gap: 5px;
}
.modal-followers-search i {
    margin-right: 10px;
}
.modal-followers-search input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 1.08em;
    color: #222;
    padding: 0;
}
.modal-followers-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.modal-followers-item {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 10px;
    padding: 0 18px;
    height: 56px;
    font-size: 1.1rem;
    color: #222;
    position: relative;
    justify-content: flex-start;
    gap: 18px;
}
.modal-followers-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
}
.modal-followers-name {
    flex: 1;
    text-align: left;
    color: #222;
    font-size: 1.08em;
}
[dir="rtl"] .modal-followers-name {
    text-align: right;
}
.modal-followers-menu-btn {
    background: none;
    border: none;
    color: #888;
    font-size: 1.3rem;
    cursor: pointer;
    border-radius: 8px;
    padding: 8px 12px;
    transition: background 0.18s;
}
.modal-followers-menu-btn:hover {
    background: #e5aa2a22;
}
.modal-followers-popup-menu {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 18px 32px;
    min-width: 170px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.followers-popup-item {
    font-size: 1.15rem;
    color: #222;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s;
    border-radius: 8px;
    padding: 2px 0 2px 0;
    text-align: left;
}
.followers-popup-item:hover {
    background: #f6f6f6;
}
.popup-divider {
    border: none;
    border-top: 1.5px solid #adadad;
    margin: 0px 0 0px 0;
}
.modal-report-backdrop,
.modal-block-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2400;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-report-content,
.modal-block-content {
    background: #f5f5f5;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 36px 38px 32px 38px;
    min-width: 340px;
    text-align: center;
}
.modal-report-title,
.modal-block-title {
    font-size: 2rem;
    font-weight: bold;
    color: #212f3a;
    margin-bottom: 18px;
}
.modal-report-textarea {
    width: 100%;
    min-height: 80px;
    border-radius: 8px;
    border: none;
    padding: 16px;
    font-size: 1.1rem;
    margin-bottom: 18px;
    resize: none;
    background: #fff;
    color: #222;
}
.modal-report-submit {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 38px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-report-submit:hover {
    background: #d49a1a;
}
.modal-block-desc {
    color: #888;
    font-size: 1.15rem;
    margin-bottom: 18px;
}
.modal-block-btn {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 38px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-block-btn:hover {
    background: #d49a1a;
}
@media (max-width: 700px) {
    .modal-followers-content {
        min-width: 0;
        padding: 18px 4vw 18px 4vw;
    }
}
.modal-delete-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2500;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-delete-content {
    background: #fff;
    border-radius: 1.3rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 32px 32px 24px 32px;
    min-width: 340px;
    text-align: center;
    position: relative;
}
.modal-delete-icon-circle {
    z-index: 2;
    width: 80px;
    height: 80px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 5px auto 5px auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    position: absolute;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    top: 0;
}
.modal-delete-title {
    color: #1a2733;
    font-size: 1.35rem;
    font-weight: bold;
    margin-top: 24px;
    margin-bottom: 8px;
}
.modal-delete-gray {
    color: #888;
    font-size: 1.08rem;
    margin-bottom: 22px;
    margin-top: 0;
    font-weight: 500;
}
.modal-delete-actions {
    display: flex;
    gap: 18px;
    justify-content: center;
}
.modal-delete-no {
    background: #1a2733;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 38px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-delete-no:hover {
    background: #222f3a;
}
.modal-delete-yes {
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 38px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
}
.modal-delete-yes:hover {
    background: #d49a1a;
}
.modal-update-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.13);
    z-index: 2600;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-update-content {
    background: #fff;
    border-radius: 1.3rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    padding: 38px 38px 32px 38px;
    min-width: 340px;
    text-align: center;
    position: relative;
}
.modal-update-icon-wrap {
    width: 80px;
    height: 80px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 5px auto 5px auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    position: absolute;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    top: 0;
}
.modal-update-text {
    color: #222;
    font-size: 1.25rem;
    margin-bottom: 22px;
    margin-top: 22px;
    font-weight: 500;
}
.modal-update-ok {
    background: #1a2733;
    color: #fff;
    border: none;
    border-radius: 16px;
    padding: 14px 0;
    width: 90%;
    font-size: 1.2rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s;
    margin: 0 auto;
    display: block;
}
.modal-update-ok:hover {
    background: #222f3a;
}

.add-ad-bg {
    min-height: 100vh;
    background: #fafbfc;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 48px 0 64px 0;
}
.add-ad-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
    padding: 38px 38px 32px 38px;
    min-width: 420px;
    max-width: 710px;
    width: 100%;
    margin-top: 18px;
    position: relative;
}
.add-ad-back {
    background: none;
    border: none;
    color: #222;
    font-size: 1.08rem;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.18s;
    margin-top: -80px;
    margin-bottom: 50px;
}
.add-ad-title-row {
    display: flex;
    align-items: center;
    gap: 18px;
    justify-content: center;
    margin-bottom: 12px;
    margin-top: 0;
}
.add-ad-logo {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: contain;
}
.add-ad-title {
    flex: 1;
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: #222;
}
.add-ad-divider {
    margin: 18px 0 18px 0;
    border: none;
    border-top: 2px solid #e5e5e5;
}
.add-ad-condition-block {
    background: #f7f7f9;
    border-radius: 12px;
    padding: 18px 18px 10px 18px;
    margin-bottom: 18px;
}
.add-ad-condition-text {
    color: #222;
    font-size: 1.08rem;
    margin-bottom: 12px;
    font-weight: bold;
    line-height: 1.7;
}
.add-ad-actions {
    display: flex;
    gap: 0;
    margin-bottom: 0;
    justify-content: center;
    margin-top: 8px;
}
.add-ad-btn {
    font-weight: 700;
    font-size: 1.08rem;
    border: none;
    outline: none;
    padding: 10px 36px;
    border-radius: 8px 8px;
    background: #cbcbd4;
    color: #1a2733;
    transition: background 0.18s, color 0.18s;
    cursor: pointer;
    box-shadow: none;
    margin: 4px;
}
.add-ad-btn.active {
    background: #e5aa2a;
    color: #fff;
    z-index: 1;
}
.add-ad-btn.disagree {
    background: #e2e2e2;
    color: #1a2733;
    border-radius: 8px 8px;
}
.add-ad-continue {
    width: 100%;
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 14px 0;
    font-size: 1.15rem;
    font-weight: 500;
    cursor: pointer;
    margin-top: 24px;
    transition: background 0.18s;
}
.add-ad-continue:disabled {
    background: #e5aa2a77;
    color: #fff;
    cursor: not-allowed;
}
/* --- Add Form Styles --- */
.ad-form-upload {
    margin-bottom: 18px;
}
.ad-form-upload-box {
    width: 100%;
    min-height: 85px;
    background: #f7f7f9;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    overflow-x: auto;
    padding: 12px 0 20px;
}
.ad-form-upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    pointer-events: none;
}
.ad-form-upload-icon {
    font-size: 3.2rem;
    color: #7c7c7c1f;
    margin-bottom: 0px;
}
.ad-form-upload-plus {
    font-size: 1.3rem;
    color: #88888863;
    font-weight: bold;
    margin-top: -33px;
}
.ad-form-upload-thumbs {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 18px;
}
.ad-form-thumb-wrap {
    position: relative;
    display: inline-block;
}
.ad-form-thumb {
    width: 54px;
    height: 54px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e5aa2a;
    background: #fff;
}
.ad-form-thumb-remove {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #fff;
    color: #e53935;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    border: 1px solid #eee;
}
.ad-form-gold-row {
    display: flex;
    gap: 18px;
    margin-bottom: 18px;
}
.ad-form-gold-row .ad-form-gold-input {
    flex: 1;
    min-width: 0;
}
.ad-form-gold-row .ad-form-weight-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
}
.ad-form-gold-row .ad-form-weight-wrap .ad-form-input {
    flex: 1;
}
.ad-form-weight-label {
    font-size: 0.9rem;
    color: #666;
    white-space: nowrap;
}
.ad-form-row {
    display: flex;
    gap: 18px;
    margin-bottom: 18px;
}
.ad-form-input {
    flex: 1;
    background: #f7f7f9;
    border: none;
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 1.08rem;
    color: #222;
    outline: none;
    width: 100%;
}
.ad-form-invoice-row {
    align-items: center;
    gap: 12px;
}
.ad-form-invoice-box {
    flex: 2;
    background: #f7f7f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding: 0 18px;
    height: 54px;
    position: relative;
}
.ad-form-invoice-label {
    display: flex;
    align-items: center;
    width: 100%;
    cursor: pointer;
}
.ad-form-invoice-title {
    color: #222;
    font-weight: 600;
    font-size: 1.08rem;
    margin-right: 8px;
}
.ad-form-invoice-optional {
    color: #bbb;
    font-size: 1.05rem;
    font-weight: 400;
    margin-left: 4px;
}
.ad-form-invoice-attach {
    color: #222;
    font-size: 1.25rem;
    margin-left: 12px;
    cursor: pointer;
    transition: color 0.18s;
}
.ad-form-invoice-attach:hover {
    color: #e5aa2a;
}
.ad-form-invoice-file {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: 12px;
}
.ad-form-invoice-filename {
    color: #888;
    font-size: 1.05rem;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.ad-form-invoice-remove {
    color: #e53935;
    font-size: 1.2rem;
    cursor: pointer;
    margin-left: 4px;
    font-weight: bold;
}
.ad-form-invoice-check {
    background: #fff;
    border-radius: 8px;
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(229, 170, 42, 0.1);
}
.ad-form-label {
    font-weight: bold;
    color: #222;
    margin-bottom: 8px;
    font-size: 1.08rem;
    flex: 1;
}
.ad-form-product-type-row {
    align-items: flex-start;
    margin-bottom: 18px;
}
.ad-form-product-type {
    display: flex;
    gap: 24px;
    flex: 2;
}
.ad-form-type-option {
    background: #f7f7f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8.7px 16px;
    min-width: 180px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border 0.18s, background 0.18s;
    font-weight: 500;
    color: #222;
    position: relative;
    width: 100%;
}
.ad-form-type-option.active {
    border: 1px solid #e5aa2a;
    background: #fffbe9;
    color: #e5aa2a;
}
.ad-form-type-label {
    font-size: 1.08rem;
    font-weight: 600;
    color: inherit;
}
.ad-form-type-radio {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid #cbcbd4;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 18px;
    transition: border 0.18s;
}
.ad-form-type-option.active .ad-form-type-radio {
    border: 2.5px solid #e5aa2a;
}
.ad-form-type-radio-inner {
    width: 16px;
    height: 16px;
    background: #e5aa2a;
    border-radius: 50%;
    display: block;
}
.ad-form-textarea {
    width: 100%;
    background: #f7f7f9;
    border: none;
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 1.08rem;
    color: #222;
    outline: none;
    min-height: 70px;
    margin-bottom: 8px;
    margin-top: 18px;
    resize: vertical;
}
.ad-form-footer-row {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 18px;
}
.ad-form-max {
    color: #bbb;
    font-size: 0.98rem;
}
.ad-form-submit {
    width: 100%;
    background: #e5aa2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 14px 0;
    font-size: 1.15rem;
    font-weight: 500;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.18s;
}
.ad-form-submit:hover {
    background: #d49a1a;
}
@media (max-width: 700px) {
    .add-ad-card {
        min-width: 0;
        padding: 18px 4vw 18px 4vw;
    }
}

.swal2-container {
    z-index: 30000 !important;
}

.swal2-popup {
    z-index: 2500 !important;
}
</style>
