<template>
    <section>
        <H3>User Section</H3>

        <div class="jcf-form-wrap">

            <form @submit.prevent="createSchemaUser" class="floating-labels">
                <div class="jcf-input-group">
                    <input type="text" name="name" v-model="schemaUserForm.name">
                    <label for="name">
                        <span class="float-label">Name</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input type="text" name="name" v-model="schemaUserForm.password">
                    <label for="name">
                        <span class="float-label">Password</span>
                    </label>
                </div>

                <select multiple v-model="schemaUserForm.schema_ids">
                    <optgroup :label="schemaType" v-for="schemaType in schemaTypes">
                        {{ schemaType }}
                        <option v-for="schema in getSchemasByType(schemaType)" :value="schema.id">{{ schema.name }}</option>
                    </optgroup>
                </select>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create User</button>
                </div>

            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>user</th>
                    <th>databases</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="schemaUser in schemaUsers">
                    <td>
                        {{ schemaUser.name }}
                    </td>
                    <td>
                        <p v-for="schemaId in schemaUser.schema_ids">
                            <template v-if="getSchema(schemaId)">
                                {{ getSchema(schemaId).database }} - {{ getSchema(schemaId).name }}
                            </template>
                        </p>
                    </td>
                    <td class="table--action">
                        <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a href="#" @click="deleteSchemaUser(schemaUser.id)"><span class="fa fa-trash"></span></a>
                            </span>
                        </tooltip>
                    </td>
                </tr>

            </tbody>
        </table>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                schemaUserForm : this.createForm({
                    name : null,
                    schema_ids : []
                })
            }
        },
        methods : {
            getSchema (schemaId) {
                if(this.schemas) {
                    return _.find(this.schemas, { id : schemaId })
                }
            },
            createSchemaUser() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_schema_users/store', {
                        site : this.siteId,
                        name : this.schemaUserForm.name,
                        password : this.schemaUserForm.password,
                        schema_ids : this.schemaUserForm.schema_ids,
                    }).then((schema) => {
                        if(schema.id) {
                            this.schemaUserForm.reset()
                        }
                    })
                }

//                if(this.serverId) {
//                    this.$store.dispatch('user_server_schemas/store', {
//                        server : this.serverId,
//                        name : this.schemaUserForm.name,
//                        password : this.schemaUserForm.password,
//                        databases : this.schemaUserForm.databases,
//                    }).then((schema) => {
//                        if(schema.id) {
//                            this.schemaUserForm.reset()
//                        }
//                    })
//                }

            },
            deleteSchemaUser(user) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_schema_users/destroy', {
                        schema_user: user,
                        site: this.siteId
                    })
                }

//                if(this.serverId) {
//                    this.$store.dispatch('user_server_schemas/destroy', {
//                        schema: database,
//                        server: this.serverId
//                    })
//                }
            },
            getSchemasByType(schemaType) {
                return _.filter(this.schemas, (schema) => {
                    return schema.database === schemaType
                })
            }
        },
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            schemas() {
                if(this.siteId) {
                    return this.$store.state.user_site_schemas.schemas
                }

                if(this.serverId) {
                    return this.$store.state.user_server_schemas.schemas
                }
            },
            serverId() {
                return this.$route.params.server_id
            },
            schemaUsers() {
                if(this.siteId) {
                    return this.$store.state.user_site_schema_users.users
                }
            },
            schemaTypes() {
                if(this.schemas) {
                    return _.uniq(_.map(this.schemas, 'database'))
                }
            }
        },
        filters : {
            schemaType(schema, type) {
                return schema.database === type
            }
        }
    }
</script>