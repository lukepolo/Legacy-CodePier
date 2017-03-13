<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <template v-if="editing">
                    <input ref="pile_name" v-model="form.name" type="text" :value="pile.name" placeholder="Pile Name">

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
                    <router-link class="list--item" :to="{ name: 'site_repository', params : { site_id : site.id} }" v-for="site in pile.sites">
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
            <button @click="deletePile()" class="btn">Delete</button>
            <button class="btn">Create Site</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['pile', 'index'],
        data() {
            return {
                form: {
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
                    this.$store.state.pilesStore.piles.splice(this.index, 1)
                }

                this.editing = false
            },
            edit() {
                this.editing = true
            },
            deletePile() {
                this.$store.dispatch('deletePile', this.pile.id)
            },
            savePile() {
                if (this.pile.id) {

                    this.form['pile'] = this.pile

                    this.$store.dispatch('updatePile', this.form)

                } else {
                    this.$store.dispatch('createPile', this.form).then(function(pile) {
                        if(pile.id) {
                            this.$emit('deletePile', this.index)
                        }
                    })
                }

                this.editing = false
            }
        },
        computed : {
            sites() {
                return this.$store.state.pilesStore.piles[this.pile.id].sites
            }
        },
        created() {
            if(this.pile.id) {
                this.$store.dispatch('getPileSites', this.pile.id)
            }
        }
    }
</script>