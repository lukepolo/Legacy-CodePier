<template>
  <section>
    <div class="heading flex flex--center">
      <h3 class="flex--grow">{{ database }} Schemas</h3>

      <tooltip message="Add Schema">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': this.shouldShowForm }"
          @click="showForm = true"
        >
          <span class="icon-plus"></span>
        </span>
      </tooltip>
    </div>

    <div class="list" v-if="schemas.length">
      <schema
        :schema="schema"
        :key="schema.id"
        v-for="schema in schemas"
      ></schema>
    </div>

    <base-form v-form="form" :action="createSchema" v-if="shouldShowForm">
      <base-input
        validate
        name="name"
        v-model="form.name"
        label="Schema Name"
      ></base-input>

      <template slot="buttons">
        <span
          class="btn"
          v-if="schemas.length"
          @click.prevent="showForm = false"
          >Cancel</span
        >
        <button
          class="btn btn-primary btn-small"
          type="submit"
          :disabled="!form.isValid()"
        >
          Create Schema
        </button>
      </template>
    </base-form>
  </section>
</template>

<script>
import Schema from "./Schema";
export default {
  components: {
    Schema,
  },
  props: ["database"],
  data() {
    return {
      loaded: true,
      showForm: false,
      form: this.createForm({
        name: null,
      }).validation({
        rules: {
          name: "required|alpha",
        },
      }),
    };
  },
  methods: {
    createSchema() {
      this.$store
        .dispatch("user/sites/schemas/create", {
          parameters: {
            site: this.siteId,
          },
          data: {
            name: this.form.name,
            database: this.database,
          },
        })
        .then(() => this.resetForm());
    },
    resetForm() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    serverId() {
      return this.$route.params.server;
    },
    schemas() {
      return this.$store.state.user.sites.schemas.schemas.filter((schema) => {
        return schema.database === this.database;
      });
    },
    shouldShowForm() {
      return (this.loaded && this.schemas.length === 0) || this.showForm;
    },
  },
};
</script>
