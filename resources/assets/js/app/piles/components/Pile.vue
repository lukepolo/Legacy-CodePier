<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <template v-if="editing">
                    <input ref="pile_name" v-model="form.name" type="text" :value="pile.name" placeholder="Pile Name" @keyup.enter="savePile">

                    <div class="action-btn">
                        <button @click="savePile" class="btn btn-small btn-primary"><span class="icon-check_circle"></span></button>
                    </div>

                </template>
                <template v-else>
                    <div class="group--item-heading-name">{{ pile.name }}</div>

                    <div class="action-btn">
                        <button @click="edit" class="btn btn-small"><span class="icon-pencil"></span></button>
                    </div>
                </template>
            </h4>
        </div>

        <template v-if="pile.sites && pile.sites.length">
            <div class="group--item-content">
                <h4>Sites</h4>
                <div class="list">
                    <router-link class="list--item" :to="{ name: 'site_repository', params : { site_id : site.id} }" v-for="site in pile.sites" :key="site.id">
                        <div class="list--item-name">
                            {{ site.name }}
                        </div>
                    </router-link>
                </div>
            </div>
        </template>

        <template v-else="pile.sites">
            <div class="group--item-content">
                <h4>No Sites</h4>
            </div>
        </template>

        <div class="btn-footer text-center">
            <button @click="deletePile()" class="btn">
                <template v-if="pile.id">
                    Delete
                </template>
                <template v-else>
                    Cancel
                </template>
            </button>
            <button class="btn" v-if="pile.id">TODO - doesn't do anything yet! --- Create Site</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['pile', 'index'],
        data() {
            return {
                form: {
                    pile : this.pile.id,
                    name: this.pile.name
                },
                editing: this.pile.editing
            }
        },
        watch: {
            'editing'() {
                Vue.nextTick(() => {
                    if (this.editing) {
                        this.$refs.pile_name.focus()
                    }
                })
            },
        },
        methods: {
            cancel() {
                if (!this.pile.id) {
                    this.$store.commit('user_piles/removeTemp', this.index)
                }

                this.editing = false
            },
            edit() {
                this.editing = true
            },
            deletePile() {

                if(this.pile.id) {
                    return this.$store.dispatch('user_piles/destroy', this.pile.id)
                }

                this.cancel()
            },
            savePile() {
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
        computed : {
            sites() {
                return this.$store.state.user_piles.piles[this.pile.id].sites
            }
        },
        created() {
            if(this.pile.id) {
                this.$store.dispatch('user_piles/sites', this.pile.id)
            }
        }
    }
</script>