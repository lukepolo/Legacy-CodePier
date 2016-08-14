<template>
    <section>
        <h3 class="section-header primary">
            Server {{ server.name }} <small>{{ server.ip }}</small> -- (DISK SPACE?)
        </h3>
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <router-link :to="{ path: '/server/'+server.id+'/sites' }" role="presentation" tag="li" ><a>Sites</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/ssh-keys' }" role="presentation" tag="li" ><a>SSH Keys</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/cron-jobs' }" role="presentation" tag="li" ><a>Cron Jobs</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/daemons' }" role="presentation" tag="li" ><a>Daemons</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/firewall-rules' }" role="presentation" tag="li" ><a>Firewall Rules</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/monitoring' }" role="presentation" tag="li" ><a>Monitoring</a></router-link>
            <router-link :to="{ path: '/server/'+server.id+'/files' }" role="presentation" tag="li" ><a>Files</a></router-link>
        </ul>
    </section>
</template>

<script>
    export default {
        props : ['server']
    }
</script>