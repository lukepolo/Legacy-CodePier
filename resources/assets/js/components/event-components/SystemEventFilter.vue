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
                <div
                    class="flyform--group-checkbox select-all"
                    @click.prevent="selectAllFilters"
                    v-if="
                        form.filters.types.commands.length !== filters.commands.length ||
                        form.filters.types.site_deployments.length !== filters.site_deployments.length
                    ">
                    <label>
                        <input type="checkbox">
                        <span class="icon"></span>
                        Select All
                    </label>
                </div>
                <div
                    class="flyform--group-checkbox select-all"
                    @click="deselectAllFilters"
                    v-if="allFiltersSelected">
                    <label>
                        <input type="checkbox" checked>
                        <span class="icon"></span>
                        Deselect All
                    </label>
                </div>

                <div class="dropup-scroll">
                    <template v-for="(types, area) in filters">
                        <div class="flyform--group-checkbox" v-for="type in types">
                            <label>
                                <template v-if="area === 'site_deployments'">
                                    <input type="checkbox" v-model="form.filters.types.site_deployments" :value="type">
                                </template>
                                <template v-else-if="area === 'commands'">
                                    <input type="checkbox" v-model="form.filters.types.commands" :value="type">
                                </template>
                                <span class="icon"></span>
                                {{ renderType(type) }}
                            </label>
                        </div>
                    </template>
                </div>

                <div class="btn-footer">
                    <a class="btn btn-small" @click="cancel">Cancel</a>
                    <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
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
        filters: {
          types: {
            commands: [],
            site_deployments: []
          }
        }
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
      Vue.set(this.form.filters.types, "commands", this.filters.commands);
      Vue.set(
        this.form.filters.types,
        "site_deployments",
        this.filters.site_deployments
      );
    },
    deselectAllFilters() {
      Vue.set(this.form.filters.types, "commands", []);
      Vue.set(this.form.filters.types, "site_deployments", []);
    },
    updateFilters() {
      this.$emit("update:selectedFilters", this.form.filters.types);
      this.form.setOriginalData();
      this.close();
    },
    renderType(type) {
      const title = type.substring(type.lastIndexOf("\\") + 1);

      return (
        title.replace(/([A-Z])/g, " $1").replace(/^./, function(type) {
          return type.toUpperCase();
        }) + "s"
      );
    }
  },
  computed: {
    allFiltersSelected() {
      return (
        this.form.filters.types.commands.length ===
          this.filters.commands.length &&
        this.form.filters.types.site_deployments.length ===
          this.filters.site_deployments.length
      );
    },
    filtersSelectionText() {
      if (this.allFiltersSelected) {
        return "All";
      }

      let types = this.form.filters.types;

      let commands = _.map(types.commands, command => {
        return this.renderType(command);
      });

      let site_deployments = _.map(types.site_deployments, event => {
        return this.renderType(event);
      });

      if (!commands.length && !site_deployments.length) {
        return "All";
      }

      return site_deployments.concat(commands).join(', ');
    }
  }
};
</script>
