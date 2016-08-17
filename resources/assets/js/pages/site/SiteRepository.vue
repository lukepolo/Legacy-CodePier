<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav :site="site"></site-nav>

                    {!! Form::open(['action' => ['SiteController@postRenameDomain', $site->server->id, $site->id]]) !!}
                    {!! Form::label('Domain') !!}
                    {!! Form::text('domain', $site->domain) !!}
                    {!! Form::submit('Rename') !!}
                    {!! Form::close() !!}

                    {!! Form::open(['action' => ['SiteController@postInstallRepository', $site->server->id, $site->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('Repository') !!}
                        {!! Form::text('repository', $site->repository, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group checkbox">
                        <label>
                            {!! Form::hidden('zerotime_deployment', false) !!}
                            {!! Form::checkbox('zerotime_deployment', true, $site->zerotime_deployment) !!} Zerotime Deployment
                        </label>
                    </div>
                    <div class="form-group">
                        {!! Form::label('branch') !!}
                        {!! Form::text('branch', $site->branch, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        @foreach(\Auth::user()->userRepositoryProviders as $userRepositoryProvider)
                        <div class="radio">
                            <label>
                                {!! Form::radio('user_repository_provider_id', $userRepositoryProvider->id) !!}  $userRepositoryProvider->repositoryProvider->name
                            </label>
                        </div>
                        @endforeach
                    </div>
                    {!! Form::submit('Install Repository') !!}
                    {!! Form::close() !!}



                    <a href="#">Remove Repoisotry</a>


                    {!! Form::open(['action' => ['SiteController@postUpdateWebDirectory', $site->server->id, $site->id]]) !!}
                    {!! Form::text('web_directory', $site->web_directory) !!}
                    {!! Form::submit('Updated Web Directory') !!}
                    {!! Form::close() !!}

                    <a href="#" class="btn btn-primary">Deploy</a>

                    @if(empty($site->automatic_deployment_id))
                    <a href="#" class="btn btn-primary">Automatic Deployments</a>
                    @else
                    <a href="#" class="btn btn-primary">Stop Automatic Deployments</a>
                    @endif

                    <a href="#" class="btn btn-xs">Delete Site</a>
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