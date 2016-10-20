<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>
                    <form @submit.prevent="installLetsEncryptCertificate">
                        Domains
                        <input type="text" v-model="domains" name="domains">
                        <button type="submit">Install Let's Encrypt Certificate</button>
                    </form>

                    <p v-for="ssl_certificate in ssl_certificates">
                        {{ ssl_certificate.type }} : {{ ssl_certificate.domains }} : {{ ssl_certificate.cert_path }} :
                        {{ ssl_certificate.key_path }}
                        <a href="#" v-if="ssl_certificate.active">Deactivate</a>
                        <a href="#" v-else>Activate</a>
                        <a @click="deleteSslCertivicate(ssl_certificate.id)" href="#">Delete</a>
                    </p>
                </div>
            </div>
        </section>
        <servers></servers>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';
    export default {
        components: {
            SiteHeader,
            SiteNav,
            LeftNav,
            Servers
        },
        data() {
            return {
                domains: null,
            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getSslCertificates', this.$route.params.site_id);
            },
            installLetsEncryptCertificate() {
                this.$store.dispatch('installLetsEncryptSslCertificate', {
                    site_id: this.site.id,
                    domains: this.domains
                })
            },
            deleteSslCertivicate: function (ssl_certificate_id) {
                this.$store.dispatch('deleteSslCertificate', ssl_certificate_id)
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            ssl_certificates() {
                return this.$store.state.sitesStore.ssl_certificates;
            }
        }
    }
</script>