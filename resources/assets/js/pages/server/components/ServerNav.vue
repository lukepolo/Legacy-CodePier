<template>
    <section>
        <h3 class="section-header primary">
            Server {{ server.name }} <small>{{ server.ip }}</small> -- (DISK SPACE?)

            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-server"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Restart Web Services</a></li>
                        <li><a href="#">Restart Servers</a></li>
                        <li><a href="#">Restart Databases</a></li>
                        <li><a href="#">Restart Workers</a></li>
                    </ul>
                </div>
            </div>
            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-files-o"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
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
            <router-link :to="{ path: '/server/'+server.id+'/daemons' }" role="presentation" tag="li" ><a>Daemons</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/firewall-rules' }" role="presentation" tag="li" ><a>Firewall Rules</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/monitoring' }" role="presentation" tag="li" ><a>Monitoring</a></router-link>
        </ul>
    </section>
</template>

<script>
    export default {
        props : ['server']
    }
</script>