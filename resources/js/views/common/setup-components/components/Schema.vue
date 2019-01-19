<template>
  <section>
    <div class="list--item list--item-icons list--item-heading">
      <div class="list--item-text">{{ schema.name }}</div>
      <div class="list--icon">
        <tooltip message="Add User">
          <span class="icon-group_add" @click="showForm = true"></span>
        </tooltip>
      </div>
      <div class="list--icon">
        <tooltip message="Delete Schema">
          <div class="icon-trash" @click="deleteSchema(schema.id)"></div>
        </tooltip>
      </div>
    </div>
    <div v-if="schemaUsers.length">
      <template v-for="user in schemaUsers">
        <div class="list--item list--item-icons list--item-subgroup">
          <div class="list--item-text">{{ user.name }}</div>

          <div class="list--icon">
            <tooltip message="Delete User">
              <div class="icon-trash" @click="deleteSchemaUser(user.id)"></div>
            </tooltip>
          </div>
        </div>
      </template>
    </div>

    <base-form v-if="shouldShowForm" v-form="form" :action="createSchemaUser">
      <base-input
        label="Name"
        name="name"
        validate
        v-model="form.name"
      ></base-input>

      <base-input
        label="Password"
        name="schema_user_password"
        validate
        v-model="form.password"
      ></base-input>

      <template slot="buttons">
        <span class="btn btn-small" @click="showForm = false"
          ><span class="icon-x"></span
        ></span>
        <button
          class="btn btn-primary btn-small"
          type="submit"
          :disabled="!form.isValid()"
        >
          Create User
        </button>
      </template>
    </base-form>
  </section>
</template>

<script>
export default {
  props: ["schema"],
  data() {
    return {
      showForm: false,
      form: this.createForm({
        name: null,
        password: null,
        schema_ids: [this.schema.id],
      }).validation({
        rules: {
          password: "required",
          name: "required|alpha",
        },
      }),
    };
  },
  methods: {
    deleteSchema(database) {
      this.$store.dispatch("user/sites/schemas/destroy", {
        schema: database,
        site: this.siteId,
      });
    },
    deleteSchemaUser(user) {
      this.$store.dispatch("user/sites/schemas/users/destroy", {
        site: this.siteId,
        schema_user: user,
      });
    },
    createSchemaUser() {
      this.$store
        .dispatch("user/sites/schemas/users/create", {
          parameters: {
            site: this.siteId,
          },
          data: this.form.data(),
        })
        .then((schema) => {
          if (schema.id) {
            this.resetForm();
          }
        });
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
    schemaUsers() {
      return this.$store.state.user.sites.schemas.users.schema_users.filter(
        (user) => {
          return user.schema_ids.includes(this.schema.id);
        },
      );
    },
    shouldShowForm() {
      return this.schemaUsers.length === 0 || this.showForm;
    },
  },
};
</script>
