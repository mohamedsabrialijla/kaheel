<template>
    <div class="my-page-container">
        <div class="profile-card">
            <div class="profile-avatar-wrap">
                <img
                    class="profile-avatar"
                    v-if="user && user.logo"
                    :src="user.logo"
                />
                <span
                    v-if="user?.online"
                    class="profile-online-dot"
                    aria-hidden="true"
                ></span>
            </div>
            <div class="profile-name">
                {{ user ? user.name : "Loading..." }}
            </div>
            <div v-if="user?.online" class="profile-last-seen">
                {{ translations?.Last_seen_now || "Last seen now" }}
            </div>
            <VerifiedBadge v-if="user" :user="user" />
            <div class="profile-menu" ref="profileMenu">
                <button class="profile-menu-btn" @click="toggleProfileMenu">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div v-if="showProfileMenu" class="profile-popup-menu">
                    <!--  <div class="popup-menu-item" @click="openRatingModal">
                        Recent Rating
                    </div>  -->
                    <div class="popup-menu-item" @click="goToPayCommission">
                        {{ translations?.Pay_Commission || "Pay Commission" }}
                    </div>
                    <div class="popup-menu-item" @click="openAccountVerify">
                        {{ translations?.Verify_Account || "توثيق الحساب" }}
                    </div>
                    <div class="popup-menu-item" @click="openAccountModal">
                        {{ translations?.account_setting || "Account Setting" }}
                    </div>


                    
 
                    <hr class="popup-divider" />
 
                    <div
                        class="popup-menu-item invoice"
                        @click="openInvoiceModal"
                    >
                        {{ translations?.my_invoices || "My Invoices" }}
                    </div>
                    <!-- <div class="popup-menu-item" @click="openReportModal">
                        Report
                    </div>
                    <div class="popup-menu-item" @click="openBlockModal">
                        User Block
                    </div> -->
                </div>
            </div>
            <div class="profile-rating">
                <span> {{ translations?.Rating || "Rating" }}</span>
                <span class="stars">
                    <i
                        v-if="
                            user &&
                            user.average_rating !== null &&
                            user.average_rating !== undefined
                        "
                        v-for="i in 5"
                        :key="i"
                        class="fas fa-star"
                        :class="{ active: i <= user.average_rating }"
                    ></i>
                </span>
                <span class="rating-value" v-if="user && user.average_rating"
                    >({{ user.average_rating }})</span
                >
            </div>
            <div class="profile-follow">
                <div @click="showFollowersModal = true" style="cursor: pointer">
                    <div
                        v-if="user && user.followers"
                        class="profile-follow-count"
                    >
                        {{ user?.followers?.length }} 
                    </div>
                    <div v-else class="profile-follow-count">0</div>
                    <div class="profile-follow-label">
                        {{ translations?.Followers || "Followers" }}
                    </div>
                </div>
                <div
                    @click="showFollowingsModal = true"
                    style="cursor: pointer"
                >
                    <div
                        v-if="user && user.following"
                        class="profile-follow-count"
                    >
                        {{ user?.following?.length }}
                    </div>
                    <div v-else class="profile-follow-count">0</div>

                    <div class="profile-follow-label">
                        {{ translations?.Followings || "Followings" }}
                    </div>
                </div>
                <div class="profile-share" @click="shareApp">
                    <i class="fas fa-share-alt"></i>
                </div>
            </div>
            <div class="profile-joined" v-if="user && user.account_age">
                {{ user?.account_age }}
            </div>
        </div>
        <div class="my-ads-section">
            <h3 class="my-ads-title">
                {{ translations?.My_Ads || "My Ads " }}
            </h3>

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
                                cursor: pointer;
                            " 
                        />

                       <!--  <img
                            v-else
                            @click="goToAdDetails(ad)"
                            :src="assets/media/svg/brand-logos/logo.png"
                            alt="Product"
                            class="rounded-3 me-3"
                            style="
                                width: 70px;
                                height: 70px;
                                object-fit: cover;
                                cursor: pointer;
                            " 
                        /> -->
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


                    <div class="ad-actions">
                        <span class="ad-action" @click.stop="openUpdateModal(ad)">
                            {{ translations?.Update || "Update" }}</span
                        >
                        <span class="ad-action" @click.stop="openEditModalAd(ad)">{{
                            translations?.edit || "Edit"
                        }}</span>
                        <span class="ad-action" @click.stop="openDeleteModal(ad)">
                            {{ translations?.delete || "Delete" }}</span
                        >
                    </div>
                   
                    <!-- <div class="product-actions ms-3">
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
                        </div>

                        <div
                            v-else
                            class="invoice-link d-flex align-items-center"
                        >
                            <i class="far fa-file-alt me-1"></i>
                        </div>

                        <div
                            class="fav-btn mb-2"
                            :class="{ active: ad.is_favorite }"
                            @click="toggleFavorite(ad)"
                        >
                            <i class="far fa-heart"></i>
                        </div>
                    </div> -->
                </div>
        </div>
        <!-- Seller Rating Modal -->
        <div v-if="showRatingModal" class="modal-rating-backdrop">
            <div class="modal-rating-content">
                <div class="modal-rating-title">
                    {{ translations?.Seller || "Seller" }}
                    {{ translations?.Rating || "Rating" }}
                </div>
                <div class="modal-rating-row">
                    <span class="modal-rating-label">Rating</span>
                    <span class="modal-rating-stars">
                        <i
                            v-for="i in 5"
                            :key="i"
                            class="fas fa-star"
                            :class="{
                                active: i <= tempRating,
                                inactive: i > tempRating,
                            }"
                            @click="setTempRating(i)"
                        ></i>
                    </span>
                </div>
                <button class="modal-rating-ok" @click="applyRating">Ok</button>
            </div>
        </div>
        <!-- Account Setting Modal -->
        <div v-if="showAccountModal" class="modal-account-backdrop">
            <div class="modal-account-content" @click.stop>
                <button
                    class="modal-account-close"
                    @click="showAccountModal = false"
                >
                    &times;
                </button>
                <div class="modal-account-header">
                    <div class="avatar-upload-container" @click.stop>
                        <img
                            :src="editForm.logo || user.logo"
                            class="modal-account-avatar"
                            @click.stop
                        />
                        <div class="avatar-upload-overlay" @click.stop>
                            <label
                                for="avatar-upload"
                                class="avatar-upload-btn"
                                @click.stop
                            >
                                <i class="fas fa-camera"></i>
                            </label>
                            <input
                                id="avatar-upload"
                                type="file"
                                accept="image/*"
                                @change="handleAvatarChange"
                                @click.stop
                                style="display: none"
                            />
                        </div>
                    </div>
                </div>
                <form
                    class="modal-account-form"
                    @submit.prevent="updateProfile"
                    @click.stop
                >
                    <div class="modal-account-field">
                        <i class="fas fa-user"></i>
                        <input
                            type="text"
                            v-model="editForm.name"
                            :placeholder="translations.name || 'Name'"
                        />
                    </div>
                    <div class="modal-account-field">
                        <i class="fas fa-envelope"></i>
                        <input
                            type="email"
                            v-model="editForm.email"
                            :placeholder="translations.email || 'Email'"
                        />
                    </div>
                    <div class="modal-account-field">
                        <i class="fas fa-mobile-alt"></i>
                        <MobileFlag
                            v-if="countries.length"
                            v-model="editForm.mobile"
                            :items="countries"
                            :initialCountry="slug"
                            placeholder="5xxxxxxxxx"
                            class="col-md-12"
                        />
                        <input
                            v-else
                            type="text"
                            v-model="editForm.mobile"
                            :placeholder="translations.mobile || 'Mobile'"
                        />
                    </div>
                    <div class="modal-account-field">
                        <i class="fas fa-venus-mars"></i>
                        <select
                            v-model="editForm.gender"
                            class="form-control bg-transparent border-0"
                        >
                            <option value="male">
                                {{ translations?.mail || "Male" }}
                            </option>
                            <option value="female">
                                {{ translations?.femail || "Female" }}
                            </option>
                        </select>
                    </div>
                    <div class="modal-account-field">
                        <i class="fas fa-city"></i>
                        <select
                            @click="fetchItemsCity"
                            v-model="editForm.city_id"
                            class="form-control bg-transparent border-0"
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

                    <!-- Country Field -->
                    <!-- <div class="modal-account-field">
                        <i class="fas fa-flag"></i>
                        <select
                            v-model="editForm.country_id"
                            :disabled="!isEditing"
                            class="form-control bg-transparent border-0"
                            @change="onCountryChange"
                        >
                            <option value="" disabled>Select Country</option>
                            <option
                                :value="country.id"
                                v-for="country in countries"
                                :key="country.id"
                            >
                                {{ country.name }}
                            </option>
                        </select>
                    </div> -->

                    <!-- <div class="modal-account-field">
                        <i class="fas fa-lock"></i>
                        <input
                            :type="confirmPasswordVisible ? 'text' : 'password'"
                            v-model="editForm.oldPassword"
                            :placeholder="
                                translations.old_password || 'Old Password'
                            "
                        />
                        <i
                            :class="
                                confirmPasswordVisible
                                    ? 'fas fa-eye'
                                    : 'fas fa-eye-slash'
                            "
                            class="modal-account-eye"
                            @click="
                                togglePasswordVisibility(
                                    'confirmPasswordVisible'
                                )
                            "
                        ></i>
                    </div> -->

                    <!-- Password Fields (only when editing) -->
                    <div class="modal-account-field">
                        <i class="fas fa-lock"></i>
                        <input
                            :type="passwordVisible ? 'text' : 'password'"
                            v-model="editForm.password"
                            :placeholder="
                                translations.new_password ||
                                'New Password (optional)'
                            "
                        />
                        <i
                            :class="
                                passwordVisible
                                    ? 'fas fa-eye'
                                    : 'fas fa-eye-slash'
                            "
                            class="modal-account-eye"
                            @click="togglePasswordVisibility('passwordVisible')"
                        ></i>
                    </div>


                    <div class="modal-account-field">
                        <i class="fas fa-lock"></i>
                        <input
                            :type="confirmPasswordVisible ? 'text' : 'password'"
                            v-model="editForm.oldPassword"
                            :placeholder="
                                translations.confirm_password || 'Confirm Password'
                            "
                        />
                        <i
                            :class="
                                confirmPasswordVisible
                                    ? 'fas fa-eye'
                                    : 'fas fa-eye-slash'
                            "
                            class="modal-account-eye"
                            @click="
                                togglePasswordVisibility(
                                    'confirmPasswordVisible'
                                )
                            "
                        ></i>
                    </div>

                    <div class="modal-account-actions">
                        <div class="d-flex gap-2">
                            <button
                                type="submit"
                                class="modal-account-save"
                                :disabled="isUpdating"
                            >
                                {{
                                    isUpdating
                                        ? translations.saving || "Saving..."
                                        : translations.save || "Save"
                                }}
                            </button>
                            <button
                                type="button"
                                class="modal-account-cancel"
                                @click="closeAccountModal"
                            >
                                {{ translations?.cancle || "Cancel" }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Invoice Modal -->
        <div v-if="showInvoiceModal" class="modal-invoice-backdrop">
            <div class="modal-invoice-content">
                <button
                    class="modal-invoice-close"
                    @click="showInvoiceModal = false"
                >
                    &times;
                </button>
                <div class="modal-invoice-header">
                    <!-- <button
                        class="modal-invoice-back"
                        @click="showInvoiceModal = false"
                    >
                        <i class="fas fa-angle-left"></i> Back
                    </button> -->
                    <img
                        src="/assets/media/svg/brand-logos/logo.png"
                        class="modal-invoice-logo"
                    />
                    <div class="modal-invoice-title">
                        {{ translations?.invoice || "Invoice" }}
                    </div>
                </div>
                <hr class="modal-invoice-divider" />
                <div
                    class="modal-invoice-list"
                    v-for="(ad, index) in invoices"
                    :key="ad.id"
                    style="margin-top: 5px"
                >
                    <a
                        href="#"
                        @click="getInvoiceUrl2(ad)"
                        class="modal-invoice-item"
                        v-if="ad"
                    >
                        <i class="far fa-file-alt modal-invoice-icon"></i>
                        <span class="modal-invoice-text">{{ ad.name }} </span>
                        <i class="fas fa-paperclip modal-invoice-clip"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- Followers Modal -->
        <div v-if="showFollowersModal" class="modal-followers-backdrop">
            <div class="modal-followers-content">
                <button
                    class="modal-followers-close"
                    @click="showFollowersModal = false"
                >
                    &times;
                </button>
                <div class="modal-followers-header">
                    <img
                        src="/assets/media/svg/brand-logos/logo.png"
                        class="modal-followers-logo"
                    />
                    <div class="modal-followers-title">
                        {{ translations?.Followers || "Followers" }}
                    </div>
                </div>
                <hr class="modal-followers-divider" />
                <div class="modal-followers-search">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        v-model="followersSearch "
                        :placeholder="translations?.search "
                    />
                </div>
                <div class="modal-followers-list">
                    <div
                        v-for="f in filteredFollowers"
                        :key="f.id"
                        class="modal-followers-item"
                    >
                        <img :src="f.logo" class="modal-followers-avatar" />
                        <UserNameLink
                            :user="f"
                            link-class="modal-followers-name"
                        />
                        <button
                            class="modal-followers-menu-btn"
                            @click="openFollowerMenu(f.id, $event)"
                        >
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div
                            v-if="showFollowerMenuId === f.id"
                            class="modal-followers-popup-menu"
                            :style="followerMenuStyle"
                        >
                            <!-- <div
                                class="followers-popup-item"
                                v-if="isFollowing(f.id)"
                                @click="toggleFollow(f.id)"
                            >
                                {{ translations?.Unfollow || "Unfollow" }}
                            </div> -->
                            <div
                                class="followers-popup-item"
                                v-if="!isFollowing(f.id)"
                                @click="toggleFollow(f.id)"
                            >
                                {{ translations?.Follow_Back || "Follow Back" }}
                            </div>
                            <!-- يظهر Unblock إذا المستخدم محظور -->
                            <div
                                class="followers-popup-item"
                                v-if="isBlocked(f.id)"
                                @click="blockUser('unblock', f.id)"
                            >
                                {{ translations?.Unblock || "Unblock" }}
                            </div>

                            <!-- يظهر Block إذا المستخدم غير محظور -->
                            <div
                                class="followers-popup-item"
                                v-else
                                @click="blockUser('block', f.id)"
                            >
                                {{ translations?.Block || "Block" }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Followings Modal -->
        <div v-if="showFollowingsModal" class="modal-followers-backdrop">
            <div class="modal-followers-content">
                <button
                    class="modal-followers-close"
                    @click="showFollowingsModal = false"
                >
                    &times;
                </button>
                <div class="modal-followers-header">
                    <img
                        src="/assets/media/svg/brand-logos/logo.png"
                        class="modal-followers-logo"
                    />
                    <div class="modal-followers-title">
                        {{ translations?.Followings || "Followings" }}
                    </div>
                </div>
                <hr class="modal-followers-divider" />
                <div class="modal-followers-search">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        v-model="followingsSearch"
                        :placeholder="translations?.search "
                    />
                </div>
                <div class="modal-followers-list">
                    <div
                        v-for="f in filteredFollowings"
                        :key="f.id"
                        class="modal-followers-item"
                    >
                        <img :src="f.logo" class="modal-followers-avatar" />
                        <UserNameLink
                            :user="f"
                            link-class="modal-followers-name"
                        />
                        <button
                            class="modal-followers-menu-btn"
                            @click="openFollowingMenu(f.id, $event)"
                        >
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div
                            v-if="showFollowingMenuId === f.id"
                            class="modal-followers-popup-menu"
                            :style="followingMenuStyle"
                        >
                            <div
                                class="followers-popup-item"
                                v-if="isFollowing(f.id)"
                                @click="toggleFollow(f.id)"
                            >
                                {{ translations?.Unfollow || "Unfollow" }}
                            </div>
                            <div
                                class="followers-popup-item"
                                v-else
                                @click="toggleFollow(f.id)"
                            >
                                {{ translations?.Follow_Back || "Follow Back" }}
                            </div>
                            <!-- يظهر Unblock إذا المستخدم محظور -->
                            <div
                                class="followers-popup-item"
                                v-if="isBlocked(f.id)"
                                @click="blockUser('unblock', f.id)"
                            >
                                {{ translations?.Unblock || "Unblock" }}
                            </div>

                            <!-- يظهر Block إذا المستخدم غير محظور -->
                            <div
                                class="followers-popup-item"
                                v-else
                                @click="blockUser('block', f.id)"
                            >
                                {{ translations?.Block || "Block" }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Report Modal -->
        <div v-if="showReportModal" class="modal-report-backdrop">
            <div class="modal-report-content">
                <div class="modal-report-title">
                    {{
                        translations?.Why_are_you_Reporting ||
                        "Why are you Reporting?"
                    }}
                </div>
                <textarea
                    class="modal-report-textarea"
                    v-model="reportReason"
                    placeholder="Write your reason here.."
                ></textarea>
                <button class="modal-report-submit" @click="submitReport">
                    {{ translations?.Submit_Now || " Submit Now" }}
                </button>
            </div>
        </div>
        <!-- Block Modal -->
        <div v-if="showBlockModal" class="modal-block-backdrop">
            <div class="modal-block-content">
                <div class="modal-block-title">
                    {{ translations?.Block_the_user || "Block the user" }}
                </div>
                <div class="modal-block-desc">
                    {{
                        translations?.Do_you_want_to_block_the_user ||
                        "Do you want to block the user?"
                    }}
                </div>
                <button class="modal-block-btn" @click="submitBlock">
                    {{ translations?.Block || "Block" }}
                </button>
            </div>
        </div>
        <!-- Delete Modal -->
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
        <!-- Update Modal -->
        <div v-if="showUpdateModal" class="modal-update-backdrop">
            <div class="modal-update-content">
                <div class="modal-update-icon-wrap">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="32" fill="#fff" />
                        <path
                            d="M20 34L29 43L44 25"
                            stroke="#e5aa2a"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <circle
                            cx="32"
                            cy="32"
                            r="28"
                            stroke="#e5aa2a"
                            stroke-width="4"
                        />
                    </svg>
                </div>
                <div class="modal-update-text">
                    {{ titleBlack }}<br />{{ bodyBlack }}
                </div>
                <button
                    class="modal-update-ok"
                    @click="showUpdateModal = false"
                >
                    {{ translations?.Ok || "Ok" }}
                </button>
            </div>
        </div>
        <!-- Add Success Modal -->
        <div v-if="showAddSuccess" class="modal-update-backdrop">
            <div class="modal-update-content">
                <div class="modal-update-icon-wrap">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="32" fill="#fff" />
                        <path
                            d="M20 34L29 43L44 25"
                            stroke="#e5aa2a"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <circle
                            cx="32"
                            cy="32"
                            r="28"
                            stroke="#e5aa2a"
                            stroke-width="4"
                        />
                    </svg>
                </div>
                <div class="modal-update-text">
                    {{
                        translations?.Congratulations_1 ||
                        "Congratulations, your ad has been added successfully."
                    }}
                </div>
                <button class="modal-update-ok" @click="showAddSuccess = false">
                    {{ translations?.Ok || "Ok" }}
                </button>
            </div>
        </div>
        <!-- Account Setting Modal -->
        <div
            v-if="showEditModal"
            class="modal-account-backdrop"
            style="overflow: scroll">
            <div
                class="modal-account-content"
                @click.stop
                style="margin-top: 400px">
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
                            {{ translations?.update_ad || "Update Ad" }}  
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
                       <!--  <div class="ad-form-row">
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
                        </div> -->
                           
                        <div class="ad-form-row">
                            <!-- <input
                                class="ad-form-input"
                                placeholder="+966  (Optional)"
                                v-model="formAd.mobile"
                            /> -->

                            <div class="modal-account-field col-md-6">
                                <!-- <i class="fas fa-mobile-alt"></i> -->
                                <MobileFlag
                                    v-if="countries.length"
                                    v-model="formAd.mobile"
                                    :items="countries"
                                    initialCountry="sa"
                                    placeholder="5xxxxxxxxx"
                                    class="col-md-12"
                                />
                                <input
                                    v-else
                                    type="text"
                                    v-model="formAd.mobile"
                                    :placeholder="translations.mobile || 'Mobile'"
                                />
                            </div>


                            <input
                                class="ad-form-input"
                                placeholder="SAR (Optional)"
                                v-model="formAd.price"
                            />
                        </div>

                       


                        <div class="ad-form-row ad-form-invoice-row">

                            <div class="modal-account-field col-md-6">
                                <i class="fas fa-city"></i>
                                <select
                                    @click="fetchItemsCityAd"
                                    v-model="formAd.city_id"
                                    class="form-control bg-transparent border-0"
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
                        />
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
                            {{ translations?.save || "Save" }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <YakeenVerifyModal v-model="showYakeenModal" />
</template>

<script>
import sliderBg1 from "@/assets/img-add.png";
import MobileFlag from "@/components/Website/auth/MobileFlag.vue";
import YakeenVerifyModal from "@/components/Website/common/YakeenVerifyModal.vue";
import axios from "axios";
import { nextTick } from "vue";
import { authApi } from "@/axiosUser"; // غيّر المسار إذا ملفك بمكان مختلف
import { navigateToUserProfile } from "@/utils/userProfile";
import { fetchAvailableGenders } from "@/utils/categoryGenders";
import { isIdentityVerified } from "@/utils/userVerification";

export default {
    name: "MyPage",
    components: {
        MobileFlag,
        YakeenVerifyModal,
    },
    data() {
        return {
            editForm: {},
            isEditing: false,
            isUpdating: false,
            passwordVisible: false,
            confirmPasswordVisible: false,
            ads: [],
            showProfileMenu: false,
            showRatingModal: false,
            showEditModal: false,
            tempRating: 4,
            showAccountModal: false,
            showInvoiceModal: false,
            showYakeenModal: false,
            showFollowersModal: false,
            showFollowingsModal: false,
            followersSearch: "",
            followingsSearch: "",
            followers: [],
            followings: [],
            showFollowerMenuId: null,
            showFollowingMenuId: null,
            followerMenuStyle: {},
            followingMenuStyle: {},
            showReportModal: false,
            showBlockModal: false,
            reportReason: "",
            showDeleteModal: false,
            adToDelete: null,
            showUpdateModal: false,
            showAddSuccess: false,
            invoices: [],

            countries: [],
            cities: [],
            categories: [],
            brands: [],
            genders: [],
            subcategories: [],
            hasGenderFlow: false,
            logo: "",
            images: [],
            titleBlack: "",
            bodyBlack: "",
            invoiceFile:"",
            // titleDeleteAd: translations?.titleDeleteAd || 'سبب الحذف',
            // bodyDeleteAd: translations?.bodyDeleteAd || 'هل تريد حذف المنتج بسبب البيع؟',
            formAd: {
                invoiceFile: "",
                country_id: "",
                city_id: "",
                category_id: "",
                brand_id: "",
                gender_id: "",
                subcategory_id: "",
                product_type: "",
            },


            isLoggedIn: !!localStorage.getItem("token"),
            slug:"sa",

        };
    },
    computed: {
        filteredFollowers() {
            if (!this.followersSearch && this.user) return this.user.followers;
            return this.user.followers.filter((f) =>
                f.name
                    .toLowerCase()
                    .includes(this.followersSearch.toLowerCase())
            );
        },

        filteredFollowings() {
            if (!this.followingsSearch) return this.user.following;
            return this.user.following.filter((f) =>
                f.name
                    .toLowerCase()
                    .includes(this.followingsSearch.toLowerCase())
            );
        },

        locale() {
            return this.$route.params.locale || "en";
        },

        user() {
            return this.$store.getters.getUser;
        },

        translations() {
            return this.$store.getters.getTranslations || {};
        },

        titleDeleteAd() {
            return this.translations.titleDeleteAd || "Reason";
        },
        bodyDeleteAd() {
            return (
                this.translations.bodyDeleteAd ||
                "Do you want to delete the product due to sale?"
            );
        },

    },
    mounted() {
        this.$store.dispatch("fetchSetting");
        this.$store.dispatch("fetchUser");

        this.fetchItemsCountry();
        this.fetchItemsAds();

        document.addEventListener("mousedown", this.handleClickOutside);
        let localAds = [];
        try {
            localAds = JSON.parse(localStorage.getItem("my_ads") || "[]");
        } catch (e) {}
        if (localAds.length) {
            this.ads = localAds.concat(this.ads);
        }
        // تحقق من باراميتر added
        if (this.$route.query.added) {
            this.showAddSuccess = true;
        }

        this.filteredFollowers = this.user?.followers;
        this.filteredFollowings = this.user?.following;
    },

    beforeUnmount() {
        document.removeEventListener("mousedown", this.handleClickOutside);
    },
    methods: {
        onImageChange(e) {
            const files = Array.from(e.target.files);
            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.images.push({ file, url: ev.target.result });
                };
                reader.readAsDataURL(file);
            });
        },

        async removeImage(id, idx) {
            this.isLoading = true;
            await axios
                .get("ads/deleteImg", {
                    params: {
                        img_id: id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        this.images.splice(idx, 1);
                    } else {
                        this.swalFunction("error", response.data.message);
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },
        onInvoiceChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.invoiceFile = file;
            }
        },

        async updateProfile() {
            if (
                this.editForm.password &&
                this.editForm.password !== this.editForm.oldPassword
            ) {
                this.showAccountModal = false;
                this.showProfileMenu = true;

                let mess = "Passwords do not match";
                if(this.locale == 'ar'){
                    let mess = 'كلمة المرو غير متطابقة';
                }
                this.swalFunction("error", mess );

                return;
            }

            this.isUpdating = true;
            try {
                let formData = this.editForm;

                formData = new FormData();
                formData.append("mobile2", this.editForm.code + this.editForm.mobile);

                Object.keys(this.editForm).forEach((key) => {
                    if (key === "logo") {
                        formData.append("logo", this.logo);
                    } else {
                        formData.append(key, this.editForm[key]);
                    }

                  
                    
                });

                const response = await axios.post("/auth/editInfo", formData, {
                    headers: {
                        "Content-Type":
                            formData instanceof FormData
                                ? "multipart/form-data"
                                : "application/json",
                    },
                });

                if (response.data.code === 200) {
                    this.swalFunction(
                        "success",response.data.message);
                    this.user = response.data.items;

                    this.$store.commit('setUser', response.data.items);

                  

                    this.showAccountModal = false;
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

        togglePasswordVisibility(type) {
            if (type === "passwordVisible") {
                this.passwordVisible = !this.passwordVisible;
            } else if (type === "confirmPasswordVisible") {
                this.confirmPasswordVisible = !this.confirmPasswordVisible;
            }
        },

        async fetchItemsCountry() {
            this.isLoading = true;
            await axios
                .get("basic/getCountries", {
                    params: {
                        country_id: this.editForm.country_id,
                    },
                })
                .then((response) => {
                    if (this.editForm.country_id) {
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

       async fetchItemsCity() {
        console.log('city')
        console.log(this.user)
            let intro = $(".iti__selected-dial-code").html();
            let code = intro.replace("+", "");
            let matchedCountry = null;

            for (const country of this.countries) {
                if (country.code == code) {
                    matchedCountry = country;
                    break;
                }
            }


            if (matchedCountry) {
                this.editForm.country_id = matchedCountry.id;

                const inputElement = document.querySelector('input[type="tel"]');
                
                if (inputElement && this.editForm.mobile) {
                    const countryCode = matchedCountry.code.toString();
                    
                    // تنظيف الرقم من المقدمة
                    let cleanedMobile = this.editForm.mobile.toString();
                    
                    // إزالة المقدمة إذا كانت موجودة
                    if (cleanedMobile.startsWith("+" + countryCode)) {
                        cleanedMobile = cleanedMobile.substring(("+" + countryCode).length);
                    } else if (cleanedMobile.startsWith(countryCode)) {
                        cleanedMobile = cleanedMobile.substring(countryCode.length);
                    } else if (cleanedMobile.startsWith("+")) {
                        // إزالة أي + في البداية
                        cleanedMobile = cleanedMobile.substring(1);
                        // محاولة إزالة كود الدولة مرة أخرى
                        if (cleanedMobile.startsWith(countryCode)) {
                            cleanedMobile = cleanedMobile.substring(countryCode.length);
                        }
                    }
                    
                    // إزالة الأصفار من البداية والمسافات
                    cleanedMobile = cleanedMobile.replace(/^0+/, "").replace(/[\s\-\(\)]/g, "").trim();
                    
                    // ✅ عرض الرقم النظيف بدون المقدمة
                    inputElement.value = cleanedMobile;
                    inputElement.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    // تحديث editForm
                    this.editForm.mobile = cleanedMobile;
                    this.editForm.code = code;
                }
            }

            this.isLoading = true;
            await axios.get("basic/getCountries", {
                params: { country_id: matchedCountry ? matchedCountry.id : null }
            })
            .then((response) => {
                this.cities = response.data.items;
                this.isLoading = false;
            })
            .catch((error) => {
                this.swalFunction("error", error);
                this.isLoading = false;
            });
        },




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


            let intro = $(".iti__selected-dial-code").html();
            let code = intro.replace("+", "");
            let matchedCountry = null;
           
            for (const country of this.countries) {
                if (country.code == code) {
                    matchedCountry = country;
                    break;
                }
            }


            if (matchedCountry) {
                const code = matchedCountry.code.toString();
                const raw = this.formAd.mobile
                    .replace("+" + code, "")
                    .replace(code, "");
                const cleaned = raw.replace(/^0+/, ""); // يشيل صفر البداية إذا موجود
                document.querySelector('input[type="tel"]').value = cleaned;
            }

            this.isLoading = true;
            await axios
                .get("basic/getCountries", {
                    params: {
                        country_id: matchedCountry.id,
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

        onCategoryChangeEdit() {
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
            this.fetchGendersForEdit();
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

        async fetchItemsAds() {
            this.isLoading = true;
            await authApi({
                method: "get",
                url: "ads/getAllItemsMyAds",
                params: {
                    pagination: 0,
                },
            })
                .then((response) => {
                    this.ads = response.data.items;
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        shareApp() {
            if (navigator.share) {
                navigator
                    .share({
                        title: "Check out this app!",
                        text: "I found this awesome app, check it out!",
                        url: window.location.origin,
                    })
                    .then(() => console.log("App shared successfully"))
                    .catch((error) => console.error("Error sharing", error));
            } else {
                alert("Sharing is not supported on this device/browser.");
            }
        },

        openEditModalAd(ad) {
            this.showEditModal = true;
            this.showProfileMenu = false;

            this.formAd = { ...ad };
            this.images = this.formAd.images || [];

            this.fetchItemsCountryAd();
            this.fetchItemsCategories();
        },

        async removeInvoice() {
            // this.formAd.invoiceFile = null;
            // this.$refs.invoiceInput.value = null;

            this.isLoading = true;
            await axios
                .get("ads/deleteInvoice", {
                    params: {
                        ad_id: this.formAd.id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        this.formAd.invoiceFile = null;
                        this.$refs.invoiceInput.value = null;
                        this.formAd = response.data.items;
                    } else {
                        this.swalFunction("error", response.data.message);
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },

        getInvoiceUrl(file) {
            if (file instanceof File) {
                return URL.createObjectURL(file);
            }

            return file;
        },

        // getInvoiceUrl2(invoice) {
        //     this.$router.push({ path: `/${this.locale}/invoice/${invoice.track_id}` });
        // },

        getInvoiceUrl2(invoice) {
            const url = `/${this.locale}/invoice/${invoice.track_id}`;
            window.open(url, "_blank"); // يفتح في تاب جديدة
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
                formData.append("product_type", this.formAd.product_type || "");
                formData.append("mobile", this.formAd.mobile ?? "");
                formData.append("price", this.formAd.price ?? "");
                formData.append("gender_id", this.formAd.gender_id || "");
                formData.append("subcategory_id", this.formAd.subcategory_id || "");

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

        toggleProfileMenu() {
            this.showProfileMenu = !this.showProfileMenu;
        },
        handleClickOutside(event) {
            if (
                this.showProfileMenu &&
                this.$refs.profileMenu &&
                !this.$refs.profileMenu.contains(event.target)
            ) {
                this.showProfileMenu = false;
            }
            if (this.showAccountModal) {
                const modal = document.querySelector(".modal-account-content");
                if (modal && !modal.contains(event.target)) {
                    // لا نغلق البوب أب إذا كان في وضع التعديل
                    if (!this.isEditing) {
                        this.showAccountModal = false;
                    }
                }
            }
            if (this.showInvoiceModal) {
                const modal = document.querySelector(".modal-invoice-content");
                if (modal && !modal.contains(event.target)) {
                    this.showInvoiceModal = false;
                }
            }
            if (this.showFollowersModal) {
                const modal = document.querySelector(
                    ".modal-followers-content"
                );
                if (modal && !modal.contains(event.target)) {
                    this.showFollowersModal = false;
                }
            }
            if (this.showFollowingsModal) {
                const modal = document.querySelector(
                    ".modal-followers-content"
                );
                if (modal && !modal.contains(event.target)) {
                    this.showFollowingsModal = false;
                }
            }
            if (this.showFollowerMenuId !== null) {
                const popup = document.querySelector(
                    ".modal-followers-popup-menu"
                );
                if (popup && !popup.contains(event.target)) {
                    this.showFollowerMenuId = null;
                }
            }
            if (this.showFollowingMenuId !== null) {
                const popup = document.querySelector(
                    ".modal-followers-popup-menu"
                );
                if (popup && !popup.contains(event.target)) {
                    this.showFollowingMenuId = null;
                }
            }
            if (this.showReportModal) {
                const modal = document.querySelector(".modal-report-content");
                if (modal && !modal.contains(event.target)) {
                    this.showReportModal = false;
                }
            }
            if (this.showBlockModal) {
                const modal = document.querySelector(".modal-block-content");
                if (modal && !modal.contains(event.target)) {
                    this.showBlockModal = false;
                }
            }
            if (this.showDeleteModal) {
                const modal = document.querySelector(".modal-delete-content");
                if (modal && !modal.contains(event.target)) {
                    this.showDeleteModal = false;
                }
            }
            if (this.showUpdateModal) {
                const modal = document.querySelector(".modal-update-content");
                if (modal && !modal.contains(event.target)) {
                    this.showUpdateModal = false;
                }
            }
        },
        openRatingModal() {
            this.tempRating = this.user.rating;
            this.showRatingModal = true;
            this.showProfileMenu = false;
        },
        setTempRating(val) {
            this.tempRating = val;
        },

        openAccountModal() {
            this.showAccountModal = true;
            this.showProfileMenu = false;
            this.editForm = this.user;
            this.slug = this.user.country.slug;
            console.log(this.editForm)

            nextTick(() => {
                this.fetchItemsCity();
            });
        },

        closeAccountModal() {
            this.showAccountModal = false;
        },

        async openInvoiceModal() {
            this.isLoading = true;
            await axios
                .get("order/invoices", {
                    params: {},
                })
                .then((response) => {
                    this.showInvoiceModal = true;
                    this.showProfileMenu = false;
                    this.invoices = response.data.items;
                    console.log(this.invoices);
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });
        },
        openFollowerMenu(id, event) {
            this.showFollowerMenuId = id;
            // Position the popup menu next to the button
            const rect = event.currentTarget.getBoundingClientRect();
            this.followerMenuStyle = {
                position: "fixed",
                top: rect.top + rect.height + 4 + "px",
                left: rect.left + "px",
                zIndex: 9999,
            };
        },
        openFollowingMenu(id, event) {
            this.showFollowingMenuId = id;
            // Position the popup menu next to the button
            const rect = event.currentTarget.getBoundingClientRect();
            this.followingMenuStyle = {
                position: "fixed",
                top: rect.top + rect.height + 4 + "px",
                left: rect.left + "px",
                zIndex: 9999,
            };
        },
        openReportModal() {
            this.showReportModal = true;
            this.showFollowerMenuId = null;
            this.showFollowingMenuId = null;
        },
        openBlockModal() {
            this.showBlockModal = true;
            this.showFollowerMenuId = null;
            this.showFollowingMenuId = null;
        },
        submitReport() {
            // يمكنك هنا إرسال السبب للسيرفر
            this.showReportModal = false;
            this.reportReason = "";
        },
        submitBlock() {
            // يمكنك هنا تنفيذ الحظر
            this.showBlockModal = false;
        },

        openDeleteModal(ad) {
            this.formAd = ad;
            this.showDeleteModal = true;
        },

        // confirmDelete() {
        //     if (this.adToDelete) {
        //         this.ads = this.ads.filter((a) => a.id !== this.adToDelete.id);
        //     }
        //     this.showDeleteModal = false;
        //     this.adToDelete = null;
        // },

        async openUpdateModal(ad) { 
            // this.showUpdateModal = true;

            this.isLoading = true;
            await axios
                .post("ads/refresh", ad)
                .then((response) => {
                    if (response.data.code == 200) {
                        this.fetchItemsAds()

                        const messages = {
        ar: {
            title: "تم التحديث",
            body: "تم تحديث البيانات بنجاح",
            ok: "حسناً",
        },
        en: {
            title: "Updated Successfully",
            body: "Data has been updated successfully",
            ok: "Ok",
        },
    };

    // اختيار اللغة الحالية
    const lang = this.locale === "en" ? "en" : "ar";

    // تعبئة النصوص
    this.titleBlack = messages[lang].title;
    this.bodyBlack = messages[lang].body;
    this.okText = messages[lang].ok;

                        this.showUpdateModal = true;
                    } else {
                        this.swalFunction("error", response.data.message);
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

        async NoDeleteModalAd() {
            this.isLoading = true;
            await axios
                .get("ads/deleteAd", {
                    params: {
                        ad_id: this.formAd.id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        this.fetchItemsAds();
                        this.titleBlack = response.data.message;
                        this.bodyBlack = "successfully";
                        this.showUpdateModal = true;
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
                        ad_id: this.formAd.id,
                    },
                })
                .then((response) => {
                    if (response.data.code == 200) {
                        // this.fetchItemsAds()
                        // this.titleBlack = response.data.message;
                        // this.bodyBlack = 'successfully';
                        // this.showUpdateModal = true;
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
            localStorage.setItem("adPay", JSON.stringify(this.formAd));
            this.$router.push({ path: `/${this.locale}/pay-commission` });
            this.showProfileMenu = false;
        },

        openAccountVerify() {
            this.showProfileMenu = false;
            if (
                isIdentityVerified(this.user) ||
                this.$store.getters.isNafathVerified
            ) {
                Swal.fire({
                    icon: "success",
                    text:
                        this.translations?.Already_Verified ||
                        "حسابك موثّق مسبقاً",
                    confirmButtonText: "Ok",
                });
                return;
            }
            this.showYakeenModal = true;
        },

        goToAdDetails(ad) {
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

        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    // 5MB limit
                    this.swalFunction(
                        "error",
                        "File size should be less than 5MB"
                    );
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.editForm.logo = e.target.result;
                    this.logo = file;
                };
                reader.readAsDataURL(file);
            }
        },

        onCountryChange() {
            if (this.editForm.country_id) {
                this.fetchCities();
            } else {
                this.cities = [];
                this.editForm.city_id = null;
            }
        },

        isFollowing(userId) {
            return this.user.following?.some((u) => u.id === userId);
        },


        goToUserPage(user) {
            navigateToUserProfile(this.$router, this.locale, this.user, user);
        },



        async toggleFollow(user_id) {
            // e.preventDefault();
            try {
                const formData = new FormData();

                formData.append("user_id", user_id);

                const response = await axios.post(
                    "/ads/toggleFollow",
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
                    this.user = response.data.items;
                    this.$store.commit("setUser", response.data.items);
                    this.showFollowersModal = false;
                    this.showFollowingsModal = false;

                    // this.$router.push({ path: `/${this.locale}` });

                    this.isEditing = false;
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

        isBlocked(userId) {
            if (!userId) {
                return false;
            }
            const currentUser = this.$store.getters.getUser ?? this.user;
            return currentUser?.blocked_users?.some((u) => u.id === userId) ?? false;
        },

        async blockUser(type, user_id) {
            const isEnglish = this.locale === "en";

            const messages = {
                block: {
                    title: isEnglish
                        ? "Are you sure you want to block this user?"
                        : "هل أنت متأكد أنك تريد حظر هذا المستخدم؟",
                    confirm: isEnglish ? "Yes, block" : "نعم، احظر",
                    deny: isEnglish ? "Cancel" : "إلغاء",
                    success: isEnglish
                        ? "Blocked successfully"
                        : "تم الحظر بنجاح",
                },
                unblock: {
                    title: isEnglish
                        ? "Are you sure you want to unblock this user?"
                        : "هل أنت متأكد أنك تريد إلغاء الحظر؟",
                    confirm: isEnglish ? "Yes, unblock" : "نعم، إلغاء الحظر",
                    deny: isEnglish ? "Cancel" : "إلغاء",
                    success: isEnglish
                        ? "Unblocked successfully"
                        : "تم الغاء الحظر بنجاح",
                },
            };

            const message = messages[type] || messages.block;

            const result = await swal.fire({
                text: message.title,
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: message.confirm,
                denyButtonText: message.deny,
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-primary",
                    denyButton: "btn btn-light-danger",
                },
            });

            if (!result.isConfirmed) {
                // ألغي الحظر أو لم يتم التأكيد
                return;
            }

            // تابع العملية إذا تم التأكيد
            this.isUpdating = true;

            try {
                const formData = new FormData();
                formData.append("blocked_user_id", user_id);

                const response = await axios.post("/ads/blockUser", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                if (response.data.code === 200) {
                    this.user = response.data.items;
                    this.$store.commit("setUser", response.data.items);
                    this.showFollowersModal = false;
                    this.showFollowingsModal = false;
                    this.isEditing = false;

                    this.swalFunction("success", message.success);
                } else {
                    this.swalFunction("error", response.data.message);
                }
            } catch (error) {
                this.swalFunction(
                    "error",
                    error.response?.data?.message ||
                        (isEnglish
                            ? "Error updating profile"
                            : "حدث خطأ أثناء التحديث")
                );
            } finally {
                this.isUpdating = false;
            }
        },
    },
};
</script>

<style scoped>
.my-page-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 32px 0 64px 0;
}
.profile-card {
    background: #f5f5f5;
    border-radius: 8px;
    padding: 32px 0 16px 0;
    text-align: center;
    position: relative;
    margin-bottom: 32px;
}
.profile-avatar-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 12px;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e5aa2a;
    margin-bottom: 0;
    display: block;
}
.profile-online-dot {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 14px;
    height: 14px;
    background: #22c55e;
    border: 2px solid #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
}
.profile-last-seen {
    font-size: 0.9rem;
    color: #9ca3af;
    margin: 0 0 4px;
    text-align: center;
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
.user-profile-link {
    cursor: pointer;
}
.user-profile-link:hover {
    color: #e5aa2a;
    text-decoration: underline;
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
