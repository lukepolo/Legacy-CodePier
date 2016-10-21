<template>
    <section id="left" class="section-column">
        <h3 class="section-header">Sites</h3>

        <div class="section-content">
            <div class="site" v-for="site in sites">
                <router-link :to="{ name: 'site_repository', params : { site_id : site.id} }">
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

            <div class="btn-container text-center" v-if="current_pile_id">
                <div @click="adding_site = !adding_site" class="btn btn-primary">Create Site</div>
            </div>

        </div>

    </section>
</template>

<script>
    export default {
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
            },
            current_pile_id() {
                return this.$store.state.userStore.user.current_pile_id;
            }
        }
    }
</script>