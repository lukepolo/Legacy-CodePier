<template>
    <section id="left" class="section-column">
        <h3 class="section-header">Sites</h3>

        <div class="section-content">
            <div class="site" v-for="site in sites">
                <router-link :to="{ path: '/site/'+site.id }">
                    <div class="site-name">
                        {{ site.name }}
                    </div>
                </router-link>
            </div>
            <form @submit.prevent="saveSite" v-if="adding_site">
                <template v-if="!form.domainless">
                    Domain
                </template>
                <template v-else>
                    Alias
                </template>

                <input v-model="form.domain" type="text">
                <input type="checkbox" v-model="form.domainless"> Not a domain
                <button class="btn btn-primary">Save</button>
            </form>

            <div class="btn-container text-center">
                <div @click="adding_site = !adding_site" class="btn btn-primary">Create Site</div>
            </div>

        </div>

    </section>
</template>

<script>
    export default {
        watch: {
            '$route' (to, from) {
                const toDepth = to.path.split('/').length
                const fromDepth = from.path.split('/').length
                this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
            }
        },
        created() {
            this.$store.dispatch('getSites');
        },
        data() {
            return {

                adding_site: false,
                form: {
                    domain: null,
                    domainless: false,
                }
            }
        },
        methods: {
            saveSite() {
                this.$store.dispatch('createSite', this.form);
                this.adding_site = false;
            }
        },
        computed: {
            sites() {
                return this.$store.state.sitesStore.sites;
            }
        }
    }
</script>