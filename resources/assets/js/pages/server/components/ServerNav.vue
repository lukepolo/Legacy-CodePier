<template>
    <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
        <router-link :to="{ path: '/server/'+server_id+'/sites' }" role="presentation" tag="li" ><a>Sites</a></router-link>
        <router-link :to="{ path: '/server/'+server_id+'/ssh-keys' }" role="presentation" tag="li" ><a>SSH Keys</a></router-link>
        <router-link :to="{ path: '/server/'+server_id+'/cron-jobs' }" role="presentation" tag="li" ><a>Cron Jobs</a></router-link>
        <router-link :to="{ path: '/server/'+server_id+'/daemons' }" role="presentation" tag="li" ><a>Daemons</a></router-link>
        <router-link :to="{ path: '/server/'+server_id+'/firewall-rules' }" role="presentation" tag="li" ><a>Firewall Rules</a></router-link>
        <router-link :to="{ path: '/server/'+server_id+'/monitoring' }" role="presentation" tag="li" ><a>Monitoring</a></router-link>
        <router-link :to="{ path: '/server/'+server_id+'/files' }" role="presentation" tag="li" ><a>Files</a></router-link>
    </ul>
</template>

<script>
    export default {
        props : ['server_id']
    }
</script>