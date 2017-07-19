<template>
    <section>
        <hr>
        <h3><span class="h--label">Users</span></h3>

        <div class="grid-2">
            <form @submit.prevent="createSchemaUser">
                <div class="flyform--group">
                    <input type="text" name="name" v-model="schemaUserForm.name" placeholder=" ">
                    <label for="name">Name</label>
                </div>

                <div class="flyform--group">
                    <input type="password" name="password" v-model="schemaUserForm.password" placeholder=" ">
                    <label for="password">Password</label>
                </div>

                <div class="flyform--group">
                    <label>Select Schema(s)</label>
                    <select multiple v-model="schemaUserForm.schema_ids">
                        <optgroup :label="schemaType" v-for="schemaType in schemaTypes">
                            {{ schemaType }}
                            <option v-for="schema in getSchemasByType(schemaType)" :value="schema.id">{{ schema.name }}</option>
                        </optgroup>
                    </select>
                </div>

                <div class="flyform--footer-btns">
                    <button class="btn btn-primary btn-small" type="submit">Create User</button>
                </div>
            </form>

            <table class="table" v-if="schemaUsers.length">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Database - Schema</th>
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
                                <a href="#" @click="deleteSchemaUser(schemaUser.id)"><span class="icon-trash"></span></a>
                            </span>
                        </tooltip>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>




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

                if(this.serverId) {
                    this.$store.dispatch('user_server_schema_users/store', {
                        server : this.serverId,
                        name : this.schemaUserForm.name,
                        password : this.schemaUserForm.password,
                        schema_ids : this.schemaUserForm.schema_ids,
                    }).then((schema) => {
                        if(schema.id) {
                            this.schemaUserForm.reset()
                        }
                    })
                }

            },
            deleteSchemaUser(user) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_schema_users/destroy', {
                        schema_user: user,
                        site: this.siteId
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_schema_users/destroy', {
                        schema_user: user,
                        server: this.serverId
                    })
                }
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

                if(this.serverId) {
                    return this.$store.state.user_server_schema_users.users
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