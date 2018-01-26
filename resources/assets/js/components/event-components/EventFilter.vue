<template>
    <li class="filter--item dropdown" ref="filter" :class="{ open : isShowing }">
        <a href="#" role="button" class="dropdown-toggle" @click="toggle">
            <strong>{{ title }}:</strong>
            <span class="filter--item-selection">
                {{ filtersSelectionText }}
            </span>
            <span class="icon-arrow-up"></span>
        </a>

        <ul class="dropdown-menu dropup" ref="dropdown">
            <div class="dropup-form">
                <template v-if="filters.length">
                    <div class="flyform--group-checkbox select-all" @click.prevent="selectAllFilters" v-if="form.filters.length !== filters.length">
                        <label>
                            <input type="checkbox">
                            <span class="icon"></span>
                            Select All
                        </label>
                    </div>
                    <div class="flyform--group-checkbox select-all" @click="deselectAllFilters" v-if="allFiltersSelected">
                        <label>
                            <input type="checkbox" checked>
                            <span class="icon"></span>
                            Deselect All
                        </label>
                    </div>

                    <div class="dropup-scroll">
                        <div class="flyform--group-checkbox" v-for="filter in filters">
                            <label>
                                <input type="checkbox" v-model="form.filters" :value="filter">
                                <span class="icon"></span>
                                {{ filter.name }}
                            </label>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="no-items">
                        No {{ title }}
                    </div>
                </template>

                <div class="btn-footer">
                    <span class="btn btn-small" @click="cancel">Cancel</span>
                    <button class="btn btn-small btn-primary" @click="updateFilters" v-if="filters.length">Apply</button>
                </div>
            </div>
        </ul>
    </li>
</template>

<script>
export default {
  props: {
    title: {
      required: true
    },
    filters: {
      required: true
    },
    selectedFilters: {
      default: () => []
    }
  },
  data() {
    return {
      form: this.createForm({
        filters: this.selectedFilters
      }),
      isShowing: false
    };
  },
  created() {
    app.$on("closeFilters", () => {
      this.cancel();
    });
  },
  methods: {
    open() {
      app.$emit("closeFilters");
      this.$nextTick(() => {
        this.$refs.dropdown.style.left =
          this.$refs.filter.getBoundingClientRect().x + "px";
        Vue.set(this, "isShowing", true);
      });
    },
    close() {
      Vue.set(this, "isShowing", false);
    },
    cancel() {
      this.form.reset();
      this.close();
    },
    toggle() {
      if (!this.isShowing) {
        return this.open();
      }
      this.cancel();
    },
    selectAllFilters() {
      Vue.set(this.form, "filters", this.filters);
    },
    deselectAllFilters() {
      Vue.set(this.form, "filters", []);
    },
    updateFilters() {
      this.$emit("update:selectedFilters", _.map(this.form.filters, "id"));
      this.form.setOriginalData();
      this.close();
    }
  },
  computed: {
    allFiltersSelected() {
      return this.form.filters.length === this.filters.length;
    },
    filtersSelectionText() {
      if (this.allFiltersSelected || this.form.filters.length === 0) {
        return "All";
      }
      return _.map(this.form.filters, "name").join(", ");
    }
  }
};
</script>
