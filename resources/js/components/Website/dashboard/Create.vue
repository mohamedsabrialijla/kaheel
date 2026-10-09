<template>
    <div class="container">
        <div class="row w-100 gy-10">
            <!--begin::Col-->

            <div class="col-md-12 alignCenter px-5">
                <div class="position-relative text-right mt-5 mb-12">
                    <!--begin::Title-->
                    <h3
                        class="fs-2rem text-gray-900 mb-1 lineHight50"
                        id="how-it-works"
                        data-kt-scroll-offset="{default: 100, lg: 150}"
                    >
                        {{ t.Start_now_creating }}
                        <div class="vectorSpan2 moveColor">
                            {{ t.free_templates }}
                            <img
                                src="/assets/media/vector2.png"
                                class="vectorImg2"
                                alt="vectorImg2"
                            />
                        </div>
                    </h3>
                    <p class="Paragr grayColor mt-0">
                        {{ t.youChange }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row w-100 gy-10 mb-md-20">
            <div class="col-md-12 mt-0 mb-5 TabTemplates CreateCvTab">
                <div class="row">
                    <div class="col-md-4">
                        <ul
                            class="nav nav-tabs row"
                            id="myTab"
                            role="tablist"
                            v-if="!editTemplate"
                        >
                            <li
                                class="nav-item col-6"
                                role="presentation"
                                v-for="(temp, index) in allTemplates"
                                :key="id"
                            >
                                <button
                                    @click="getItemById(temp.id)"
                                    class="nav-link"
                                    type="button"
                                >
                                    <!-- active -->
                                    <img
                                        :src="temp.logo"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                </button>
                            </li>
                        </ul>

                        <div class="nav nav-tabs p-0 row" v-else>
                            <!--begin::Accordion-->
                            <div class="accordion p-0" id="kt_accordion_1">
                                <div class="AddSection">
                                    <button
                                        @click="AtsText"
                                        class="btn btn-light-warning w-100"
                                        :disabled="isLoading"
                                    >
                                        <i
                                            class="ki-duotone ki-abstract-25 text-warning fs-3x me-2"
                                        >
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span
                                            v-if="isLoading"
                                            class="spinner-border spinner-border-sm text-warning me-2"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                        {{ t.check_ats_ai }}
                                    </button>
                                </div>

                                <draggable
                                    v-model="selectedSections"
                                    item-key="section"
                                    animation="200"
                                    @end="
                                        onSectionsOrderChanged(selectedSections)
                                    "
                                >
                                    <template
                                        #item="{ element: section, index }"
                                    >
                                        <div
                                            class="accordion-item mb-2 border rounded"
                                        >
                                            <!-- العنوان -->
                                            <h2
                                                class="accordion-header"
                                                @click="toggleAccordion(index)"
                                            >
                                                <button
                                                    class="accordion-button w-100 text-start"
                                                    :class="{
                                                        collapsed:
                                                            openAccordion !==
                                                            index,
                                                    }"
                                                >
                                                    {{
                                                        sectionTitles[
                                                            section.key
                                                        ]
                                                    }}
                                                </button>
                                            </h2>

                                            <!-- الجسم -->
                                            <div
                                                v-show="openAccordion === index"
                                                class="accordion-collapse transition-all duration-300"
                                            >
                                                <div class="accordion-body">
                                                    <div
                                                        v-if="
                                                            section.key ===
                                                            'personalInfo'
                                                        "
                                                    >
                                                        <div class="card-body">
                                                            <div class="mb-0">
                                                                <p
                                                                    class="text-muted mb-5"
                                                                    style="
                                                                        line-height: 20px;
                                                                    "
                                                                >
                                                                    {{
                                                                        t.add_contact_tip
                                                                    }}
                                                                </p>
                                                            </div>

                                                            <div class="row">
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.first_name
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="firstName"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.first_name
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.last_name
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="lastName"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.last_name
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.job_title
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="position"
                                                                        class="form-control"
                                                                        placeholder="The role you want"
                                                                        v-model="
                                                                            personalInfo.job_title
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.upload_photo
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        @change="
                                                                            onFileChange
                                                                        "
                                                                        type="file"
                                                                        accept=".jpg,.jpeg,.png,.gif,.tiff"
                                                                        class="form-control"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.email
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="email"
                                                                        name="email"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.email
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.phone
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="phone"
                                                                        name="phoneNumber"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.mobile
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.address
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="address"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.address
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.state
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="city"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.city
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.country
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="countryName"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.country
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.postal_code
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="postalCode"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.postal_code
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.driving_license
                                                                        }}
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        name="drivingLicense"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.driving_license
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.plac
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        name="birthPlace"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.place_of_birth
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-12 mb-2"
                                                                >
                                                                    <label
                                                                        class="form-label mb-0"
                                                                        >{{
                                                                            t.birth
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="date"
                                                                        name="dateOfBirth"
                                                                        class="form-control"
                                                                        v-model="
                                                                            personalInfo.date_of_birth
                                                                        "
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'summary'
                                                        "
                                                    >
                                                        <div class="card-body">
                                                            <div class="mb-0">
                                                                <p
                                                                    class="text-muted mb-5"
                                                                >
                                                                    {{
                                                                        t.below
                                                                    }}
                                                                </p>
                                                            </div>

                                                            <div class="mb-3">
                                                                <div
                                                                    style="
                                                                        gap: 66%;
                                                                        height: 34px;
                                                                        display: flex;
                                                                    "
                                                                >
                                                                    <label
                                                                        class="form-label"
                                                                        >{{
                                                                            t.description
                                                                        }}</label
                                                                    >

                                                                    <!-- الزر مع Dropdown -->
                                                                    <div
                                                                        class="dropdown"
                                                                    >
                                                                        <!-- الزر الأساسي -->
                                                                        <div
                                                                            class="d-flex flex-center w-30px h-30px rounded-3 bg-light-success bg-opacity-90 mb-10"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false"
                                                                            style="
                                                                                cursor: pointer;
                                                                            "
                                                                        >
                                                                            <span
                                                                                v-if="
                                                                                    isLoadingSummary
                                                                                "
                                                                                class="spinner-border spinner-border-sm text-warning me-2"
                                                                                role="status"
                                                                                aria-hidden="true"
                                                                            ></span>

                                                                            <i
                                                                                v-else
                                                                                class="ki-duotone ki-square-brackets text-success fs-2x"
                                                                            >
                                                                                <span
                                                                                    class="path1"
                                                                                ></span>
                                                                                <span
                                                                                    class="path2"
                                                                                ></span>
                                                                                <span
                                                                                    class="path3"
                                                                                ></span>
                                                                                <span
                                                                                    class="path4"
                                                                                ></span>
                                                                            </i>
                                                                        </div>

                                                                        <!-- القائمة المنسدلة -->
                                                                        <ul
                                                                            class="dropdown-menu select2-dropdown w-150px select2-dropdown--below show"
                                                                            data-bs-auto-close="outside"
                                                                        >
                                                                            <li>
                                                                                <span
                                                                                    class="dropdown-item"
                                                                                    @mousedown.prevent.stop="
                                                                                        Suggestion(
                                                                                            'summary'
                                                                                        )
                                                                                    "
                                                                                    >{{
                                                                                        t.Suggestion
                                                                                    }}</span
                                                                                >
                                                                            </li>
                                                                            <li>
                                                                                <span
                                                                                    class="dropdown-item"
                                                                                    @mousedown.prevent.stop="
                                                                                        checkText(
                                                                                            'summary'
                                                                                        )
                                                                                    "
                                                                                    >{{
                                                                                        t.Audit
                                                                                    }}</span
                                                                                >
                                                                            </li>
                                                                            <li>
                                                                                <span
                                                                                    class="dropdown-item"
                                                                                    @mousedown.prevent.stop="
                                                                                        rephraseText(
                                                                                            'summary'
                                                                                        )
                                                                                    "
                                                                                    >{{
                                                                                        t.Reformulation
                                                                                    }}</span
                                                                                >
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <textarea
                                                                    class="form-control form-control-solid"
                                                                    name="description[]"
                                                                    rows="3"
                                                                    v-model="
                                                                        personalInfo.summary
                                                                    "
                                                                    placeholder="Brief description..."
                                                                ></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'employmentHistory'
                                                        "
                                                    >
                                                        <div class="card-body">
                                                            <div class="mb-0">
                                                                <p
                                                                    class="text-muted mb-5"
                                                                >
                                                                    {{
                                                                        t.below2
                                                                    }}
                                                                </p>
                                                            </div>

                                                            <div class="row">
                                                                <div
                                                                    v-for="(
                                                                        employment,
                                                                        index
                                                                    ) in personalInfo.employmentHistory"
                                                                    :key="index"
                                                                    class="accordion mb-3"
                                                                >
                                                                    <div
                                                                        class="accordion-item"
                                                                    >
                                                                        <h2
                                                                            class="accordion-header"
                                                                        >
                                                                            <button
                                                                                class="accordion-button"
                                                                                type="button"
                                                                                @click="
                                                                                    toggle(
                                                                                        index
                                                                                    )
                                                                                "
                                                                            >
                                                                                {{
                                                                                    (employment.company_name ||
                                                                                        "Employment " +
                                                                                            (index +
                                                                                                1)) +
                                                                                    (employment.job_title
                                                                                        ? " - " +
                                                                                          employment.job_title
                                                                                        : "")
                                                                                }}
                                                                            </button>
                                                                        </h2>
                                                                        <div
                                                                            class="accordion-collapse"
                                                                            v-show="
                                                                                activeIndex ===
                                                                                index
                                                                            "
                                                                        >
                                                                            <div
                                                                                class="accordion-body"
                                                                            >
                                                                                <p
                                                                                    class="text-muted mb-4"
                                                                                >
                                                                                    {{
                                                                                        t.below3
                                                                                    }}
                                                                                </p>
                                                                                <div
                                                                                    class="row"
                                                                                >
                                                                                    <div
                                                                                        class="col-md-6 mb-2"
                                                                                    >
                                                                                        <label
                                                                                            class="form-label mb-0"
                                                                                        >
                                                                                            {{
                                                                                                t.company_name
                                                                                            }}</label
                                                                                        >
                                                                                        <input
                                                                                            type="text"
                                                                                            v-model="
                                                                                                employment.company_name
                                                                                            "
                                                                                            class="form-control"
                                                                                            placeholder="Company ABC"
                                                                                        />
                                                                                    </div>
                                                                                    <div
                                                                                        class="col-md-6 mb-2"
                                                                                    >
                                                                                        <label
                                                                                            class="form-label mb-0"
                                                                                            >{{
                                                                                                t.job_title
                                                                                            }}</label
                                                                                        >
                                                                                        <input
                                                                                            type="text"
                                                                                            v-model="
                                                                                                employment.job_title
                                                                                            "
                                                                                            class="form-control"
                                                                                            placeholder="Software Developer"
                                                                                        />
                                                                                    </div>
                                                                                    <div
                                                                                        class="col-md-12 mb-2"
                                                                                    >
                                                                                        <label
                                                                                            class="form-label mb-0"
                                                                                            >{{
                                                                                                t.Duration
                                                                                            }}</label
                                                                                        >
                                                                                        <input
                                                                                            type="text"
                                                                                            v-model="
                                                                                                employment.duration
                                                                                            "
                                                                                            class="form-control"
                                                                                            placeholder="e.g. 2018-2022"
                                                                                        />
                                                                                    </div>
                                                                                    <div
                                                                                        class="col-md-12 mb-2"
                                                                                    >
                                                                                        <label
                                                                                            class="form-label mb-0"
                                                                                            >{{
                                                                                                t.job_description
                                                                                            }}</label
                                                                                        >
                                                                                        <textarea
                                                                                            v-model="
                                                                                                employment.job_description
                                                                                            "
                                                                                            class="form-control"
                                                                                            rows="4"
                                                                                            placeholder="Describe your role and responsibilities"
                                                                                        ></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="text-end mt-3"
                                                                                >
                                                                                    <button
                                                                                        class="btn btn-danger btn-sm"
                                                                                        @click="
                                                                                            removeEmployment(
                                                                                                index
                                                                                            )
                                                                                        "
                                                                                    >
                                                                                        {{
                                                                                            t.de_em
                                                                                        }}
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Add Employment Button -->
                                                                <div
                                                                    class="text-start mt-4"
                                                                >
                                                                    <button
                                                                        class="btn btn-primary"
                                                                        @click="
                                                                            addEmployment
                                                                        "
                                                                    >
                                                                        +
                                                                        {{
                                                                            t.ad_em
                                                                        }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'education'
                                                        "
                                                    >
                                                        <div class="card-body">
                                                            <div class="mb-0">
                                                                <p
                                                                    class="text-muted mb-5"
                                                                >
                                                                    {{
                                                                        t.below4
                                                                    }}
                                                                </p>
                                                            </div>

                                                            <div
                                                                class="row mb-3"
                                                            >
                                                                <div
                                                                    v-for="(
                                                                        edu,
                                                                        index
                                                                    ) in personalInfo.education"
                                                                    :key="index"
                                                                    class="accordion mb-3"
                                                                >
                                                                    <div
                                                                        class="accordion-item"
                                                                    >
                                                                        <h2
                                                                            class="accordion-header"
                                                                        >
                                                                            <button
                                                                                class="accordion-button"
                                                                                type="button"
                                                                                @click="
                                                                                    toggleEducation(
                                                                                        index
                                                                                    )
                                                                                "
                                                                            >
                                                                                {{
                                                                                    (edu.field ||
                                                                                        "Education " +
                                                                                            (index +
                                                                                                1)) +
                                                                                    (edu.degree
                                                                                        ? " - " +
                                                                                          edu.degree
                                                                                        : "")
                                                                                }}
                                                                            </button>
                                                                        </h2>

                                                                        <div
                                                                            class="accordion-collapse collapse"
                                                                            :class="{
                                                                                show:
                                                                                    activeEducation ===
                                                                                    index,
                                                                            }"
                                                                        >
                                                                            <div
                                                                                class="accordion-body"
                                                                            >
                                                                                <div
                                                                                    class="card-body"
                                                                                >
                                                                                    <p
                                                                                        class="text-muted mb-5"
                                                                                    >
                                                                                        {{
                                                                                            t.below5
                                                                                        }}
                                                                                    </p>

                                                                                    <div
                                                                                        class="row mb-3"
                                                                                    >
                                                                                        <div
                                                                                            class="col-md-6"
                                                                                        >
                                                                                            <label
                                                                                                class="form-label required"
                                                                                                >{{
                                                                                                    t.School
                                                                                                }}</label
                                                                                            >
                                                                                            <input
                                                                                                type="text"
                                                                                                class="form-control"
                                                                                                v-model="
                                                                                                    edu.school
                                                                                                "
                                                                                            />
                                                                                        </div>
                                                                                        <div
                                                                                            class="col-md-6"
                                                                                        >
                                                                                            <label
                                                                                                class="form-label required"
                                                                                                >{{
                                                                                                    t.Degree
                                                                                                }}</label
                                                                                            >
                                                                                            <input
                                                                                                type="text"
                                                                                                class="form-control"
                                                                                                v-model="
                                                                                                    edu.degree
                                                                                                "
                                                                                            />
                                                                                        </div>
                                                                                        <div
                                                                                            class="col-md-12"
                                                                                        >
                                                                                            <label
                                                                                                class="form-label"
                                                                                                >{{
                                                                                                    t.stud
                                                                                                }}</label
                                                                                            >
                                                                                            <input
                                                                                                type="text"
                                                                                                class="form-control"
                                                                                                v-model="
                                                                                                    edu.field
                                                                                                "
                                                                                            />
                                                                                        </div>
                                                                                    </div>

                                                                                    <div
                                                                                        class="row mb-3"
                                                                                    >
                                                                                        <div
                                                                                            class="col-md-6"
                                                                                        >
                                                                                            <label
                                                                                                class="form-label"
                                                                                                >{{
                                                                                                    t.start_date
                                                                                                }}</label
                                                                                            >
                                                                                            <input
                                                                                                type="date"
                                                                                                class="form-control"
                                                                                                v-model="
                                                                                                    edu.start_date
                                                                                                "
                                                                                            />
                                                                                        </div>
                                                                                        <div
                                                                                            class="col-md-6"
                                                                                        >
                                                                                            <label
                                                                                                class="form-label"
                                                                                                >{{
                                                                                                    t.end_date
                                                                                                }}</label
                                                                                            >
                                                                                            <input
                                                                                                type="date"
                                                                                                class="form-control"
                                                                                                v-model="
                                                                                                    edu.end_date
                                                                                                "
                                                                                            />
                                                                                        </div>
                                                                                    </div>

                                                                                    <div
                                                                                        class="mb-3"
                                                                                    >
                                                                                        <label
                                                                                            class="form-label"
                                                                                            >{{
                                                                                                t.description
                                                                                            }}</label
                                                                                        >
                                                                                        <textarea
                                                                                            class="form-control"
                                                                                            v-model="
                                                                                                edu.descriptions
                                                                                            "
                                                                                            rows="3"
                                                                                        ></textarea>
                                                                                    </div>

                                                                                    <button
                                                                                        v-if="
                                                                                            personalInfo
                                                                                                .education
                                                                                                .length >
                                                                                            1
                                                                                        "
                                                                                        class="btn btn-danger"
                                                                                        @click="
                                                                                            removeEducation(
                                                                                                index
                                                                                            )
                                                                                        "
                                                                                    >
                                                                                        {{
                                                                                            t.de_ed
                                                                                        }}
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- زر إضافة عنصر جديد -->
                                                                <div
                                                                    class="mt-4"
                                                                >
                                                                    <button
                                                                        class="btn btn-primary"
                                                                        @click="
                                                                            addEducation
                                                                        "
                                                                    >
                                                                        {{
                                                                            t.ad_ed
                                                                        }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'skillsList'
                                                        "
                                                    >
                                                        <div
                                                            style="
                                                                height: 72px;
                                                                display: flex;
                                                            "
                                                        >
                                                            <p
                                                                class="text-muted mb-4"
                                                            >
                                                                {{ t.below6 }}
                                                            </p>

                                                            <!-- الزر مع Dropdown -->
                                                            <div
                                                                class="dropdown"
                                                            >
                                                                <!-- الزر الأساسي -->
                                                                <div
                                                                    class="d-flex flex-center w-30px h-30px rounded-3 bg-light-success bg-opacity-90 mb-10"
                                                                    data-bs-toggle="dropdown"
                                                                    aria-expanded="false"
                                                                    style="
                                                                        cursor: pointer;
                                                                    "
                                                                >
                                                                    <span
                                                                        v-if="
                                                                            isLoadingSummary
                                                                        "
                                                                        class="spinner-border spinner-border-sm text-warning me-2"
                                                                        role="status"
                                                                        aria-hidden="true"
                                                                    ></span>

                                                                    <i
                                                                        v-else
                                                                        class="ki-duotone ki-square-brackets text-success fs-2x"
                                                                    >
                                                                        <span
                                                                            class="path1"
                                                                        ></span>
                                                                        <span
                                                                            class="path2"
                                                                        ></span>
                                                                        <span
                                                                            class="path3"
                                                                        ></span>
                                                                        <span
                                                                            class="path4"
                                                                        ></span>
                                                                    </i>
                                                                </div>

                                                                <!-- القائمة المنسدلة -->
                                                                <ul
                                                                    class="dropdown-menu select2-dropdown w-150px select2-dropdown--below show"
                                                                    data-bs-auto-close="outside"
                                                                >
                                                                    <li>
                                                                        <span
                                                                            class="dropdown-item"
                                                                            @mousedown.prevent.stop="
                                                                                Suggestion(
                                                                                    'skillsList'
                                                                                )
                                                                            "
                                                                            >Suggestion</span
                                                                        >
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="form-check mb-3"
                                                        >
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                id="hideExperienceLevel"
                                                            />
                                                            <label
                                                                class="form-check-label"
                                                                for="hideExperienceLevel"
                                                            >
                                                                {{ t.below7 }}
                                                            </label>
                                                        </div>

                                                        <!-- Tabs Navigation -->
                                                        <ul
                                                            class="nav nav-tabs SkillsMenu mb-3"
                                                        >
                                                            <li
                                                                class="nav-item"
                                                                v-for="(
                                                                    skill, index
                                                                ) in personalInfo.skillsList"
                                                                :key="index"
                                                            >
                                                                <button
                                                                    class="nav-link"
                                                                    :style="{
                                                                        backgroundColor:
                                                                            skill.level &&
                                                                            levelColors[
                                                                                skill
                                                                                    .level
                                                                            ]
                                                                                ? getTransparentColor(
                                                                                      levelColors[
                                                                                          skill
                                                                                              .level
                                                                                      ],
                                                                                      0.2
                                                                                  )
                                                                                : 'rgba(0, 0, 0, 0.05)',
                                                                        color: levelColors[
                                                                            skill
                                                                                .level
                                                                        ],
                                                                    }"
                                                                    :class="{
                                                                        active:
                                                                            activeSkillIndex ===
                                                                            index,
                                                                    }"
                                                                    @click="
                                                                        activeSkillIndex =
                                                                            index
                                                                    "
                                                                >
                                                                    {{
                                                                        skill.name ||
                                                                        `Skill ${
                                                                            index +
                                                                            1
                                                                        }`
                                                                    }}
                                                                    -
                                                                    {{
                                                                        skill.level ||
                                                                        "No Level"
                                                                    }}

                                                                    <span
                                                                        @click="
                                                                            removeSkill(
                                                                                index
                                                                            )
                                                                        "
                                                                        :class="{
                                                                            'float-left':
                                                                                locale ===
                                                                                'ar',
                                                                            'float-right':
                                                                                locale !==
                                                                                'ar',
                                                                        }"
                                                                        class="btn btn-icon btn-circle btn-light-danger"
                                                                        style="
                                                                            width: 20px;
                                                                            height: 20px;
                                                                            padding: 0;
                                                                        "
                                                                    >
                                                                        <i
                                                                            class="bi bi-dash fs-8 text-danger"
                                                                        ></i>
                                                                    </span>
                                                                </button>
                                                            </li>
                                                        </ul>

                                                        <div
                                                            class="row p-2 mt-3"
                                                        >
                                                            <!-- Loop over skills and display the selected tab -->
                                                            <div
                                                                class="boxSkills"
                                                                v-for="(
                                                                    skill, index
                                                                ) in personalInfo.skillsList"
                                                                :key="index"
                                                                v-show="
                                                                    activeSkillIndex ===
                                                                    index
                                                                "
                                                            >
                                                                <div
                                                                    class="mb-3"
                                                                >
                                                                    <label
                                                                        for="skillName1"
                                                                        class="form-label"
                                                                        >{{
                                                                            t.skill_name
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        v-model="
                                                                            skill.name
                                                                        "
                                                                        placeholder="Enter skill name"
                                                                    />
                                                                </div>

                                                                <div
                                                                    class="mb-4"
                                                                >
                                                                    <label
                                                                        class="form-label d-block fw-bold text-muted"
                                                                    >
                                                                        {{
                                                                            t.level
                                                                        }}
                                                                        —
                                                                        <span
                                                                            :style="{
                                                                                color: levelColors[
                                                                                    skill
                                                                                        .level
                                                                                ],
                                                                            }"
                                                                            >{{
                                                                                skill.level
                                                                            }}</span
                                                                        >
                                                                    </label>

                                                                    <div
                                                                        class="level-slider d-flex align-items-center justify-content-between"
                                                                        :style="{
                                                                            backgroundColor:
                                                                                skill.level &&
                                                                                levelColors[
                                                                                    skill
                                                                                        .level
                                                                                ]
                                                                                    ? getTransparentColor(
                                                                                          levelColors[
                                                                                              skill
                                                                                                  .level
                                                                                          ],
                                                                                          0.2
                                                                                      )
                                                                                    : 'rgba(0, 0, 0, 0.05)',
                                                                        }"
                                                                    >
                                                                        <div
                                                                            v-for="(
                                                                                level,
                                                                                idx
                                                                            ) in levels"
                                                                            :key="
                                                                                idx
                                                                            "
                                                                            class="level-step"
                                                                            :class="{
                                                                                active:
                                                                                    skill.level ===
                                                                                    level,
                                                                            }"
                                                                            :style="
                                                                                skill.level ===
                                                                                level
                                                                                    ? {
                                                                                          backgroundColor:
                                                                                              levelColors[
                                                                                                  level
                                                                                              ],
                                                                                      }
                                                                                    : {}
                                                                            "
                                                                            @click="
                                                                                skill.level =
                                                                                    level
                                                                            "
                                                                        ></div>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="d-flex justify-content-end gap-2"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        @click="
                                                                            removeSkill(
                                                                                index
                                                                            )
                                                                        "
                                                                    >
                                                                        {{
                                                                            t.delete
                                                                        }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button
                                                            class="btn btn-success mt-3"
                                                            @click="addSkill"
                                                        >
                                                            +
                                                            {{
                                                                t.enter_skill_name
                                                            }}
                                                        </button>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'languagesList'
                                                        "
                                                    >
                                                        <p
                                                            class="text-muted mb-4"
                                                        >
                                                            {{
                                                                t.description_2
                                                            }}
                                                        </p>

                                                        <!-- Tabs Navigation -->
                                                        <ul
                                                            class="nav nav-tabs SkillsMenu mb-3"
                                                        >
                                                            <li
                                                                class="nav-item"
                                                                v-for="(
                                                                    language,
                                                                    index
                                                                ) in personalInfo.languagesList"
                                                                :key="index"
                                                            >
                                                                <button
                                                                    class="nav-link"
                                                                    :style="{
                                                                        backgroundColor:
                                                                            language.level &&
                                                                            levelColors[
                                                                                language
                                                                                    .level
                                                                            ]
                                                                                ? getTransparentColor(
                                                                                      levelColors[
                                                                                          language
                                                                                              .level
                                                                                      ],
                                                                                      0.2
                                                                                  )
                                                                                : 'rgba(0, 0, 0, 0.05)',
                                                                        color: levelColors[
                                                                            language
                                                                                .level
                                                                        ],
                                                                    }"
                                                                    :class="{
                                                                        active:
                                                                            activeLanguageIndex ===
                                                                            index,
                                                                    }"
                                                                    @click="
                                                                        activeLanguageIndex =
                                                                            index
                                                                    "
                                                                >
                                                                    {{
                                                                        language.name ||
                                                                        `Language ${
                                                                            index +
                                                                            1
                                                                        }`
                                                                    }}
                                                                    -
                                                                    {{
                                                                        language.level ||
                                                                        t.no_level
                                                                    }}
                                                                </button>
                                                            </li>
                                                        </ul>

                                                        <div
                                                            class="row p-2 mt-3"
                                                        >
                                                            <div
                                                                class="boxSkills"
                                                                v-for="(
                                                                    language,
                                                                    index
                                                                ) in personalInfo.languagesList"
                                                                :key="index"
                                                                v-show="
                                                                    activeLanguageIndex ===
                                                                    index
                                                                "
                                                            >
                                                                <div
                                                                    class="mb-3"
                                                                >
                                                                    <label
                                                                        class="form-label"
                                                                        >{{
                                                                            t.language_name
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        v-model="
                                                                            language.name
                                                                        "
                                                                        :placeholder="
                                                                            t.enter_language
                                                                        "
                                                                    />
                                                                </div>

                                                                <div
                                                                    class="mb-4"
                                                                >
                                                                    <label
                                                                        class="form-label d-block fw-bold text-muted"
                                                                    >
                                                                        {{
                                                                            t.level
                                                                        }}
                                                                        —
                                                                        <span
                                                                            :style="{
                                                                                color: levelColors[
                                                                                    language
                                                                                        .level
                                                                                ],
                                                                            }"
                                                                        >
                                                                            {{
                                                                                language.level
                                                                            }}
                                                                        </span>
                                                                    </label>

                                                                    <div
                                                                        class="level-slider d-flex align-items-center justify-content-between"
                                                                        :style="{
                                                                            backgroundColor:
                                                                                language.level &&
                                                                                levelColors[
                                                                                    language
                                                                                        .level
                                                                                ]
                                                                                    ? getTransparentColor(
                                                                                          levelColors[
                                                                                              language
                                                                                                  .level
                                                                                          ],
                                                                                          0.2
                                                                                      )
                                                                                    : 'rgba(0, 0, 0, 0.05)',
                                                                        }"
                                                                    >
                                                                        <div
                                                                            v-for="(
                                                                                level,
                                                                                idx
                                                                            ) in levels"
                                                                            :key="
                                                                                idx
                                                                            "
                                                                            class="level-step"
                                                                            :class="{
                                                                                active:
                                                                                    language.level ===
                                                                                    level,
                                                                            }"
                                                                            :style="
                                                                                language.level ===
                                                                                level
                                                                                    ? {
                                                                                          backgroundColor:
                                                                                              levelColors[
                                                                                                  level
                                                                                              ],
                                                                                      }
                                                                                    : {}
                                                                            "
                                                                            @click="
                                                                                language.level =
                                                                                    level
                                                                            "
                                                                        ></div>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="d-flex justify-content-end gap-2"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        @click="
                                                                            removeLanguage(
                                                                                index
                                                                            )
                                                                        "
                                                                    >
                                                                        {{
                                                                            t.delete
                                                                        }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button
                                                            class="btn btn-success mt-3"
                                                            @click="addLanguage"
                                                        >
                                                            +
                                                            {{ t.add_language }}
                                                        </button>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'socialLinks'
                                                        "
                                                    >
                                                        <p
                                                            class="text-muted mb-4"
                                                        >
                                                            {{
                                                                t.description_3
                                                            }}
                                                        </p>

                                                        <!-- Tabs Navigation -->
                                                        <ul
                                                            class="nav nav-tabs SkillsMenu mb-3"
                                                        >
                                                            <li
                                                                class="nav-item"
                                                                v-for="(
                                                                    link, index
                                                                ) in personalInfo.socialLinks"
                                                                :key="index"
                                                            >
                                                                <button
                                                                    class="nav-link"
                                                                    :class="{
                                                                        active:
                                                                            activeSocialIndex ===
                                                                            index,
                                                                    }"
                                                                    @click="
                                                                        activeSocialIndex =
                                                                            index
                                                                    "
                                                                >
                                                                    {{
                                                                        link.platform ||
                                                                        `Link ${
                                                                            index +
                                                                            1
                                                                        }`
                                                                    }}
                                                                </button>
                                                            </li>
                                                        </ul>

                                                        <div
                                                            class="row p-2 mt-3"
                                                        >
                                                            <!-- Loop to display selected tab -->
                                                            <div
                                                                class="boxSkills"
                                                                v-for="(
                                                                    link, index
                                                                ) in personalInfo.socialLinks"
                                                                :key="index"
                                                                v-show="
                                                                    activeSocialIndex ===
                                                                    index
                                                                "
                                                            >
                                                                <div
                                                                    class="mb-3"
                                                                >
                                                                    <label
                                                                        class="form-label"
                                                                        >{{
                                                                            t.platform_name
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        v-model="
                                                                            link.platform
                                                                        "
                                                                        :placeholder="
                                                                            t.enter_platform
                                                                        "
                                                                    />
                                                                </div>

                                                                <div
                                                                    class="mb-3"
                                                                >
                                                                    <label
                                                                        class="form-label"
                                                                        >{{
                                                                            t.profile_url
                                                                        }}</label
                                                                    >
                                                                    <input
                                                                        type="url"
                                                                        class="form-control"
                                                                        v-model="
                                                                            link.url
                                                                        "
                                                                        :placeholder="
                                                                            t.enter_url
                                                                        "
                                                                    />
                                                                </div>

                                                                <div
                                                                    class="d-flex justify-content-end gap-2"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        @click="
                                                                            removeSocial(
                                                                                index
                                                                            )
                                                                        "
                                                                    >
                                                                        {{
                                                                            t.delete
                                                                        }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button
                                                            class="btn btn-success mt-3"
                                                            @click="addSocial"
                                                        >
                                                            + {{ t.add_social }}
                                                        </button>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'color'
                                                        "
                                                    >
                                                        <div class="card-body">
                                                            <p
                                                                class="text-muted mb-4"
                                                            >
                                                                {{
                                                                    t.description_4
                                                                }}
                                                            </p>

                                                            <!-- Primary Color -->
                                                            <!-- <div class="mb-3">
                                                        <label class="form-label">{{ t.primary_color }}</label>
                                                        <input
                                                            type="color"
                                                            class="form-control form-control-color"
                                                            name="primary_color"
                                                            value="#fe6f00"
                                                            :title="t.primary_note"
                                                            v-model="personalInfo.theme.mainBgColor"
                                                        />
                                                        <small class="text-muted">{{ t.primary_note }}</small>
                                                    </div> -->

                                                            <!-- Secondary Color -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                    >{{
                                                                        t.sidebar_bg_color
                                                                    }}</label
                                                                >
                                                                <input
                                                                    type="color"
                                                                    class="form-control form-control-color"
                                                                    name="sidebar_bg_color"
                                                                    value="#000000"
                                                                    :title="
                                                                        t.sidebar_note
                                                                    "
                                                                    v-model="
                                                                        personalInfo
                                                                            .theme
                                                                            .sidebarBgColor
                                                                    "
                                                                />
                                                                <small
                                                                    class="text-muted"
                                                                    >{{
                                                                        t.sidebar_bg_color
                                                                    }}</small
                                                                >
                                                            </div>

                                                            <!-- Sidebar Background -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                    >{{
                                                                        t.percentage_bg_color
                                                                    }}</label
                                                                >
                                                                <input
                                                                    type="color"
                                                                    class="form-control form-control-color"
                                                                    name="sidebar_bg_color"
                                                                    value="#f5deb3"
                                                                    :title="
                                                                        t.sidebar_note
                                                                    "
                                                                    v-model="
                                                                        personalInfo
                                                                            .theme
                                                                            .barColor
                                                                    "
                                                                />
                                                                <small
                                                                    class="text-muted"
                                                                    >{{
                                                                        t.text_note
                                                                    }}</small
                                                                >
                                                            </div>

                                                            <!-- Text Color -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                    >{{
                                                                        t.title_section_color
                                                                    }}</label
                                                                >
                                                                <input
                                                                    type="color"
                                                                    class="form-control form-control-color"
                                                                    name="text_color"
                                                                    value="#333333"
                                                                    :title="
                                                                        t.text_note
                                                                    "
                                                                    v-model="
                                                                        personalInfo
                                                                            .theme
                                                                            .titleColor
                                                                    "
                                                                />
                                                                <small
                                                                    class="text-muted"
                                                                    >{{
                                                                        t.text_note
                                                                    }}</small
                                                                >
                                                            </div>

                                                            <!-- Links Color -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                    >{{
                                                                        t.links_color
                                                                    }}</label
                                                                >
                                                                <input
                                                                    type="color"
                                                                    class="form-control form-control-color"
                                                                    name="links_color"
                                                                    value="#007bff"
                                                                    :title="
                                                                        t.links_note
                                                                    "
                                                                    v-model="
                                                                        personalInfo
                                                                            .theme
                                                                            .linkColor
                                                                    "
                                                                />
                                                                <small
                                                                    class="text-muted"
                                                                    >{{
                                                                        t.links_note
                                                                    }}</small
                                                                >
                                                            </div>

                                                            <!-- Profile Border Color -->

                                                            <!-- Font Family -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                    >{{
                                                                        t.font_family
                                                                    }}</label
                                                                >
                                                                <select
                                                                    class="form-select"
                                                                    v-model="
                                                                        personalInfo
                                                                            .theme
                                                                            .fontFamily
                                                                    "
                                                                    id="fontFamilySelect"
                                                                    name="font_family"
                                                                >
                                                                    <option
                                                                        value="Arial"
                                                                    >
                                                                        Arial
                                                                    </option>
                                                                    <option
                                                                        value="Roboto"
                                                                    >
                                                                        Roboto
                                                                    </option>
                                                                    <option
                                                                        value="Open Sans"
                                                                    >
                                                                        Open
                                                                        Sans
                                                                    </option>
                                                                    <option
                                                                        value="Tajawal"
                                                                    >
                                                                        Tajawal
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-else-if="
                                                            section.key ===
                                                            'additionalSections'
                                                        "
                                                    >
                                                        <div
                                                            v-for="(
                                                                section, index
                                                            ) in personalInfo.additionalSections"
                                                            :key="index"
                                                            class="accordion-item"
                                                        >
                                                            <h2
                                                                class="accordion-header"
                                                                :id="`additional_header_${index}`"
                                                            >
                                                                <button
                                                                    class="accordion-button fs-4 fw-semibold"
                                                                    :class="{
                                                                        collapsed:
                                                                            activeAdditionalIndex !==
                                                                            index,
                                                                    }"
                                                                    type="button"
                                                                    @click="
                                                                        activeAdditionalIndex =
                                                                            index
                                                                    "
                                                                    :aria-expanded="
                                                                        activeAdditionalIndex ===
                                                                        index
                                                                    "
                                                                    data-bs-toggle="collapse"
                                                                    :data-bs-target="`#additional_section_${index}`"
                                                                    :aria-controls="`additional_section_${index}`"
                                                                >
                                                                    {{
                                                                        section.field ||
                                                                        t.default_title.replace(
                                                                            ":number",
                                                                            index +
                                                                                1
                                                                        )
                                                                    }}
                                                                </button>
                                                            </h2>

                                                            <div
                                                                class="accordion-collapse collapse"
                                                                :class="{
                                                                    show:
                                                                        activeAdditionalIndex ===
                                                                        index,
                                                                }"
                                                                :id="`additional_section_${index}`"
                                                                :aria-labelledby="`additional_header_${index}`"
                                                            >
                                                                <div
                                                                    class="accordion-body"
                                                                >
                                                                    <div
                                                                        class="card-body"
                                                                    >
                                                                        <p
                                                                            class="text-muted mb-4"
                                                                        >
                                                                            {{
                                                                                t.description_5
                                                                            }}
                                                                        </p>

                                                                        <div
                                                                            class="mb-3"
                                                                        >
                                                                            <label
                                                                                class="form-label"
                                                                                >{{
                                                                                    t.section_name
                                                                                }}</label
                                                                            >
                                                                            <input
                                                                                type="text"
                                                                                class="form-control form-control-solid"
                                                                                v-model="
                                                                                    section.field
                                                                                "
                                                                                :placeholder="
                                                                                    t.section_name_placeholder
                                                                                "
                                                                            />
                                                                        </div>

                                                                        <div
                                                                            class="row mb-3"
                                                                        >
                                                                            <div
                                                                                class="col-md-6"
                                                                            >
                                                                                <label
                                                                                    class="form-label"
                                                                                    >{{
                                                                                        t.start_date
                                                                                    }}</label
                                                                                >
                                                                                <input
                                                                                    type="date"
                                                                                    class="form-control form-control-solid"
                                                                                    v-model="
                                                                                        section.start_date
                                                                                    "
                                                                                />
                                                                            </div>
                                                                            <div
                                                                                class="col-md-6"
                                                                            >
                                                                                <label
                                                                                    class="form-label"
                                                                                    >{{
                                                                                        t.end_date
                                                                                    }}</label
                                                                                >
                                                                                <input
                                                                                    type="date"
                                                                                    class="form-control form-control-solid"
                                                                                    v-model="
                                                                                        section.end_date
                                                                                    "
                                                                                />
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="mb-3"
                                                                        >
                                                                            <label
                                                                                class="form-label"
                                                                                >{{
                                                                                    t.description_label
                                                                                }}</label
                                                                            >
                                                                            <textarea
                                                                                class="form-control form-control-solid"
                                                                                v-model="
                                                                                    section.description
                                                                                "
                                                                                rows="3"
                                                                                :placeholder="
                                                                                    t.description_placeholder
                                                                                "
                                                                            ></textarea>
                                                                        </div>

                                                                        <div
                                                                            class="d-flex justify-content-end"
                                                                        >
                                                                            <button
                                                                                class="btn btn-danger btn-sm"
                                                                                @click="
                                                                                    removeAdditionalSection(
                                                                                        index
                                                                                    )
                                                                                "
                                                                            >
                                                                                {{
                                                                                    t.delete
                                                                                }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="AddSection">
                                                            <button
                                                                class="btn btn-primary w-100"
                                                                @click="
                                                                    personalInfo?.additionalSections &&
                                                                        addAdditionalSection()
                                                                "
                                                            >
                                                                {{
                                                                    t.add_section
                                                                }}
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- أضف else-if لأقسام أخرى -->
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>

                            <!--end::Accordion-->

                            <!-- Buttons Row -->

                            <!-- Save & Cancel -->
                            <div
                                class="d-flex justify-content-between ActionsButons"
                            >
                                <!-- Previous Button (disabled in step 1) -->
                                <button
                                    type="button"
                                    class="btn btn-light-danger"
                                    @click="cancelEditTemplate()"
                                >
                                    {{ t.cancel }}
                                </button>
                                <!-- Next Button -->
                                <button
                                    @click="addEditItem"
                                    type="button"
                                    class="btn btn-primary"
                                    data-kt-stepper-action="next"
                                    :disabled="isLoading"
                                >
                                    <span
                                        v-if="!isLoading"
                                        class="indicator-label"
                                    >
                                        {{ t.save }}</span
                                    >
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span
                                        v-else
                                        class="indicator-progress"
                                        :style="{
                                            display: isLoading
                                                ? 'inline-flex'
                                                : 'none',
                                        }"
                                    >
                                        {{ t.Please_wait }}

                                        <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                        ></span
                                    ></span>
                                </button>

                                <!-- <button @click="exportToPDF">Download PDF</button> -->
                            </div>

                            <div
                                class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid"
                                v-if="couponDisplay"
                                style="margin-top: 24px"
                            >
                                <input
                                    v-model="coupon"
                                    type="text"
                                    name="text_input"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="Coupon hrer..."
                                    value=""
                                />
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"
                                >
                                    <div
                                        data-field="text_input"
                                        data-validator="notEmpty"
                                    >
                                        Please coupon is required .
                                    </div>
                                </div>
                            </div>

                            <div
                                class="d-flex justify-content-between ActionsButons"
                            >
                                <!-- Next Button -->
                                <button
                                    style="width: 100%"
                                    @click="exportToPDF"
                                    type="button"
                                    class="btn btn-light-danger"
                                    data-kt-stepper-action="next"
                                    :disabled="isLoading"
                                    v-if="ItemID != ''"
                                >
                                    <span
                                        v-if="!isLoading"
                                        class="indicator-label"
                                    >
                                        {{ t.ExportPDF }}</span
                                    >

                                    <span
                                        v-else
                                        class="indicator-progress"
                                        :style="{
                                            display: isLoading
                                                ? 'inline-flex'
                                                : 'none',
                                        }"
                                    >
                                        {{ t.Please_wait }}

                                        <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                        ></span
                                    ></span>
                                </button>
                            </div>
                            <!-- End Save & Cancel -->
                        </div>
                    </div>

                    <div class="col-md-8 contentCvViewed">
                        <button
                            v-if="!editTemplate"
                            class="btn btn-light-primary contentCvViewedBtn"
                            @click="EditTemplate()"
                        >
                            <span class="spanTyping"
                                >{{ displayedText
                                }}<span class="typing-cursor">|</span></span
                            >
                        </button>
                        <div ref="cvContent" class="cv-container h-100">
                            <div
                                v-html="
                                    `<style>${selectedTemplate.content_css}</style>`
                                "
                            ></div>
                            <component
                                :is="dynamicComponent"
                                ref="cvContent"
                                id="contentToExport"
                            ></component>
                        </div>
                    </div>
                </div>
                <!--begin::Col-->
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="kt_modal_scrollable_1">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">{{ t.title_tips }}</h5>
                    <div
                        class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal"
                    >
                        <i class="ki-duotone ki-cross fs-2x"
                            ><span class="path1"></span
                            ><span class="path2"></span
                        ></i>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body" style="min-height: 500px">
                    <div class="card-body p-lg-15">
                        <div class="mb-13">
                            <!-- Intro -->
                            <div class="mb-15">
                                <h4
                                    class="fs-2x text-gray-800 w-bolder mb-6"
                                    style="font-size: 1.8rem !important"
                                >
                                    {{ t.sera_check }}
                                </h4>
                                <p class="fw-semibold fs-4 text-gray-600 mb-2">
                                    {{ t.tips }}
                                </p>
                            </div>

                            <!-- Score bar -->
                            <div class="row mb-12">
                                <div class="col-md-12 pe-md-10 mb-10 mb-md-0">
                                    <div
                                        class="d-flex align-items-center flex-column mt-3 w-100"
                                    >
                                        <div
                                            class="d-flex justify-content-between w-100 mt-auto mb-2"
                                        >
                                            <span
                                                class="fw-bolder fs-6 text-gray-900"
                                                >{{ t.gool }}</span
                                            >
                                            <span
                                                class="fw-bold fs-6 text-gray-500"
                                                >{{ score_ats }}%</span
                                            >
                                        </div>
                                        <div
                                            class="h-8px mx-3 w-100 bg-light-success rounded"
                                        >
                                            <div
                                                class="bg-success rounded h-8px"
                                                role="progressbar"
                                                :style="{
                                                    width: score_ats + '%',
                                                }"
                                                :aria-valuenow="score_ats"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- ATS Sections -->
                                    <div
                                        v-for="(
                                            issues, section, index
                                        ) in sections_ats"
                                        :key="index"
                                        class="m-0"
                                    >
                                        <div
                                            class="d-flex align-items-center collapsible py-3 toggle mb-0 collapsed"
                                            :data-bs-toggle="'collapse'"
                                            :data-bs-target="
                                                '#ats_section_' + index
                                            "
                                            aria-expanded="false"
                                        >
                                            <div
                                                class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5"
                                            >
                                                <i
                                                    class="ki-duotone ki-minus-square toggle-on text-primary fs-1"
                                                    ><span class="path1"></span
                                                    ><span class="path2"></span
                                                ></i>
                                                <i
                                                    class="ki-duotone ki-plus-square toggle-off fs-1"
                                                    ><span class="path1"></span
                                                    ><span class="path2"></span
                                                    ><span class="path3"></span
                                                ></i>
                                            </div>
                                            <h4
                                                class="text-gray-700 fw-bold cursor-pointer mb-0"
                                            >
                                                {{ section }}
                                            </h4>
                                        </div>

                                        <div
                                            class="fs-6 ms-1 collapse"
                                            :id="'ats_section_' + index"
                                        >
                                            <ul
                                                class="mb-4 text-gray-600 fw-semibold fs-6 ps-10"
                                            >
                                                <li
                                                    v-for="(issue, i) in issues"
                                                    :key="i"
                                                >
                                                    {{ issue }}
                                                </li>
                                            </ul>
                                        </div>

                                        <div
                                            v-if="
                                                index <
                                                Object.keys(sections_ats)
                                                    .length -
                                                    1
                                            "
                                            class="separator separator-dashed"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        {{ t.close }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import html2canvas from "html2canvas";
import draggable from "vuedraggable";
import html2pdf from "html2pdf.js";

import axios from "axios";
import { mapActions, mapState } from "vuex";
import { markRaw } from "vue";

const myComponent = markRaw({
    template: "...",
    // أي خصائص أخرى
});
export default {
    components: { draggable },
    data() {
        return {
            fullText:
                "Review and update your CV details to ensure they are up to date and accurate.",
            displayedText: "",
            typingIndex: 0,
            openAccordion: null,
            typingInterval: null,
            ItemID: "",
            itemsResume: "",

            coupon: "",
            checkCoupon: false,
            couponDisplay: false,

            thumbnailDataUrl: null,
            loadingType: null,

            activeAdditionalIndex: 0,
            activeSkillIndex: 0,
            activeLanguageIndex: 0,
            activeEducation: 0,
            activeSocialIndex: 0,
            activeIndex: 0,

            levels: ["Novice", "Beginner", "Skillful", "Experienced", "Expert"],

            selectedLevel: "Experienced",
            levelColors: {
                Novice: "#fe7d8b",
                Beginner: "#f68559",
                Skillful: "#f68559",
                Experienced: "#48ba75",
                Expert: "#9ba1fb",
            },
            // t: window.translations,
            languages: {},
            isLoading: false,
            isLoadingSummary: false,

            socialMediaIcons: [],
            allTemplates: [],
            items: [],
            selectedTemplate: {},

            editTemplate: false,

            dynamicComponent: null,
            cvCssFromApi: null,

            score_ats: "",
            sections_ats: [],

            selectedSections: [
                { key: "personalInfo", column: "right" },
                { key: "summary", column: "right" },
                { key: "education", column: "left" },
                { key: "employmentHistory", column: "left" },
                { key: "skillsList", column: "left" },
                { key: "languagesList", column: "left" },
                { key: "socialLinks", column: "right" },
                { key: "color", column: "right" },
                { key: "additionalSections", column: "right" },
            ],

            sectionTitles: {
                personalInfo: "Personal Information",
                summary: "Summary",
                employmentHistory: "Employment History",
                education: "education",
                skillsList: "Skills",
                languagesList: "Languages",
                socialLinks: "social and Links",
                color: "Theme Customization",
                additionalSections: "additional Sections",
            },

            levelWidths: {
                Novice: "20",
                Beginner: "40",
                Skillful: "60",
                Experienced: "80",
                Expert: "100",
            },

            personalInfo: {
                first_name: "Mohamed",
                last_name: "Ijla",
                job_title: "Full Stack Developer",
                email: "mohamed.alijla@gmail.com",
                mobile: "+970599895357",
                address: "Gaza Strip",
                city: "Gaza",
                country: "Palestine",
                postal_code: "0097",
                driving_license: "B",
                nationality: "Palestinian",
                place_of_birth: "Palestine",
                date_of_birth: "1989-02-09",
                logo: "http://127.0.0.1:8000/assets/ijla.jfif",
                previewUrl: "http://127.0.0.1:8000/assets/ijla.jfif",
                summary: "Experienced software developer...",
                employmentHistory: [
                    {
                        job_title: "Full Stack Developer",
                        company_name: "Aramico Company",
                        duration: "2021-2025",
                        job_description: "Developed and Management Systems.",
                    },

                    {
                        job_title: "Team Leader Developer",
                        company_name: "Work Without Borders",
                        duration: "2015-2020",
                        job_description: "Developed and Management Systems.",
                    },
                ],
                education: [
                    {
                        id: 1,
                        degree: "Master in AI",
                        school: "University of Al azhar",
                        start_date: "2019-09-01",
                        end_date: "2021-06-01",
                        field: "Computer Engineering",
                        descriptions:
                            "Studied computer science and software development.",
                    },

                    {
                        id: 1,
                        degree: "Bachelor's in Computer Engineering",
                        school: "University of Al azhar",
                        start_date: "2007-09-01",
                        end_date: "2012-06-01",
                        field: "Computer Engineering",
                        descriptions:
                            "Studied computer science and software development.",
                    },
                ],

                skillsList: [
                    {
                        name: "JavaScript",
                        level: "Expert",
                        percentage: "100%",
                    },
                    {
                        name: "React",
                        level: "Skillful",
                        percentage: "60%",
                    },
                    {
                        name: "Node.js",
                        level: "Experienced",
                        percentage: "80%",
                    },
                    {
                        name: "HTML",
                        level: "Expert",
                        percentage: "100%",
                    },
                    {
                        name: "CSS",
                        level: "Expert",
                        percentage: "100%",
                    },
                    {
                        name: "Git",
                        level: "Expert",
                        percentage: "100%",
                    },
                    {
                        name: "SQL",
                        level: "",
                        percentage: "0%",
                    },
                    {
                        name: "RESTful APIs",
                        level: "Expert",
                        percentage: "100%",
                    },
                    {
                        name: "AWS",
                        level: "Experienced",
                        percentage: "80%",
                    },
                ],
                languagesList: [
                    {
                        name: "English",
                        level: "Experienced",
                        percentage: "80%",
                    },
                ],
                socialLinks: [
                    {
                        platform: "LinkedIn",
                        url: "https://www.linkedin.com/in/mohamed-al-ijla-087700167/",
                    },
                ],
                additionalSections: [
                    {
                        field: "",
                        start_date: "",
                        end_date: "",
                        description: "",
                    },
                ],

                theme: {
                    // mainBgColor: "#f5deb3",   // page
                    sidebarBgColor: "", // siderbar
                    // textColor: "#333333",      // text
                    titleColor: "", // title
                    barColor: "", // as skills , languages
                    linkColor: "", // as skills , languages
                    fontFamily: "",
                },
            },
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {
            this.fetchItems();
        });

        if (this.$route.query.id) {
            this.ItemID = this.$route.query.id;
            this.combaindTemplate(this.ItemID);
        }
        this.startTypingEffect();

        const t = this.$store.getters.getTranslations;
        this.sectionTitles = {
            personalInfo: t.personalInfo,
            summary: t.summary,
            employmentHistory: t.employmentHistory,
            education: t.education,
            skillsList: t.skillsList,
            languagesList: t.languagesList,
            socialLinks: t.socialLinks,
            color: t.color,
            additionalSections: t.additionalSections,
        };
    },

    computed: {
        setting() {
            return this.$store.getters.getSetting;
        },

        locale() {
            return this.$route.params.locale;
        },

        t() {
            return this.$store.getters.getTranslations;
        },
    },

    methods: {
        async exportToPDF() {
            // this.isLoading = true
            if (!this.couponDisplay) {
                this.couponDisplay = true;
                return false;
            }
            if (this.coupon != "") {
                const response = await axios.post("/Coupon/check", {
                    coupon: this.coupon,
                    resume_id: this.ItemID,
                });

                let responses = response.data;

                if (responses.code == 200) {
                    const element = this.$refs.cvContent; // ضع ref="cvContent" على السيرة
                    const opt = {
                        margin: 0.5,
                        filename: "resume.pdf",
                        image: { type: "jpeg", quality: 0.98 },
                        html2canvas: { scale: 2 },
                        jsPDF: {
                            unit: "in",
                            format: "a4",
                            orientation: "portrait",
                        },
                    };
                    html2pdf().set(opt).from(element).save();
                    this.isLoading = false;
                } else if (responses.code == 201) {
                    this.swalFunction("info", responses.message);
                    this.isLoading = false;
                }
            }
        },

        // getSectionComponent(sectionKey) {
        //     const map = {
        //       personalInfo: "SectionPersonalInfo",
        //       summary: "SectionSummary",
        //       education: "SectionEducation",
        //       employmentHistory: "SectionEmployment",
        //       skillsList: "SectionSkills",
        //       languagesList: "SectionLanguages",
        //       additionalSections: "SectionAdditional",
        //     };
        //     return map[sectionKey] || null;
        //   },

        async checkText(section) {
            this.isLoadingSummary = true;
            const response = await axios.post("/check-text", {
                text: this.personalInfo[section],
            });
            this.result = response.data.corrected_text;

            this.personalInfo[section] = this.result;
            this.isLoadingSummary = false;
        },

        async rephraseText(section) {
            this.isLoadingSummary = true;
            const response = await axios.post("/rephrase-text", {
                text: this.personalInfo[section],
            });
            this.result = response.data.rephrased_text;
            this.personalInfo[section] = this.result;
            this.isLoadingSummary = false;
        },

        async AtsText() {
            this.isLoading = true;

            const plainTextResume = `
                Full Name: ${this.personalInfo.first_name} ${
                this.personalInfo.last_name
            }
                Job Title: ${this.personalInfo.job_title}
                Email: ${this.personalInfo.email}
                Mobile: ${this.personalInfo.mobile}
                Address: ${this.personalInfo.address}, ${
                this.personalInfo.city
            }, ${this.personalInfo.country}, ${this.personalInfo.postal_code}
                Driving License: ${this.personalInfo.driving_license}
                Nationality: ${this.personalInfo.nationality}
                Place of Birth: ${this.personalInfo.place_of_birth}
                Date of Birth: ${this.personalInfo.date_of_birth}

                Summary:
                ${this.personalInfo.summary}

                Employment History:
                ${this.personalInfo.employmentHistory
                    .map(
                        (job, i) => `
                ${i + 1}. Job Title: ${job.job_title}
                   Company: ${job.company_name}
                   Duration: ${job.duration}
                   Description: ${job.job_description}
                `
                    )
                    .join("")}

                Education:
                ${this.personalInfo.education
                    .map(
                        (edu, i) => `
                ${i + 1}. Degree: ${edu.degree}
                   School: ${edu.school}
                   Location: ${edu.field}
                   Period: ${edu.start_date} to ${edu.end_date}
                   Description: ${edu.descriptions}
                `
                    )
                    .join("")}

                Skills:
                ${
                    this.personalInfo.skillsList
                        .map((skill) => `- ${skill.name} (${skill.level})`)
                        .join("\n") || "- (لم يتم إدخال مهارات)"
                }

                Languages:
                ${
                    this.personalInfo.languagesList
                        .map((lang) => `- ${lang.name} (${lang.level})`)
                        .join("\n") || "- (لم يتم إدخال لغات)"
                }

                Social Links:
                ${
                    this.personalInfo.socialLinks
                        .map((link) => `- ${link.platform}: ${link.url}`)
                        .join("\n") || "- (لم يتم إدخال روابط)"
                }

                Additional Sections:
                ${
                    this.personalInfo.additionalSections
                        .map(
                            (sec) => `
                - ${sec.field}: From ${sec.start_date} to ${sec.end_date}
                  ${sec.description}
                `
                        )
                        .join("") || "- (لا توجد بيانات إضافية)"
                }
                `;

            const response = await axios.post("/check-ats", {
                text: plainTextResume,
            });

            // this.result = JSON.parse(response.data.corrected_text);
            this.score_ats = response.data.score;
            this.sections_ats = response.data.sections;
            this.isLoading = false;

            $("#kt_modal_scrollable_1").modal("show");
        },

        async Suggestion(section) {
            this.isLoadingSummary = true;
            if (this.personalInfo.job_title == "") {
                this.swalFunction("info", "Please fill job Title");
                return false;
            }

            const response = await axios.post("/check-suggestions", {
                text: this.personalInfo.job_title,
                type: section,
            });

            this.result = response.data.suggestions;
            if (section == "summary") {
                this.personalInfo[section] = this.result;
            } else if (section == "skillsList") {
                this.result.forEach((skill) => {
                    this.personalInfo.skillsList.push({
                        name: skill,
                        level: "",
                    });
                });
            }

            this.isLoadingSummary = false;
        },

        onSectionsOrderChanged(newOrder) {
            this.$nextTick(() => {
                this.selectedSections = [...newOrder]; // نسخ جديد لتفعيل التفاعل
                this.compileTemplate();
            });
        },

        toggleAccordion(index) {
            this.openAccordion = this.openAccordion === index ? null : index;
        },

        addSocial() {
            this.personalInfo.socialLinks.push({ platform: "", url: "" });
            this.activeSocialIndex = this.personalInfo.socialLinks.length - 1;
        },
        removeSocial(index) {
            this.personalInfo.socialLinks.splice(index, 1);
            if (
                this.activeSocialIndex >= this.personalInfo.socialLinks.length
            ) {
                this.activeSocialIndex =
                    this.personalInfo.socialLinks.length - 1;
            }
        },
        addLanguage() {
            this.personalInfo.languagesList.push({ name: "", level: "" });
            this.activeLanguageIndex =
                this.personalInfo.languagesList.length - 1;
        },
        removeLanguage(index) {
            this.personalInfo.languagesList.splice(index, 1);
            if (
                this.activeLanguageIndex >=
                this.personalInfo.languagesList.length
            ) {
                this.activeLanguageIndex =
                    this.personalInfo.languagesList.length - 1;
            }
        },
        getTransparentColor(color, opacity) {
            const hexToRgb = (hex) => {
                const bigint = parseInt(hex.replace("#", ""), 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `${r}, ${g}, ${b}`;
            };
            return `rgba(${hexToRgb(color)}, ${opacity})`;
        },
        onFileChange(e) {
            this.personalInfo.previewUrl = "";
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.personalInfo.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append("logo", file);

            axios
                .post("/Resume/upload-image", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then((response) => {
                    if (response.data.items) {
                        // console.log(response.data.items)
                        this.personalInfo.logo = response.data.items;
                        this.personalInfo.previewUrl = response.data.items;
                        // console.log(this.personalInfo.previewUrl)
                    }
                });
        },

        addAdditionalSection() {
            this.personalInfo.additionalSections.push({
                field: "",
                start_date: "",
                end_date: "",
                description: "",
            });
            this.activeAdditionalIndex =
                this.personalInfo.additionalSections.length - 1;
        },
        removeAdditionalSection(index) {
            this.personalInfo.additionalSections.splice(index, 1);
            if (this.activeAdditionalIndex >= index) {
                this.activeAdditionalIndex =
                    this.activeAdditionalIndex > 0
                        ? this.activeAdditionalIndex - 1
                        : 0;
            }
        },
        addSkill() {
            this.personalInfo.skillsList.push({
                name: "",
                level: "",
                percentage: "",
            });
            this.activeSkillIndex = this.personalInfo.skillsList.length - 1;
        },
        removeSkill(index) {
            this.personalInfo.skillsList.splice(index, 1);
            if (this.personalInfo.skillsList.length === 0) {
                this.addSkill();
            } else if (this.activeSkillIndex >= index) {
                this.activeSkillIndex = Math.max(0, this.activeSkillIndex - 1);
            }
        },
        toggleEducation(index) {
            this.activeEducation =
                this.activeEducation === index ? null : index;
        },
        addEducation() {
            this.personalInfo.education.push({
                school: "",
                degree: "",
                field: "",
                start_date: "",
                end_date: "",
                description: "",
            });
            this.activeEducation = this.personalInfo.education.length - 1;
        },
        removeEducation(index) {
            this.personalInfo.education.splice(index, 1);
            if (this.activeEducation === index) {
                this.activeEducation = null;
            }
        },
        addEmployment() {
            this.personalInfo.employmentHistory.push({
                companyName: "",
                jobTitle: "",
                duration: "",
                jobDescription: "",
            });
            this.activeIndex = this.personalInfo.employmentHistory.length - 1; // Open the new one
        },
        removeEmployment(index) {
            this.personalInfo.employmentHistory.splice(index, 1);
            if (this.activeIndex === index) {
                this.activeIndex = -1; // Collapse if deleted
            }
        },
        toggle(index) {
            this.activeIndex = this.activeIndex === index ? -1 : index;
        },
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.personalInfo.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append("logo", file);

            axios
                .post("/Resume/upload-image", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then((response) => {
                    if (response.data.items) {
                        // console.log(response.data.items);
                        this.personalInfo.logo = response.data.items;
                        this.personalInfo.previewUrl = response.data.items;
                    }
                });
        },

        addSection(section) {
            // Prevent adding the same section twice
            if (!this.selectedSections.find((s) => s.type === section.type)) {
                this.selectedSections.push(section);
            }
        },
        selectLevel(level) {
            this.selectedLevel = level;
        },
        getTransparentColor(hex, alpha) {
            // Convert hex to rgba
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        },
        startTypingEffect() {
            if (this.locale == "ar") {
                this.fullText =
                    "راجع وحدث تفاصيل سيرتك الذاتية للتأكد من أنها محدثة ودقيقة.";
            }
            this.typingInterval = setInterval(() => {
                if (this.typingIndex < this.fullText.length) {
                    this.displayedText += this.fullText[this.typingIndex];
                    this.typingIndex++;
                } else {
                    clearInterval(this.typingInterval);
                }
            }, 30); // سرعة الحروف
        },
        EditTemplate() {
            this.editTemplate = true;
        },

        cancelEditTemplate() {
            this.editTemplate = false;
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

        compileTemplate() {
            // console.log(this.selectedTemplate.content_html);

            this.dynamicComponent = {
                template: this.selectedTemplate.content_html,
                data: () => ({
                    personalInfo: this.personalInfo,
                    sectionTitles: this.sectionTitles,
                    selectedSections: this.selectedSections,
                    t: this.t,
                }),
            };

            this.applyTranslations();
        },

        async fetchItems() {
            this.isLoading = true;
            const typeList = ["category"];
            axios
                .get("/ItemsCategories/getAllItems", {
                    params: {
                        pagination: 0,
                        type: "category_template",
                        order: 1,
                    },
                })
                .then((response) => {
                    this.ItemsOverall = response.data.items;
                    this.items = this.ItemsOverall;
                    this.allTemplates = this.items.flatMap(
                        (item) => item.templates
                    );

                    const templateID = localStorage.getItem("tempID");

                    if (this.ItemID == "" && templateID == "") {
                        this.selectedTemplate = this.allTemplates[0];

                        let raw = this.selectedTemplate.selectedSections;

                        if (typeof raw === "string") {
                            this.selectedSections = JSON.parse(raw);
                        } else {
                            this.selectedSections = raw; // احتمال يكون JSON فعلاً
                        }
                        this.compileTemplate();
                    } else if (this.ItemID == "" && templateID != "") {
                        const found = this.allTemplates.find(
                            (t) => t.id == templateID
                        );
                        this.selectedTemplate = found || this.allTemplates[0];

                        let raw = this.selectedTemplate.selectedSections;

                        if (typeof raw === "string") {
                            this.selectedSections = JSON.parse(raw);
                        } else {
                            this.selectedSections = raw; // احتمال يكون JSON فعلاً
                        }
                        this.compileTemplate();
                    }

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction(
                        "error",
                        "Error happens, Please Try Again"
                    );
                    this.isLoading = true;
                });
        },

        async getItemById(ID) {
            let templateByID = this.allTemplates.find((t) => t.id === ID);
            this.selectedTemplate = templateByID;
            this.cvCssFromApi = this.selectedTemplate.content_css;

            let raw = this.selectedTemplate.selectedSections;

            if (typeof raw === "string") {
                this.selectedSections = JSON.parse(raw);
            } else {
                this.selectedSections = raw; // احتمال يكون JSON فعلاً
            }

            // console.log(this.selectedTemplate);

            this.compileTemplate();
        },

        async combaindTemplate(ID) {
            this.isLoading = true;
            axios
                .get("/Resume/getById", {
                    params: {
                        ID: ID,
                    },
                })
                .then((response) => {
                    this.itemsResume = response.data.items;
                    this.personalInfo = JSON.parse(
                        response.data.items.content_data || []
                    );

                    let temp = this.selectedSections;

                    this.selectedSections = JSON.parse(
                        response.data.items.selectedSections || []
                    );

                    // this.selectedSections = JSON.parse(
                    //     [...response.data.items.selectedSections] || temp
                    // );

                    this.selectedTemplate = this.itemsResume.template;

                    this.compileTemplate();

                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction(
                        "error",
                        "Error happens, Please Try Again"
                    );
                    this.isLoading = true;
                });
        },

        addEditItem() {
            this.isLoading = true;

            console.log(this.personalInfo);

            const config = {
                headers: {
                    "content-type": "multipart/form-data",
                },
            };

            const cvElement = this.$refs.cvContent;

            html2canvas(cvElement)
                .then((canvas) => {
                    this.thumbnailDataUrl = canvas.toDataURL("image/jpeg");

                    let form = new FormData();
                    form.append("jsonData", JSON.stringify(this.personalInfo));
                    form.append(
                        "selectedSections",
                        JSON.stringify(this.selectedSections)
                    );
                    form.append("logo", this.personalInfo.logo);
                    form.append("resume_template_id", this.selectedTemplate.id);
                    form.append("logo_cv", this.thumbnailDataUrl);

                    if (this.ItemID != "") {
                        form.append("ItemID", this.ItemID);
                    }

                    axios
                        .post("Resume/createItem", form, config)
                        .then((response) => {
                            this.isLoading = false;
                            if (response.data.code == 200) {
                                this.swalFunction(
                                    "success",
                                    response.data.message
                                );
                                this.personalInfo = JSON.parse(
                                    response.data.items.content_data
                                );
                                this.compileTemplate();
                                this.ItemID = response.data.items.id;
                            } else {
                                this.swalFunction("error", "Errors happen");
                            }
                        })
                        .catch((error) => {
                            this.isLoading = false;
                            this.swalFunction("error", "Errors happen");
                        });
                })
                .catch((err) => {
                    this.isLoading = false;
                    this.swalFunction("error", "Thumbnail generation failed");
                });
        },

        applyTranslations() {
            this.$nextTick(() => {
                const labels =
                    this.$refs.cvContent?.querySelectorAll("[data-key]");
                labels?.forEach((el) => {
                    const key = el.getAttribute("data-key");
                    if (this.t[key]) {
                        el.textContent = this.t[key];
                    }
                });
            });
        },
    },

    watch: {
        "personalInfo.skillsList": {
            handler(newList) {
                const levelMap = {
                    Novice: "20%",
                    Beginner: "40%",
                    Skillful: "60%",
                    Experienced: "80%",
                    Expert: "100%",
                };

                newList.forEach((skill) => {
                    skill.percentage = levelMap[skill.level] || "0%";
                });
            },
            deep: true, // لمراقبة التغيير داخل العناصر
            immediate: true, // لتطبيقه أول مرة مباشرة بعد mounted
        },

        "personalInfo.languagesList": {
            handler(newList) {
                const levelMap = {
                    Novice: "20%",
                    Beginner: "40%",
                    Skillful: "60%",
                    Experienced: "80%",
                    Expert: "100%",
                };

                newList.forEach((skill) => {
                    skill.percentage = levelMap[skill.level] || "0%";
                });
            },
            deep: true, // لمراقبة التغيير داخل العناصر
            immediate: true, // لتطبيقه أول مرة مباشرة بعد mounted
        },

        personalInfo: {
            handler() {
                this.applyTranslations();
            },
            deep: true,
        },
    },
};
</script>

<style scoped>
.level-slider {
    background-color: #e8f5e9;
    padding: 10px;
    border-radius: 10px;
    gap: 5px;
    height: 45px;
}

.level-step {
    flex: 1;
    height: 100%;
    border-left: 1px solid #28a74533;
    border-right: 1px solid #28a74533;
    background-color: #ffffff6b;
    transition: background-color 0.3s ease;
    cursor: pointer;
    border-radius: 6px;
}

.level-step.active {
    /* background color handled dynamically in :style */
}
.app-content {
    padding-top: 0px !important;
}

i.bi,
i[class^="fonticon-"],
i[class*=" fonticon-"],
i[class^="fa-"],
i[class*=" fa-"],
i[class^="la-"],
i[class*=" la-"] {
    font-size: 1.5rem;
    color: #ced0d7;
}

.custom-body {
    display: flex;
    margin: auto;
}

.w-50px {
    width: 260px !important;
}

.background-custom {
    margin-top: 18px;
}

.ms-n1 {
    margin-left: -3rem !important;
}

.border-custom {
    border: 34px solid #eff2f9;
}

.rela-block {
    display: block;
    position: relative;
    margin: auto;
}
.rela-inline {
    display: inline-block;
    position: relative;
    margin: auto;
}
.floated {
    display: inline-block;
    float: left;
}
.abs-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, 0%);
    text-align: left;
    width: 88%;
}

.caps {
    text-transform: uppercase;
}
.page .justified {
    text-align: justify;
}
.page p.light {
    color: #777;
}
.page h2 {
    font-size: 30px;
    letter-spacing: 5px;
    font-weight: 600;
    color: #000;
}
.page h3 {
    font-size: 21px;
    font-weight: 600;
    color: #000;
}
.page {
    width: 100%;
    height: 100%;
    max-width: 1200px;
    margin: 0px auto;
    background-color: #fff;
    direction: ltr;
    text-align: left;
    zoom: 0.9;
    border-radius: 8px;
    box-shadow: 0px 3px 10px 0px rgb(0 0 0 / 12%);
    border: 1px solid #f1f1f4;
    overflow: hidden;
}
.top-bar {
    height: 220px;
    background-color: #848484;
    color: #fff;
}
.name {
    position: absolute;
    left: 44%;
    height: 220px;
    text-align: center;

    font-size: 30px;
    letter-spacing: 8px;
    width: 52%;
    font-weight: bold;
}
.name div {
    width: 100%;
}
.side-bar {
    position: absolute;
    top: 60px;
    left: 5%;
    width: 34%;
    background-color: #f7e0c1;
    padding: 320px 30px 50px;
    height: 96.1%;
}
.mugshot {
    position: absolute;
    top: 50px;
    left: 0%;
    height: auto;
    min-width: 100%;
    margin: 0 auto;
}
.mugshot .logo {
    margin: 0;
    width: 100%;
    position: relative;
    text-align: center;
}
.logo {
    position: absolute;
    height: 250px;
    width: 250px;
}
.logo .logo-svg {
    height: 100%;
    width: 100%;
    stroke: #000;
}
.logo .logo-text {
    position: absolute;
    top: 58%;
    right: 16%;

    font-size: 55px;
}
.social {
    padding-left: 40px;
    margin-bottom: 20px;
}
.social:before {
    content: "";
    position: absolute;
    top: 0px;
    left: 0px;
    height: 26px;
    width: 26px;
    background-size: cover !important;
}
.social.twitter:before {
    background: url("/assets/media/tweter.webp") center no-repeat;
}
.social.pinterest:before {
    background: url("/assets/media/p.webp") center no-repeat;
}
.social.linked-in:before {
    background: url("/assets/media/linkedIn.webp") center no-repeat;
}
.side-header {
    font-size: 18px;
    letter-spacing: 4px;
    font-weight: 600;
    margin: 60px auto 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #888;
}
.list-thing {
    padding-left: 25px;
    margin-bottom: 10px;
}
.content-container {
    margin-left: 38%;
    padding: 50px;
}
.content-container p {
    line-height: 20px;
}
.separator {
    height: 2px;
    width: 100%;
    background-color: #999;
    margin: 20px 0;
}
.long-margin {
    margin-bottom: 40px;
}
img.avatar {
    width: 75%;
    border-radius: 8px;
    margin: 0 auto;
    text-align: center;
}
.section-wrapper .skill-percentage {
    margin-bottom: 15px;
    position: relative;
}
.section-wrapper .skill-percentage::after {
    content: "";
    width: 100%;
    height: 6px;
    background: #4a4e51;
    display: block;
    margin-top: 8px;
}
.section-wrapper .skill-percentage::before {
    content: "";
    height: 6px;
    background: #ff9608;
    position: absolute;
    margin-top: 8px;
    bottom: 0;
}
.section-wrapper .skill-percentage:nth-child(1)::before {
    width: 80%;
    -webkit-animation: skill_1 0.6s ease;
    animation: skill_1 0.6s ease;
}
.section-wrapper .skill-percentage:nth-child(2)::before {
    width: 90%;
    -webkit-animation: skill_2 0.6s ease;
    animation: skill_2 0.6s ease;
}
.section-wrapper .skill-percentage:nth-child(3)::before {
    width: 50%;
    -webkit-animation: skill_3 0.6s ease;
    animation: skill_3 0.6s ease;
}
.section-wrapper .skill-percentage:nth-child(4)::before {
    width: 60%;
    -webkit-animation: skill_4 0.6s ease;
    animation: skill_4 0.6s ease;
}
.section-wrapper .skill-percentage:nth-child(5)::before {
    width: 70%;
    -webkit-animation: skill_5 0.6s ease;
    animation: skill_5 0.6s ease;
}
.section-wrapper .skill-percentage:nth-child(6)::before {
    width: 70%;
    -webkit-animation: skill_6 0.6s ease;
    animation: skill_6 0.6s ease;
}
.section-wrapper .skill-percentage:nth-child(7)::before {
    width: 70%;
    -webkit-animation: skill_6 0.6s ease;
    animation: skill_6 0.6s ease;
}
.section-wrapper ul {
    list-style: none;
    padding-left: 0;
}

.thumbnail {
    width: 150px;
    height: auto;
    border: 1px solid #ccc;
    margin-top: 10px;
}

.dropdown-item {
    cursor: pointer;
}

.btn-check:checked + .btn.btn-light-warning,
.btn-check:active + .btn.btn-light-warning,
.btn.btn-light-warning:focus:not(.btn-active),
.btn.btn-light-warning:hover:not(.btn-active),
.btn.btn-light-warning:active:not(.btn-active),
.btn.btn-light-warning.active,
.btn.btn-light-warning.show,
.show > .btn.btn-light-warning {
    color: var(--bs-warning-inverse-light);
    border-color: var(--bs-warning-light);
    background-color: var(--bs-warning-light) !important;
}

.float-right {
    float: right;
}

.float-left {
    float: left;
}
</style>
