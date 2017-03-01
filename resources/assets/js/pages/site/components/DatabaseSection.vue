<template>
    <section>
        <div>
            <h1>{{ database }}</h1>

            <div class="jcf-form-wrap">
                <form @submit.prevent="createSchema" class="floating-labels">
                    <h3>Schemas :</h3>
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
                    <tr v-for="schema in siteSchemas">
                        <td>{{ schema.name }}</td>
                        <td><a href="#" @click="deleteSchema(schema.id)">delete</a> </td>
                    </tr>
                </tbody>
            </table>

            </div>
        </div>
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
                this.$store.dispatch('createSiteSchema', {
                    database : this.database,
                    name : this.schemaForm.name,
                    site : this.$route.params.site_id
                }).then((schema) => {
                    if(schema.id) {
                        this.schemaForm.name = ''
                    }
                })
            },
            deleteSchema(database) {
                this.$store.dispatch('deleteSiteSchema', {
                    schema : database,
                    site : this.$route.params.site_id
                })
            }
        },
        computed : {
            siteSchemas() {
                return _.filter(this.$store.state.siteSchemasStore.site_schemas, (schema) => {
                    return schema.database == this.database
                })
            }
        }
    }
</script>