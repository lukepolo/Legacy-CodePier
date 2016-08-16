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
                    </div><!-- end group-container -->
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
                sites : [],
                current_pile_id : localStorage.getItem('current_pile_id')
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
            Vue.http.get(this.action('Pile\PileSitesController@show', {pile : this.current_pile_id})).then((response) => {
                this.sites = response.json();
            }, (errors) => {
                alert(error);
            });
        }
    }
</script>