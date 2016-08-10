<template>
    <div class="group">
        <div class="group-heading">
            <h4>
                <template v-if="editing">
                    <input v-model="name" type="text" :value="pile.name">
                </template>
                <template v-else>
                    {{ pile.name }}
                </template>
            </h4>
        </div>

        <template v-if="pile.servers && pile.servers.length">
            <div class="group-content">
                <h4>Servers</h4>

                <div class="server-list">
                    <a class="server">
                        <div class="server-name">CodePier 9.0</div>
                    </a>
                    <a class="server">
                        <div class="server-name">Switchblade.io</div>
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
            <a class="btn btn-primary" href="create-server.html">Create Server</a>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['pile', 'index'],
        data() {
            return {
                name: this.pile.name,
                editing : this.pile.editing
            }
        },
        methods : {
            cancel : function() {
                this.editing = false;
            },
            edit : function() {
                this.editing = true;
            },
            deletePile :function() {
                if(this.pile.id) {
                    Vue.http.delete(this.action('Pile\PileController@destroy', { pile : this.pile.id })).then((response) => {
                        vue.user.piles.splice(this.index, 1);
                    }, (errors) => {
                        alert(error);
                    })
                }
            },
            savePile : function() {
                if(this.pile.id) {
                    Vue.http.put(this.action('Pile\PileController@update', { pile : this.pile.id }), {
                        name : this.name
                    }).then((response) => {
                        vue.user.piles.splice(this.index, 1);
                        vue.user.piles.push(response.json());
                    }, (errors) => {
                        alert(error);
                    })
                } else {
                    Vue.http.post(this.action('Pile\PileController@store'), {
                        name : this.name
                    }).then((response) => {
                        vue.user.piles.splice(this.index, 1);
                        vue.user.piles.push(response.json());
                    }, (errors) => {
                        alert(error);
                    })
                }
                this.editing = false;
            }
        }
    }
</script>