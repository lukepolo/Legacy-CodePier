<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <template v-if="editing">
                    <input ref="pile_name" v-model="form.name" type="text" :value="pile.name" placeholder="Pile Name" @keyup.enter="savePile">

                    <div class="action-btn">
                        <button @click="savePile" class="btn btn-small btn-primary"><span class="icon-check_circle"></span></button>

                        <button @click="deletePile()" class="btn btn-small">
                            <template v-if="pile.id">
                                <span class="icon-trash"></span>
                            </template>
                            <template v-else>
                                <span class="icon-cancel"></span>
                            </template>
                        </button>
                    </div>

                </template>
                <template v-else>
                    <template v-if="pile.name && pile.name.length > 23">
                        <tooltip :message="pile.name" placement="bottom">
                            <div class="group--item-heading-name">{{ pile.name }}</div>
                        </tooltip>
                    </template>
                    <template v-else>
                        <div class="group--item-heading-name">{{ pile.name }}</div>
                    </template>

                    <div class="action-btn">
                        <button @click="edit" class="btn btn-small"><span class="icon-pencil"></span></button>

                        <button @click="deletePile()" class="btn btn-small">
                            <span class="icon-trash"></span>
                        </button>
                    </div>
                </template>
            </h4>
        </div>

        <div class="group--item-content">
            <template v-if="pile.sites && pile.sites.length">
                <h4>Sites</h4>
                <div class="list">
                    <router-link class="list--item" :to="{ name: 'site_overview', params : { site_id : site.id} }" v-for="site in pile.sites" :key="site.id">
                        <div class="list--item-name">
                            {{ site.name }}
                        </div>
                    </router-link>
                </div>
            </template>
            <template v-else>
                <h4>No Sites</h4>
            </template>

            <div class="group--item-link" @click="addingSite = true">
                <template v-if="!addingSite && !editing">
                    <span class="icon-plus"></span> Create New Site
                </template>
                <site-form :pile="pile" :adding.sync="addingSite"></site-form>
            </div>
        </div>
    </div>
</template>

<script>
    import SiteForm from './../../../components/SiteForm.vue'
    export default {
        props: ['pile', 'index'],
        components: {
            SiteForm
        },
        data () {
            return {
                form: {
                    pile: this.pile.id,
                    name: this.pile.name
                },
                addingSite: false,
                editing: this.pile.editing
            }
        },
        watch: {
            'editing' () {
                Vue.nextTick(() => {
                    if (this.editing) {
                        this.$refs.pile_name.focus()
                    }
                })
            }
        },
        methods: {
            cancel () {
                if (!this.pile.id) {
                    this.$store.commit('user_piles/removeTemp', this.index)
                }

                this.editing = false
            },
            edit () {
                this.editing = true
            },
            deletePile () {
                if (this.pile.id) {
                    return this.$store.dispatch('user_piles/destroy', this.pile.id)
                }

                this.cancel()
            },
            savePile () {
                if (this.pile.id) {
                    this.$store.dispatch('user_piles/update', this.form)
                } else {
                    this.$store.dispatch('user_piles/store', this.form).then(() => {
                        this.cancel()
                    })
                }

                this.editing = false
            }
        },
        computed: {
            sites () {
                return this.$store.state.user_piles.piles[this.pile.id].sites
            }
        }
    }
</script>
