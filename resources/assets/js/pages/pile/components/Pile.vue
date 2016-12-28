<template>
    <div class="group">
        <div class="group-heading">
            <h4>
                <template v-if="editing">
                    <input v-model="form.name" type="text" :value="pile.name">
                </template>
                <template v-else>
                    {{ pile.name }}
                </template>
            </h4>
        </div>

        <template v-if="pile.sites">
            <div class="group-content">
                <h4>Sites</h4>
                <div class="site-list" v-for="site in pile.sites">
                    <a class="site">
                        <div class="site-name">
                            <router-link :to="{ name: 'site_repository', params : { site_id : site.id} }">
                                <div class="site-name">
                                    {{ site.name }}
                                </div>
                            </router-link>
                        </div>
                    </a>
                </div>
            </div>
        </template>

        <div class="btn-footer text-center" v-if="editing">
            <button @click="cancel" class="btn">Cancel</button>
            <button @click="savePile" class="btn btn-primary">Save</button>
        </div>
        <div class="btn-footer text-center" v-else>
            <button @click="edit" class="btn">Edit</button>
            <button @click="deletePile()" class="btn">Delete</button>
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
        methods: {
            cancel() {
                if (!this.pile.id) {
                    this.$store.state.pilesStore.piles.splice(this.index, 1);
                }

                this.editing = false;
            },
            edit() {
                this.editing = true;
            },
            deletePile() {
                this.$store.dispatch('deletePile', this.pile.id);
            },
            savePile() {
                if (this.pile.id) {

                    this.form['pile'] = this.pile;

                    this.$store.dispatch('updatePile', this.form);

                } else {
                    this.$store.dispatch('createPile', this.form);
                }
                this.editing = false;
            }
        },
        computed : {
            sites() {
                return this.$store.state.pilesStore.piles[this.pile.id].sites;
            }
        },
        created() {
            if(this.pile.id) {
                this.$store.dispatch('getPileSites', this.pile.id);
            }
        }
    }
</script>