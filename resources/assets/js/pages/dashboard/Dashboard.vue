<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Sites</h3>

            <div class="section-content">
                <div class="container">
                    <div class="group-container">
                        <site :site="site" :index="index" v-for="(site, index) in sites"></site>
                        <div class="group">
                            <a v-on:click="newsite()" class="add-site">
                                <div class="group-content">
                                    <span class="icon-layers"></span>
                                </div>

                                <div class="btn-footer text-center">
                                    <button class="btn btn-primary">Add site</button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import Site from './components/Site.vue';
    export default {
        components : {
            LeftNav,
            Site
        },
        data () {
            return {
                current_pile_id : pileStore.state.current_pile_id
            }
        },
        computed : {
            sites : () => {
                return siteStore.state.sites;
            }
        },
        methods : {
            newsite : function() {
                this.sites.push({
                    domain : 'New Domain',
                    web_directory : 'public',
                    wildcard_domain : true,
                    editing : true
                });
            }
        },
        mounted() {
            siteStore.dispatch('getSites');
        }
    }
</script>