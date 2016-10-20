<template>
    <section v-if="server">
        <h3 class="section-header primary">
            Server {{ server.name }} <small>{{ server.ip }}</small> -- (DISK SPACE?)

            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a @click.prevent="archiveServer(server.id)">Archive Server</a></li>
                    </ul>
                </div>
            </div>
            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-server"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a @click.prevent="restartServer(server.id)">Restart Server</a></li>
                        <li><a @click.prevent="restartServerWebServices(server.id)">Restart Web Services</a></li>
                        <li><a @click.prevent="restartServerDatabases(server.id)">Restart Databases</a></li>
                        <li><a @click.prevent="restartServerWorkers(server.id)">Restart Workers</a></li>
                    </ul>
                </div>
            </div>
            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-files-o"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Edit Nginx Config</a></li>
                        <li><a href="#">Edit PHP Config</a></li>
                    </ul>
                </div>
            </div>

        </h3>
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <router-link :to="{ path: '/server/'+server.id+'/sites' }" role="presentation" tag="li" ><a>Sites</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/ssh-keys' }" role="presentation" tag="li" ><a>SSH Keys</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/cron-jobs' }" role="presentation" tag="li" ><a>Cron Jobs</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/workers' }" role="presentation" tag="li" ><a>Workers</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/firewall-rules' }" role="presentation" tag="li" ><a>Firewall Rules</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/monitoring' }" role="presentation" tag="li" ><a>Monitoring</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/features' }" role="presentation" tag="li" ><a>Features</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/files' }" role="presentation" tag="li" ><a>Files</a></router-link>
        </ul>
    </section>
</template>

<script>
    export default {
        props : ['server'],
        methods : {
            restartServerWebServices : function(server_id) {
                this.$store.dispatch('restartServerWebServices', server_id);
            },
            restartServerDatabases : function(server_id) {
                this.$store.dispatch('restartServerDatabases', server_id);
            },
            restartServerWorkers : function(server_id) {
                this.$store.dispatch('restartServerWorkers', server_id);
            },
            restartServer : function(server_id) {
                this.$store.dispatch('restartServer', server_id);
            },
            archiveServer : function(server_id) {
                this.$store.dispatch('archiveServer', server_id);
            }
        }
    }
</script>