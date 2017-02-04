<template>
    <section>
        <div>
            <h1>{{ database }}</h1>

            <div class="jcf-form-wrap">
                <form @submit.prevent="createSchema(database)" class="floating-labels">
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
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="schema in siteSchemas">
                        <td>{{ schema.name }}</td>
                    </tr>
                </tbody>
            </table>


            <div class="jcf-form-wrap">
                <form @submit.prevent="createUser(database)" class="floating-labels">
                    <h3>Users :</h3>
                    <div class="jcf-input-group">
                        <input type="text" name="user" v-model="userForm.user">
                        <label for="user">
                            <span class="float-label">Name</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input type="password" name="user" v-model="userForm.password">
                        <label for="password">
                            <span class="float-label">Password</span>
                        </label>
                    </div>


                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">Create User</button>
                    </div>

                </form>


                <table class="table">
                    <thead>
                        <tr>
                            <th>Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>codepier - super user</td>
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
                },
                userForm : {
                    name : null,
                    schemas : [],
                    password : null,
                }
            }
        },
        methods : {
            createUser(database) {
                alert('create user for '+database);
            },
            updateUser(database) {
                alert('create user for '+database);
            },
            deleteUser(database) {

            },
            createSchema(database) {
                this.$store.dispatch('createSiteSchema', {
                    database : this.database,
                    name : this.schemaForm.name,
                    site : this.$route.params.site_id
                })
            },
            updateSchema(database) {

            },
            deleteSchema(database) {

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