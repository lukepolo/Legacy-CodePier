<template>
    <section>
        <left-nav></left-nav>
        <div class="group">
            Domain
            <input v-model="domain" type="text" :value="domain">

            WildCard Domain
            <input type="checkbox" v-model="wildcard_domain" name="wildcard_domain" value="1">

            <template v-for="server in servers">
                <input type="checkbox" v-model="selectedServers" name="selectedServers[]" :value="server.id">
                {{ server.name }} ({{ server.ip }})
            </template>

            <button v-on:click="saveSite" class="btn btn-primary">Save</button>
        </div>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components: {
            LeftNav
        },
        data() {
            return {
                domain: null,
                wildcard_domain: false,
                selectedServers: [],
            }
        },
        computed: {
            servers () {
                return serverStore.state.servers;
            }
        },
        methods: {
            saveSite: function () {
                siteStore.dispatch('createSite', {
                    domain: this.domain,
                    servers: this.selectedServers,
                    wildcard_domain: this.wildcard_domain,
                }).then(function(response) {

                });
            }
        }
    }
</script>