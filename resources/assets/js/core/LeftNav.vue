<template>
    <section id="left" class="section-column">
        <h3 class="section-header">Sites</h3>
        <div class="server selected" v-for="site in sites">
            <router-link :to="{ path: '/site/'+site.id }">
                {{ site.domain }}
            </router-link>
        </div>
        <div class="section-content">
            <div class="server text-center">
                <router-link to="/site/create" class="btn btn-primary">
                    Create Site
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        computed: {
            sites () {
                return siteStore.state.sites;
            }
        },
        created() {
            siteStore.dispatch('getSites');
        }
    }
</script>