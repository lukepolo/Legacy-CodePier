<template>
    <section>
        <H3>{{ database }}</H3>

        <div class="jcf-form-wrap">

            <form @submit.prevent="createSchema" class="floating-labels">
                <div class="jcf-input-group">
                    <div class="input-question">Schemas :</div>
                </div>
                <div class="jcf-input-group">
                    <input type="text" name="name" v-model="schemaForm.name">
                    <label for="name">
                        <span class="float-label">Name</span>
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create Schema</button>
                </div>

            </form>
        </div>

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
                    <td><a href="#" @click="deleteSchema(schema.id)">delete</a> </td>
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
                    this.$store.dispatch('createSiteSchema', {
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
                    this.$store.dispatch('createServerSchema', {
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
                    this.$store.dispatch('deleteSiteSchema', {
                        schema: database,
                        site: this.siteId

                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('deleteServerSchema', {
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
                    return _.filter(this.$store.state.siteSchemasStore.site_schemas, (schema) => {
                        return schema.database == this.database
                    })
                }

                if(this.serverId) {
                    return _.filter(this.$store.state.serverSchemasStore.server_schemas, (schema) => {
                        return schema.database == this.database
                    })
                }
            }
        }
    }
</script>