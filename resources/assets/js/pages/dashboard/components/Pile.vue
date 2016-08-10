<template>
    <div class="group">
        <div class="group-heading">
            <h4>
                <template v-if="pile.editing">
                    <input v-model="name" type="text" :value="pile.name">
                </template>
                <template v-else>
                    {{ pile.name }}
                </template>
            </h4>
        </div>

        <template v-if="pile.servers.length">
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

        <div class="btn-footer text-center" v-if="!pile.editing">
            <button v-on:click="edit" class="btn">Edit</button>
            <button v-on:click="deletePile()" class="btn">Delete</button>
            <a class="btn btn-primary" href="create-server.html">Create Server</a>
        </div>
        <div v-else>
            <button v-on:click="cancel" class="btn">Cancel</button>
            <button v-on:click="savePile" class="btn btn-primary">Save</button>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['pile', 'index'],
        data() {
            return {
                name: this.pile.name,
            }
        },
        methods : {
            cancel : function() {
                this.pile.editing = false;
            },
            edit : function() {
                this.pile.editing = true;
            },
            deletePile :function() {
                if(this.pile.id) {
                    alert('we gotta delete it from the server');
                }
                vue.user.piles.splice(this.index, 1);
            },
            savePile : function() {
                alert(this.index);
                if(this.pile.id) {
                    // update
                } else {
//                    Vue.http.post(this.action('Pile\PileController@store'), {
//                        name : this.name
//                    }).then((response) => {
//
//                        alert(this.index);
//                        vue.user.piles[this.index].name = response.json().name;
//                    }, (errors) => {
//                        alert(error);
//                    })
                }
            }
        }
    }
</script>