<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav :site="site"></site-nav>
                    <form @submit.prevent="updateEnvironmentFile" v-for="server in site_servers">
                        <div :data-server_id="server.id" v-file-editor :data-path="'/home/codepier/' + site.domain + '/.env'" class="editor">Loading . . . </div>
                        <button type="submit">Update Environment File</button>
                    </form>
                </div>
            </div>
        </section>
        <servers></servers>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';
    export default {
        components: {
            SiteHeader,
            SiteNav,
            LeftNav,
            Servers
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods : {
            fetchData : function() {
                siteStore.dispatch('getSite', this.$route.params.site_id);
                siteStore.dispatch('getSiteServers', this.$route.params.site_id);
            },
            updateEnvironmentFile : function() {
                alert('yay');
            }
        },
        computed : {
            site : () => {
                return siteStore.state.site;
            },
            site_servers : () => {
                return siteStore.state.site_servers;
            }
        }
    }
</script>