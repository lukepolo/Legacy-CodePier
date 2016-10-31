<template>
    <div v-if="site">
        Site SSH Keys
        <template v-for="sshKey in sshKeys">
            {{ sshKey.id }}
        </template>
    </div>
</template>

<script>
    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSiteSshKeys', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            sshKeys() {
                return this.$store.state.siteSshKeysStore.site_ssh_keys;
            }
        }
    }
</script>