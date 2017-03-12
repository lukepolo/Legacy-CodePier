<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <template v-if="editing">
                    <input v-model="form.name" type="text" :value="pile.name">

                    <div class="action-btn">
                        <button @click="savePile" class="btn btn-small btn-primary"><span class="icon-check_circle"></span></button>
                    </div>
                </template>
                <template v-else>
                    {{ pile.name }}

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

        <div class="btn-footer text-center" v-if="editing">
            <button @click="cancel" class="btn">Cancel</button>
            <button @click="savePile" class="btn btn-primary">Save</button>
        </div>
        <div class="btn-footer text-center" v-else>
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