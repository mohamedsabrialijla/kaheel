<template>
    <div>
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
            <div class="col-md-12 alignCenter px-5 mb-5">
                <p class="Paragr moveColor mt-0">
                    {{ t.PreviousModels }}
                </p>
            </div>
            <!--begin::Col-->
            <div class="col-md-3 mt-0">
                <router-link :to="`/${locale}/app/create-template`">
                    <div class="createCv">
                        <div class="PlusCv">
                            <div class="PlusIcon">
                                <img
                                    src="/assets/media/plus.png"
                                    class="plusImg2"
                                    alt="plusImg2"
                                />
                            </div>
                            <div class="WorldCvAdd">
                                {{ t.CreateCV }}
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>
            <div class="col-md-3 mt-0">
                <div class="createCvImage">
                    <img src="/assets/media/ccv1.png" class="ccv" alt="ccv" />
                    <div class="Captions">
                        <p class="mb-2">
                            {{ t.myCv }}
                        </p>
                        <span>
                            {{ t.LastModified }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-0">
                <div class="createCvImage">
                    <img src="/assets/media/ccv2.png" class="ccv" alt="ccv" />
                    <div class="Captions">
                        <p class="mb-2">
                            {{ t.myCv }}
                        </p>
                        <span>
                            {{ t.LastModified }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-0">
                <div class="createCvImage">
                    <img src="/assets/media/ccv3.png" class="ccv" alt="ccv" />
                    <div class="Captions">
                        <p class="mb-2">
                            {{ t.myCv }}
                        </p>
                        <span>
                            {{ t.LastModified }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-12 alignCenter px-5 mt-15 mb-0">
                <p class="Paragr moveColor mt-0">
                    {{ t.CVtemplates }}
                </p>
            </div>

            <div class="col-md-12 mt-0 mb-5 TabTemplates">
                <!-- Tabs navigation -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="AllTemplates-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#AllTemplates"
                            type="button"
                            role="tab"
                            aria-controls="AllTemplates"
                            aria-selected="true"
                        >
                            {{ t.AllTemplates }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="classic-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#classic"
                            type="button"
                            role="tab"
                            aria-controls="classic"
                            aria-selected="false"
                        >
                            {{ t.classic }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="professional-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#professional"
                            type="button"
                            role="tab"
                            aria-controls="professional"
                            aria-selected="false"
                        >
                            {{ t.professional }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="creative-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#creative"
                            type="button"
                            role="tab"
                            aria-controls="creative"
                            aria-selected="false"
                        >
                            {{ t.creative }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="basic-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#basic"
                            type="button"
                            role="tab"
                            aria-controls="basic"
                            aria-selected="false"
                        >
                            {{ t.basic }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="WithoutPicture-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#WithoutPicture"
                            type="button"
                            role="tab"
                            aria-controls="WithoutPicture"
                            aria-selected="false"
                        >
                            {{ t.WithoutPicture }}
                        </button>
                    </li>
                </ul>

                <!-- Tabs content -->
                <div class="tab-content pt-3" id="myTabContent">
                    <div
                        class="tab-pane fade show active"
                        id="AllTemplates"
                        role="tabpanel"
                        aria-labelledby="AllTemplates-tab"
                    >
                        <div class="row mt-10">
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv1.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv2.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv3.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv4.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv5.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv6.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv7.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv8.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv9.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv10.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv11.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv12.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv13.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv14.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv15.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv16.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="classic"
                        role="tabpanel"
                        aria-labelledby="classic-tab"
                    >
                        <div class="row mt-10">
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv11.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv12.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv13.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv14.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv15.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv16.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="professional"
                        role="tabpanel"
                        aria-labelledby="professional-tab"
                    >
                        <div class="row mt-10">
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv1.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv2.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv3.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv4.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv5.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv6.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv7.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="creative"
                        role="tabpanel"
                        aria-labelledby="creative-tab"
                    >
                        <div class="row mt-10">
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv1.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv2.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv3.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv4.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv5.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv15.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv16.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="basic"
                        role="tabpanel"
                        aria-labelledby="basic-tab"
                    >
                        <div class="row mt-10">
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv1.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv2.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv3.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv8.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv9.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv10.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv11.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv12.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv13.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="WithoutPicture"
                        role="tabpanel"
                        aria-labelledby="WithoutPicture-tab"
                    >
                        <div class="row mt-10">
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv1.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv11.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv12.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv13.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv14.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv15.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-0 mb-10">
                                <div class="createCvImageTap">
                                    <img
                                        src="/assets/media/ccvv16.png"
                                        class="ccvTap"
                                        alt="ccv"
                                    />
                                    <div class="CaptionsTab">
                                        <p>
                                            {{ t.AfreetwoColumn }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Col-->
        </div>
    </div>
</template>
<script>
import axios from "axios";
import { mapActions, mapState } from "vuex";

export default {
    components: {},
    data() {
        return {
            // t: window.translations,
            languages: {},
            isLoading: false,
            socialMediaIcons: [],
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {});
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

    methods: {},
};
</script>

<style>
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
</style>
