<template>
    <div>
        <div class="TitleHead">
            <div class="container">
                <div class="text-center mb-10">
                    <h3
                        class="fs-3hx text-gray-900 mb-5"
                        id="how-it-works"
                        data-kt-scroll-offset="{default: 100, lg: 150}"
                    >
                        {{ title }}
                    </h3>
                    <p
                        style="
                            color: #aab0bf;
                            font-size: 20px;
                            margin-top: 2rem;
                        "
                    >
                        {{ subtitle }}
                    </p>
                </div>
            </div>
            <img src="/assets/media/bkHead.png" class="bkHead" alt="bkHead" />
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { mapActions, mapState } from "vuex";
import { api } from "../../../axiosUser";

export default {

      props: {
         title: {
          type: String,
          default: "Default Title" // قيمة افتراضية
        },
        subtitle: {
          type: String,
          default: "Default Subtitle"
        }
      },

    name: "TitleHead",
    components: {},
    data() {
        return {
            success: 0,
            translations: window.translations,
            items: [],
        };
    },
    mounted() {},

    computed: {},

    methods: {},
};
</script>
