<template>
    <section>

        <form @submit.prevent="createSchema" class="flyform--submit">
            <div class="flyform--group">
                <input type="text" name="name" v-model="schemaForm.name" placeholder=" ">
                <label for="name">Schema Name</label>
            </div>

            <div class="flyform--footer-btns">
                <button class="btn btn-primary btn-small" type="submit">Create Schema</button>
            </div>
        </form>

        <br><br>

        <div class="list">
            <div class="list--item list--item-icons list--item-heading">
                <div class="list--item-text">
                    -- schema name --
                </div>
                <div class="list--icon">
                    <span class="icon-group_add"></span>
                </div>
                <div class="list--icon">
                    <div class="icon-trash"></div>
                </div>
            </div>

            <div class="list--item list--item-icons list--item-subgroup">
                <div class="list--item-text">
                    -- user name --
                </div>

                <div class="list--icon">
                    <div class="icon-trash"></div>
                </div>
            </div>

            <form>
                <div class="flyform--submit">
                    <div class="flyform--group">
                        <input type="text" name="name" placeholder=" ">
                        <label for="name">Name</label>
                    </div>

                    <div class="flyform--group">
                        <input type="password" name="password" placeholder=" ">
                        <label for="password">Password</label>
                    </div>

                    <div class="flyform--footer-btns">
                        <button class="btn btn-primary btn-small" type="submit">Create User</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="list">
            <div class="list--item list--item-icons list--item-heading">
                <div class="list--item-text">
                    -- schema name --
                </div>

                <div class="list--icon">
                    <span class="icon-group_add"></span>
                </div>
                <div class="list--icon">
                    <div class="icon-trash"></div>
                </div>
            </div>


            <form>
                <div class="flyform--submit">
                    <div class="flyform--group">
                        <input type="text" name="name" placeholder=" ">
                        <label for="name">Name</label>
                    </div>

                    <div class="flyform--group">
                        <input type="password" name="password" placeholder=" ">
                        <label for="password">Password</label>
                    </div>

                    <div class="flyform--footer-btns">
                        <button class="btn btn-primary btn-small" type="submit">Create User</button>
                    </div>
                </div>
            </form>
        </div>



        <br><br><br><br><br>
        <h3><span class="h--label">{{ database }}</span></h3>

        <div class="grid-2">



            <table class="table" v-if="schemas.length">
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
                                    <a href="#" @click="deleteSchema(schema.id)"><span class="icon-trash"></span></a>
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
        props : ['database'],
        data() {
            return {
                schemaForm : this.createForm({
                    name : null
                })
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