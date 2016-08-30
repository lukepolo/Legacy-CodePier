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

        <template v-if="pile.servers">
            <div class="group-content">
                <h4>Servers</h4>
                <div class="server-list" v-for="server in pile.servers">
                    <a class="server">
                        <div class="server-name">{{ server.name }}</div>
                    </a>
                </div>
            </div>
        </template>

        <div class="btn-footer text-center" v-if="editing">
            <button v-on:click="cancel" class="btn">Cancel</button>
            <button v-on:click="savePile" class="btn btn-primary">Save</button>
        </div>
        <div v-else>
            <button v-on:click="edit" class="btn">Edit</button>
            <button v-on:click="deletePile()" class="btn">Delete</button>

            <router-link to="/server/create" class="btn btn-primary">
                Create Server
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['pile', 'index'],
        data() {
            return {
                form : {
                    name: this.pile.name
                },
                editing : this.pile.editing
            }
        },
        methods : {
            cancel : function() {
                if(!this.pile.id) {
                    pileStore.state.piles.splice(this.index, 1);
                }

                this.editing = false;
            },
            edit : function() {
                this.editing = true;
            },
            deletePile :function() {
                pileStore.dispatch('deletePile', this.pile.id);
            },
            savePile : function() {
                if(this.pile.id) {

                    this.form['pile'] = this.pile;

                    pileStore.dispatch('updatePile', this.form);

                } else {
                    pileStore.dispatch('createPile', this.form);
                }
                this.editing = false;
            }
        }
    }
</script>