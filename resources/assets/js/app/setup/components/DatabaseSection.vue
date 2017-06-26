<template>
    <section>
        <h3>{{ database }}</h3>

        <form @submit.prevent="createSchema" class="floating-labels">
            <div class="flyform--group">
                <input type="text" name="name" v-model="schemaForm.name" placeholder=" ">
                <label for="name">Database Name</label>
            </div>

            <button class="btn btn-primary" type="submit">Create Schema</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Schemas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="schema in schemas">
                    <td>{{ schema.name }}</td>
                    <td class="table--action">
                        <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a href="#" @click="deleteSchema(schema.id)"><span class="fa fa-trash"></span></a>
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
        props : ['database'],
        data() {
            return {
                schemaForm : {
                    name : null
                }
            }
        },
        methods : {
            createSchema() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_schemas/store', {
                        site : this.siteId,
                        database : this.database,
                        name : this.schemaForm.name
                    }).then((schema) => {
                        if(schema.id) {
                            this.schemaForm.name = ''
                        }
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_schemas/store', {
                        server : this.serverId,
                        database : this.database,
                        name : this.schemaForm.name,
                    }).then((schema) => {
                        if(schema.id) {
                        this.schemaForm.name = ''
                    }
                })
                }

            },
            deleteSchema(database) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_schemas/destroy', {
                        schema: database,
                        site: this.siteId

                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_schemas/destroy', {
                        schema: database,
                        server: this.serverId
                    })
                }
            }
        },
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            schemas() {
                if(this.siteId) {
                    return _.filter(this.$store.state.user_site_schemas.schemas, (schema) => {
                        return schema.database === this.database
                    })
                }

                if(this.serverId) {
                    return _.filter(this.$store.state.user_server_schemas.schemas, (schema) => {
                        return schema.database === this.database
                    })
                }
            }
        }
    }
</script>