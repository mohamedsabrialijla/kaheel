<template>
    <div class="slider-wrapper position-relative">
        <div
            class="up_slider_container tns-slider rounded-4"
            ref="slider"
            v-if="slides"
        >
            <div
                v-for="(slide, idx) in slides"
                :key="idx"
                class="slider-slide position-relative overflow-hidden"
            >
                <img
                    :src="slide.logo"
                    class="slider-bg-img"
                    alt="Banner"
                    @error="onImgError($event)"
                />
            </div>
        </div>
    </div>
</template>

<script>
import "tiny-slider/dist/tiny-slider.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import { tns } from "tiny-slider/src/tiny-slider";

import sliderBg1 from "@/assets/slider-bg-1.png";
import sliderBg2 from "@/assets/slider-bg-2.png";
import sliderModel1 from "@/assets/slider-model-1.png";

export default {
    name: "BannerSlider",
    data() {
        return {
            slides: [],
            slider: null,
        };
    },
    mounted() {
       
            this.fetchItems(); 
       
    },
    methods: {
        initSlider() {
            if (this.slider) return;
            this.slider = tns({
                container: this.$refs.slider,
                items: 1,
                slideBy: 1,
                controls: false,
                nav: true,
                navContainer: document.getElementById("custom-dots"),
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayButtonOutput: false,
                mouseDrag: true,
                speed: 600,
            });
        },
        onImgError(e) {
            // fallback for missing images
            e.target.src =
                "https://via.placeholder.com/900x320?text=Banner+Image";
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
            await axios.get('basic/getBanner')
                .then(response => {
                    this.slides = response.data.items;
                    this.isLoading = false;
                     this.$nextTick(() => {
                        this.initSlider();
                    });
                })
                .catch(error => {
                   console.log(error)
                   this.swalFunction('error', 'Error Happens, Please try again')
                   this.isLoading = false;
                });
        },  


    },
};
</script>

<style scoped>
.slider-wrapper {
    position: relative;
    border-radius: 1rem;
    overflow: visible;
    display: flex;
    flex-direction: column;
}
.tns-slider {
    border-radius: 1rem;
    overflow: hidden;
    background: transparent;
}
.slider-slide {
    min-height: 380px;
    background: transparent;
    border-radius: 1rem;
    position: relative;
}
.slider-bg-img {
    width: 100%;
    height: 380px;
    object-fit: cover;
    border-radius: 1rem;
    display: block;
}
.slider-content {
    top: 0;
    left: 0;
    height: 100%;
    z-index: 2;
    pointer-events: none;
}
.slider-model-img {
    height: 260px;
    width: auto;
    object-fit: contain;
    border-radius: 1rem;
    margin-right: 0;
    margin-left: 0;
    pointer-events: none;
}
.slider-title {
    color: #23243a;
    font-size: 2.2rem;
    letter-spacing: 0.5px;
}
.slider-subtitle {
    color: #bcbcc7;
    font-size: 1.1rem;
    font-weight: 600;
}
.slider-link {
    color: #23243a;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.slider-social {
    pointer-events: auto;
}
.slider-social-icon {
    color: #ff5c3a;
    background: #fff;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 8px;
    transition: background 0.2s, color 0.2s;
}
.slider-social-icon:hover {
    background: #ff5c3a;
    color: #fff;
}
.slider-dots-holder {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 18px;
}
.tns-nav {
    display: flex !important;
    justify-content: center;
    gap: 8px;
    margin: 0;
    position: relative;
    z-index: 10;
}

.tns-nav button {
    width: 18px;
    height: 6px;
    border-radius: 4px;
    background: #e5aa2a;
    opacity: 0.3;
    border: none;
    margin: 0 2px;
    transition: opacity 0.2s;
}
.tns-nav .tns-nav-active {
    opacity: 1;
}
.tns-controls {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    width: 100%;
    pointer-events: none;
    z-index: 10;
}
.tns-controls button {
    pointer-events: all;
    background: #fff;
    border: none;
    border-radius: 50%;
    width: 54px;
    height: 54px;
    box-shadow: 0 4px 16px rgba(80, 80, 80, 0.13);
    color: #e5aa2a;
    font-size: 1.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    margin: 0 12px;
}
.tns-controls button:hover {
    background: #e5aa2a;
    color: #fff;
    box-shadow: 0 6px 24px rgba(80, 80, 80, 0.18);
}
@media (max-width: 900px) {
    .slider-model-img {
        height: 180px;
    }
    .slider-bg-img {
        height: 200px;
    }
    .slider-slide {
        min-height: 200px;
    }
}
</style>
