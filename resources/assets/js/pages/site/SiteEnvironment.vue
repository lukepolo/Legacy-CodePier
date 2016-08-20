<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
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

    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    export default {
        components : {
            SiteNav,
            LeftNav,
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