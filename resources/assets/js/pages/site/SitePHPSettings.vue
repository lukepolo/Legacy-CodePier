<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
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