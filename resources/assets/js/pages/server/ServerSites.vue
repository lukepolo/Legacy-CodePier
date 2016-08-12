<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <template v-if="server">
                <div class="container">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Server {{ server.name }} <small>{{ server.ip }}</small>
                                    -- (DISK SPACE?)
                                </div>
                                <div class="panel-body">
                                    <server-nav></server-nav>
                                    <div id="my-tab-content" class="tab-content">
                                        <div class="tab-pane active" id="sites">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Domain</th>
                                                    <th>Repository</th>
                                                    <th>ZeroTime Deployment</th>
                                                    <th>Workers</th>
                                                    <th>WildCard Domain</th>
                                                    <th>SSL</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><a href="#">site</a></td>
                                                    <td>repository</td>
                                                    <td>zero time deployment</td>
                                                    <td>0</td>
                                                    <td>wildcard</td>
                                                    <td>has active ssl?</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            SITE FORM
                                        </div>
                                        <div class="tab-pane" id="ssh_keys">
                                            <form>
                                                <div class="form-group">
                                                    {!! Form::label('name') !!}
                                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="form-group">
                                                    <label>Public Key</label>
                                                    <textarea name="ssh_key"></textarea>
                                                </div>
                                                <button type="submit">Install SSH KEY</button>
                                            </form>
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Key Name</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>key name</td>
                                                    <td><a href=#" class="fa fa-remove"></a></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="cron_jobs">
                                            <span id="cron-preview"></span>
                                            <form>
                                                <input type="text" name="cron">
                                                <input type="hidden" name="cron_timing">
                                                <div id="cron-maker"></div>
                                                <button type="submit">Create Cron</button>
                                            </form>

                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Job</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>cron job</td>
                                                    <td><a href="#" class="fa fa-remove"></a></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="tab-pane" id="daemons">
                                            <form>
                                                Command
                                                <input type="text" name="command">
                                                User
                                                <input type="text" name="user">
                                                <input type="checkbox" name="auto_start"> Auto Start
                                                <input type="checkbox" name="auto_restart"> Auto Restart
                                                Workers
                                                <input type="integer" name="number_of_workers">

                                                <button type="submit">Create Cron</button>
                                            </form>

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
                                                <tr>
                                                    <td>command</td>
                                                    <td>user</td>
                                                    <td>auto_start</td>
                                                    <td>auto_restart</td>
                                                    <td>number_of_workers</td>
                                                    <td><a href="#" class="fa fa-remove"></a></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="firewall">
                                            <div class="row">
                                                Connect to :
                                                <form>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="servers[]"> server name - ip
                                                        </label>
                                                    </div>
                                                    <button type="submit">Connect to servers</button>
                                                </form>
                                            </div>
                                            <div class="row">
                                                <form>
                                                    description
                                                    <input type="text" name="description">
                                                    from ip
                                                    <input type="text" name="from_ip">
                                                    port
                                                    <input type="text" name="port">
                                                    <button type="submit">Add Firewall Rule</button>
                                                </form>

                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>From IP</th>
                                                        <th>Port</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>description</td>
                                                        <td>port</td>
                                                        <td>from ip</td>
                                                        <td><a href="#" class="fa fa-remove"></a></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="monitoring">
                                            <div class="btn">Integrate With Slack</div>
                                            <div class="btn">Integrate With HipChat</div>
                                            <form>
                                                server id
                                                <input type="text" name="server_id">
                                                server token
                                                <input type="text" name="server_token">
                                                <button type="submit">Install Blackfire</button>
                                            </form>

                                        </div>
                                        <div class="tab-pane" id="edit-files">
                                            <div class="row">
                                                <form>
                                                    <!--<div data-url="" data-path="/etc/php/7.0/fpm/php.ini" class="editor">Loading . . . </div>-->
                                                    <button type="submit">Update PHP Config</button>
                                                </form>
                                                <form>
                                                    <div data-url="#" data-path="/etc/php/7.0/cli/php.ini" class="editor">Loading . . . </div>
                                                    <button type="submit">Update PHP CLI Config</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="btn btn-xs">Archive Server</a>

                            <a href="#" class="btn btn-xs">Restart Web Services</a>
                            <a href="#" class="btn btn-xs">Restart Server</a>
                            <a href="#" class="btn btn-xs">Restart Database</a>
                            <a href="#" class="btn btn-xs">Restart Workers</a>
                        </div>
                    </div>
                </div>
            </template>
        </section>
    </section>
</template>

<script>
    import ServerNav from './components/ServerNav.vue';
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components : {
            LeftNav,
            ServerNav
        },
        data() {
            return {
                server : null
            }
        },
        computed : {

        },
        methods : {

        },
        mounted() {
            Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                this.server = response.json();
            }, (errors) => {
                alert(error);
            });
        }
    }
</script>
