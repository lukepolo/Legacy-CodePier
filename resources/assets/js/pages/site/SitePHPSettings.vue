<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav :site="site"></site-nav>
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
        computed : {
            site : () => {
                return siteStore.state.site;
            }
        },
        mounted() {
            siteStore.dispatch('getSite', this.$route.params.site_id);
        }
    }
</script>