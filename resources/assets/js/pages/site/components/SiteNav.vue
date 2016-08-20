<template>
    <section>
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <router-link :to="{ path : '/site/' + site.id}" tag="li" class="btn btn-primary">Repository</router-link>
            <router-link :to="{ path : '/site/' + site.id + '/environment'}" tag="li" class="btn btn-primary">Environment</router-link>
            <router-link :to="{ path : '/site/' + site.id + '/workers'}" tag="li" class="btn btn-primary">Workers</router-link>
            <router-link :to="{ path : '/site/' + site.id + '/ssl-certificates'}" tag="li" class="btn btn-primary">SSL Certificates</router-link>
            <router-link :to="{ path : '/site/' + site.id + '/php-settings'}" tag="li" class="btn btn-primary">PHP Common Settings</router-link>
            <router-link :to="{ path : '/site/' + site.id + '/files'}" tag="li" class="btn btn-primary">Edit Files</router-link>
        </ul>
    </section>
</template>

<script>
    export default {
        props : ['site'],
        methods : {
            deleteSite :function() {
                if(this.site.id) {
                    Vue.http.delete(this.action('Site\SiteController@destroy', { site : this.site.id })).then((response) => {
                        siteStore.dispatch('getSites');
                    }, (errors) => {
                        alert(error);
                    })
                }
            },
        }
    }
</script>