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

            <router-link
                :to="{ name: 'CreateTemplate', query: { id: mycv.id } }"
                class="col-md-3 mt-0"
                v-for="(mycv, index) in itemsMyCv"
                :key="mycv.id"
            >
                <div class="createCvImage">
                    <img :src="mycv.logo_cv" class="ccv" alt="ccv" />
                    <div class="Captions">
                        <p class="mb-2">
                            {{ t.myCv }}
                        </p>
                        <span>
                            {{ t.LastModified }} {{ mycv.created_at }}
                        </span>
                    </div>
                </div>
            </router-link>

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

                    <li
                        class="nav-item"
                        role="presentation"
                        v-for="(item, index) in items"
                        :key="item.id"
                    >
                        <button
                            class="nav-link"
                            :id="`${item.id}-tab`"
                            data-bs-toggle="tab"
                            :data-bs-target="`#tab-${item.id}`"
                            type="button"
                            role="tab"
                            :aria-controls="`tab-${item.id}`"
                            aria-selected="false"
                        >
                            {{ item.name }}
                        </button>
                    </li>
                </ul>

                <!-- Tabs content -->
                <div class="tab-content pt-3" id="myTabContent">
                    <!-- All Templates Tab -->
                    <div
                        class="tab-pane fade show active"
                        id="AllTemplates"
                        role="tabpanel"
                        aria-labelledby="AllTemplates-tab"
                    >
                        <div class="row mt-10">
                            <div
                                class="col-md-3 mt-0 mb-10"
                                v-for="(temp, index) in allTemplates"
                                :key="id"
                            >
                                <div
                                    class="createCvImageTap"
                                    @click="openTemplate(temp.id)"
                                >
                                    <img
                                        :src="temp.logo"
                                        class="ccvTap"
                                        alt="ccv"
                                    />

                                    <div class="CaptionsTab">
                                        <p>{{ temp.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Items Tabs -->
                    <div
                        class="tab-pane fade"
                        v-for="(item, index) in items"
                        :id="`tab-${item.id}`"
                        role="tabpanel"
                        :aria-labelledby="`${item.id}-tab`"
                        :key="`tab-${item.id}`"
                    >
                        <div class="row mt-10">
                            <div
                                class="col-md-3 mt-0 mb-10"
                                v-for="(template, i) in item.templates"
                                :key="i"
                            >
                                <div
                                    class="createCvImageTap"
                                    @click="openTemplate(template.id)"
                                >
                                    <img
                                        :src="template.logo"
                                        class="ccvTap"
                                        alt="ccv"
                                    />

                                    <div class="CaptionsTab">
                                        <p>{{ template.description }}</p>
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
            items: [],
            allTemplates: [],
            itemsMyCv: [],
        };
    },

    mounted() {
        this.$store.dispatch("fetchSetting").then(() => {
            this.fetchItems();
            this.fetchItemsMyCV();
        });
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
        openTemplate(ID) {
            localStorage.setItem("tempID", ID);
            this.$router.push({ path: `/${this.locale}/app/create-template` });
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

                    // this.items = this.ItemsOverall.filter(item => item.type && item.type.name === "category_template");
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

        async fetchItemsMyCV() {
            this.isLoading = true;
            axios
                .get("/Resume/getAllItems", {
                    params: {},
                })
                .then((response) => {
                    this.itemsMyCv = response.data.items;
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
    },
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
