<template>
    <section id="left" class="section-column">
        <h3 class="section-header">Sites</h3>
        <div class="server selected" v-for="site in sites">
            <router-link :to="{ path: '/site/'+site.id }">
                <span class="server-connection server-success" data-toggle="tooltip" data-placement="top"
                      data-container="body" title="Site Health"></span> {{ site.name }}
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

        <div class="section-content">
            <div class="server text-center">
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
            }
        }
    }
</script>