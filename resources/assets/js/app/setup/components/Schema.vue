<template>
    <section>
        <div class="list--item list--item-icons list--item-heading">
            <div class="list--item-text">
                {{ schema.name }}
            </div>
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
        <div v-if="users.length">
            <template v-for="user in users">
                <div class="list--item list--item-icons list--item-subgroup">
                    <div class="list--item-text">
                        {{ user.name }}
                    </div>

                    <div class="list--icon">
                        <tooltip message="Delete User">
                            <div class="icon-trash" @click="deleteSchemaUser(user.id)"></div>
                        </tooltip>
                    </div>
                </div>
            </template>

        </div>

        <form v-if="shouldShowForm" @submit.prevent="createSchemaUser()">
            <div class="flyform--submit">
                <div class="flyform--group">
                    <input type="text" name="name" placeholder=" " v-model="form.name">
                    <label for="name">Name</label>
                </div>

                <div class="flyform--group">
                    <input type="password" name="password" placeholder=" " v-model="form.password">
                    <label for="password">Password</label>
                </div>

                <div class="flyform--footer-btns">
                    <span class="btn btn-small" @click="showForm = false"><span class="icon-x"></span></span>
                    <button class="btn btn-primary btn-small" type="submit">Create User</button>
                </div>
            </div>
        </form>
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
        schema_ids: [this.schema.id]
      })
    };
  },
  methods: {
    deleteSchema(database) {
      if (this.siteId) {
        this.$store.dispatch("user_site_schemas/destroy", {
          schema: database,
          site: this.siteId
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_schemas/destroy", {
          schema: database,
          server: this.serverId
        });
      }
    },
    deleteSchemaUser(user) {
      if (this.siteId) {
        this.$store.dispatch("user_site_schema_users/destroy", {
          schema_user: user,
          site: this.siteId
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_schema_users/destroy", {
          schema_user: user,
          server: this.serverId
        });
      }
    },
    createSchemaUser() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_schema_users/store", {
            site: this.siteId,
            name: this.form.name,
            password: this.form.password,
            schema_ids: this.form.schema_ids
          })
          .then(schema => {
            if (schema.id) {
              this.resetForm();
            }
          });
      }
      if (this.serverId) {
        this.$store
          .dispatch("user_server_schema_users/store", {
            server: this.serverId,
            name: this.form.name,
            password: this.form.password,
            schema_ids: this.form.schema_ids
          })
          .then(schema => {
            if (schema.id) {
              this.resetForm();
            }
          });
      }
    },
    resetForm() {
      this.form.reset();
      this.showForm = false;
    }
  },
  computed: {
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    users() {
      return _.filter(this.schemaUsers, schemaUser => {
        return schemaUser.schema_ids.indexOf(this.schema.id) > -1;
      });
    },
    schemaUsers() {
      if (this.siteId) {
        return this.$store.state.user_site_schema_users.users;
      }
      if (this.serverId) {
        return this.$store.state.user_server_schema_users.users;
      }
    },
    shouldShowForm() {
      return this.users.length === 0 || this.showForm;
    }
  }
};
</script>