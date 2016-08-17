<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav :site="site"></site-nav>

                    Laravel Queue Workers
                    {!! Form::open(['action' => ['SiteController@postInstallWorker', $site->server->id, $site->id]]) !!}
                    Connection
                    {!! Form::text('connection', 'beanstalkd') !!}
                    Queue
                    {!! Form::text('queue', 'default') !!}
                    Maximum Seconds Per Job
                    {!! Form::text('timeout', '60') !!}
                    Time interval between jobs (when empty)
                    {!! Form::text('sleep', '10') !!}
                    Maximum Tries
                    {!! Form::text('tries', '3') !!}
                    Run As Daemon
                    {!! Form::hidden('daemon', false) !!}
                    {!! Form::checkbox('daemon', 'true') !!}
                    {!! Form::submit('Install Worker') !!}
                    {!! Form::close() !!}


                    <table class="table">
                        <thead>
                        <tr>
                            <th>Command</th>
                            <th>User</th>
                            <th>Auto Start</th>
                            <th>Auto Restart</th>
                            <th>Number of Workers</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($site->daemons as $daemon)
                        <tr>
                            <td>$daemon->command }}</td>
                            <td>$daemon->user }}</td>
                            <td>$daemon->auto_start }}</td>
                            <td>$daemon->auto_restart }}</td>
                            <td> $daemon->number_of_workers }}</td>
                            <td><a href="#" class="fa fa-remove"></a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

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