<template>
    <section>
        <div class="heading flex flex--center">
            <h3 class="flex--grow">
                {{ database }} Schemas
            </h3>

            <tooltip message="Add Schema">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <div class="list" v-if="schemas.length">
            <schema :schema="schema" :key="schema.id" v-for="schema in schemas"></schema>
        </div>

        <form @submit.prevent="createSchema" class="flyform--submit" v-if="shouldShowForm">
            <div class="flyform--group">
                <input type="text" name="name" v-model="form.name" placeholder=" ">
                <label for="name">Schema Name</label>
            </div>

            <div class="flyform--footer-btns">
                <span class="btn" v-if="schemas.length" @click.prevent="showForm = false">Cancel</span>
                <button class="btn btn-primary btn-small" type="submit">Create Schema</button>
            </div>
        </form>
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
      }),
    };
  },
  methods: {
    createSchema() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_schemas/store", {
            site: this.siteId,
            database: this.database,
            name: this.form.name,
          })
          .then(() => {
            this.resetForm();
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_schemas/store", {
            server: this.serverId,
            database: this.database,
            name: this.form.name,
          })
          .then(() => {
            this.resetForm();
          });
      }
    },
    resetForm() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    schemas() {
      if (this.siteId) {
        return _.filter(
          this.$store.state.user_site_schemas.schemas,
          (schema) => {
            return schema.database === this.database;
          },
        );
      }

      if (this.serverId) {
        return _.filter(
          this.$store.state.user_server_schemas.schemas,
          (schema) => {
            return schema.database === this.database;
          },
        );
      }
    },
    shouldShowForm() {
      return (this.loaded && this.schemas.length === 0) || this.showForm;
    },
  },
};
</script>
