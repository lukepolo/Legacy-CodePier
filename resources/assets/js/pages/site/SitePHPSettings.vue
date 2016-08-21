<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>

                    {!! Form::open(['action' => ['SiteController@postSavePHPSettings', $site->server_id, $site->id]]) !!}
                    Max Upload Size
                    {!! Form::text('max_upload_size', !empty($site->settings) && isset($site->settings->data['max_upload_size']) ? $site->settings->data['max_upload_size']: null) !!} MB
                    {!! Form::submit('Update') !!}
                    {!! Form::close() !!}

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