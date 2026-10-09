<template>
  <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
    <div class="page-title d-flex flex-column me-3">
      <h1 class="d-flex text-gray-900 fw-bold my-1 fs-3">{{ title }}</h1>
      <ul class="breadcrumb breadcrumb-dot fw-semibold text-gray-600 fs-7 my-1">
        <li v-for="(breadcrumb, index) in breadcrumbs" :key="index" class="breadcrumb-item">
          <a v-if="breadcrumb.link" :href="breadcrumb.link" class="text-gray-600 text-hover-primary">{{ breadcrumb.text }}</a>
          <span v-else class="text-gray-500">{{ breadcrumb.text }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      breadcrumbs: []
    };
  },
  computed: {
    title() {
      return this.$route.meta.breadcrumb;
    }
  },
  mounted() {
    // استدعاء breadcrumbs بناءً على route
    this.setBreadcrumbs();
  },
  methods: {
    setBreadcrumbs() {
      this.breadcrumbs = [];
      let matched = this.$route.matched;

      matched.forEach(route => {
        if (route.meta && route.meta.breadcrumb) {
          this.breadcrumbs.push({
            text: route.meta.breadcrumb,
            link: route.path
          });
        }
      });
    }
  }
}
</script>
