<template>
    <div class="group">
        <div class="group-heading">
            <h4>
                <template v-if="editing">
                    <input v-model="domain" type="text" :value="domain">
                </template>
                <template v-else>
                    {{ site.domain }}
                </template>
            </h4>
        </div>

        Web Directory
        <template v-if="editing">
            <input name="web_directory" v-model="web_directory" :value="web_directory">
        </template>
        <template v-else>
            {{ site.web_directory }}
        </template>


        WildCard Domain
        <template v-if="editing">
            <input type="checkbox" v-model="wildcard_domain" name="wildcard_domain" value="1">
        </template>
        <template v-else>
            {{ site.wildcard_domain }}
        </template>

        <template v-for="server in servers" v-if="editing">
            <input type="checkbox" v-model="selectedServers" name="selectedServers[]" :value="server.id">{{ server.name }} ({{ server.ip }})
        </template>
        <template v-else>
            Connected To :
            <p v-for="server in site.servers">
                {{ server.name }} - {{ server.ip }}
            </p>
        </template>

        <div class="btn-footer text-center" v-if="editing">
            <button v-on:click="cancel" class="btn">Cancel</button>
            <button v-on:click="saveSite" class="btn btn-primary">Save</button>
        </div>
        <div v-else>
            <button v-on:click="edit" class="btn">Edit</button>
            <button v-on:click="deleteSite()" class="btn">Delete</button>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['site', 'index'],
        data() {
            return {
                domain: this.site.domain,
                web_directory : this.site.web_directory,
                wildcard_domain : this.site.wildcard_domain ? true : false,
                selectedServers : this.site.servers ? _.map(this.site.servers, function(server) {
                        return server.id
                    }) : [],
                editing : this.site.editing
            }
        },
        computed: {
            servers () {
                return serverStore.state.servers;
            }
        },
        methods : {
            cancel : function() {
                if(!this.site.id) {
                    siteStore.state.sites.splice(this.index, 1);
                }

                this.editing = false;
            },
            edit : function() {
                this.editing = true;
            },
            deleteSite :function() {
                if(this.site.id) {
                    Vue.http.delete(this.action('Site\SiteController@destroy', { site : this.site.id })).then((response) => {
                        siteStore.dispatch('getSites');
                    }, (errors) => {
                        alert(error);
                    })
                }
            },
            saveSite : function() {
                if(this.site.id) {
                    Vue.http.put(this.action('Site\SiteController@update', { site : this.site.id }), {
                        pile_id : pileStore.state.current_pile_id,
                        domain : this.domain,
                        web_directory : this.web_directory,
                        wildcard_domain : this.wildcard_domain,
                        servers : this.selectedServers
                    }).then((response) => {
                        siteStore.dispatch('getSites');
                    }, (errors) => {
                        alert(error);
                    })
                } else {
                    Vue.http.post(this.action('Site\SiteController@store'), {
                        pile_id : pileStore.state.current_pile_id,
                        domain : this.domain,
                        web_directory : this.web_directory,
                        wildcard_domain : this.wildcard_domain,
                        servers : this.selectedServers
                    }).then((response) => {
                        siteStore.dispatch('getSites');
                    }, (errors) => {
                        alert(error);
                    })
                }
                this.editing = false;
            }
        }
    }
</script>