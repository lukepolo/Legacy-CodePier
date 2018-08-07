<template>
    <nav v-if="shouldShowPagination">
        <div class="pagination">
            <div class="pagination--item pagination--item-arrow" :class="{ disabled: pagination.current_page === 1 }" @click="pageClicked( pagination.current_page - 1 )">
              <span class="icon-arrow-left"></span>
            </div>

            <div v-if="hasFirst" class="pagination--item" :class="{ active: isActive(1) }" @click="pageClicked(1)">
                1
            </div>

            <div v-if="hasFirstEllipsis" class="pagination--item pagination--item-ellipsis">
              ...
            </div>

            <div class="pagination--item" :class="{ active: isActive(page), disabled: page === '...' }" v-for="page in pages" :key="page" @click="pageClicked(page)">
                {{ page }}
            </div>

            <div v-if="hasLastEllipsis" class="pagination--item pagination--item-ellipsis">
              ...
            </div>

            <div v-if="hasLast" class="pagination--item" :class="{ active: isActive(pagination.last_page) }" @click="pageClicked(pagination.last_page)">
              {{pagination.last_page}}
            </div>

            <div class="pagination--item pagination--item-arrow" :class="{ disabled: pagination.current_page === pagination.last_page }" @click="pageClicked( pagination.current_page + 1 )">
              <span class="icon-arrow-right"></span>
            </div>
        </div>
    </nav>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  props: {
    pagination: {
      type: Object,
      default: () => ({}),
    },
    dispatch: {
      type: String,
      required: true,
    },
    parameters: {
      type: Object,
      default: () => ({}),
    },
  },

  computed: {
    pages() {
      return this.pagination.last_page === undefined ? [] : this.pageLinks();
    },

    hasFirst() {
      return (
        this.pagination.current_page >= 4 || this.pagination.last_page < 10
      );
    },

    hasLast() {
      return (
        this.pagination.current_page <= this.pagination.last_page - 3 ||
        this.pagination.last_page < 10
      );
    },

    hasFirstEllipsis() {
      return (
        this.pagination.current_page > 4 && this.pagination.last_page >= 10
      );
    },

    hasLastEllipsis() {
      return (
        this.pagination.current_page < this.pagination.last_page - 3 &&
        this.pagination.last_page >= 10
      );
    },

    shouldShowPagination() {
      if (this.pagination.last_page === undefined) {
        return false;
      }

      if (this.pagination.total === 0) {
        return false;
      }

      return this.pagination.last_page > 1;
    },
  },

  methods: {
    isActive(page) {
      const current_page = this.pagination.current_page || 1;

      return current_page === page;
    },

    pageClicked(page) {
      if (
        page === "..." ||
        page === this.pagination.current_page ||
        page > this.pagination.last_page ||
        page < 1
      ) {
        return;
      }
      this.parameters.page = page;
      this.$store.dispatch(this.dispatch, this.parameters);
    },

    pageLinks() {
      const pages = [];

      let left = 2;
      let right = this.pagination.last_page - 1;

      if (this.pagination.last_page >= 10) {
        left = Math.max(1, this.pagination.current_page - 2);
        right = Math.min(
          this.pagination.current_page + 2,
          this.pagination.last_page,
        );
      }

      for (let i = left; i <= right; i++) {
        pages.push(i);
      }

      return pages;
    },
  },
});
</script>
