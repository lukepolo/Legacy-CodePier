<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav :site="site"></site-nav>

                    <a href="#" class="btn btn-xs">Create Signing Request</a>
                    <a href="#" class="btn btn-xs">Install Certificate</a>
                    {!! Form::open(['action' => ['SiteController@postRequestLetsEncryptSSLCert', $site->server_id, $site->id]]) !!}
                    {!! Form::label('Domains') !!}
                    {!! Form::text('domains', $site->domain) !!}
                    {!! Form::submit('Request SSL') !!}
                    {!! Form::close() !!}


                    {!! Form::open(['action' => ['SiteController@postAddSSLCert', $site->server_id, $site->id]]) !!}
                    {!! Form::label('Domains') !!}
                    {!! Form::text('domains', $site->domain) !!}
                    {!! Form::submit('Request SSL') !!}
                    {!! Form::close() !!}


                    @foreach($site->ssls as $ssl)
                    <p>
                        $ssl->type }} : $ssl->domains }}
                        @if($ssl->active)
                        <a href="#">Deactivate</a>
                        @else
                        <a href="#">Activate</a>
                        @endif

                        <a href="#">Delete</a>
                    </p>
                    @endforeach

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