<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">

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
        methods: {
            fetchData: function () {
                siteStore.dispatch('getSite', this.$route.params.site_id);
            }
        },
        computed : {
            site : () => {
                return siteStore.state.site;
            }
        }
    }
</script>