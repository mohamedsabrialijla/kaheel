<template>
  <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
    <div class="dt-paging paging_simple_numbers">
      <nav aria-label="Pagination">
        <ul class="pagination">
          <!-- Previous Button -->
          <li :class="['page-item', { disabled: currentPage === 1 }]">
            <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" aria-label="Previous">
              <i class="previous"></i>
            </button>
          </li>
          
          <!-- Page Numbers -->
          <li v-for="page in totalPages" :key="page" :class="['page-item', { active: currentPage === page }]">
            <button class="page-link" @click="goToPage(page)">
              {{ page }}
            </button>
          </li>

          <!-- Next Button -->
          <li :class="['page-item', { disabled: currentPage === totalPages }]">
            <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" aria-label="Next">
              <i class="next"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    totalItems: {
      type: Number,
      required: true
    },
    currentPage: {
      type: Number,
      default: 1
    },
    itemsPerPage: {
      type: Number,
      default: 10
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    }
  },
  methods: {
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.$emit('page-changed', page);  // إرسال الحدث للصفحة الرئيسية لتحديث الصفحة
      }
    }
  }
};
</script>
